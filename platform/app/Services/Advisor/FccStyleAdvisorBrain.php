<?php

declare(strict_types=1);

namespace Avc\Services\Advisor;

final class FccStyleAdvisorBrain
{
    private FccBrainSnapshotLoader $snapshotLoader;
    private AdvisorRecommendationService $recommendationService;

    public function __construct(private array $config)
    {
        $this->snapshotLoader = new FccBrainSnapshotLoader($this->config);
        $this->recommendationService = new AdvisorRecommendationService($this->config);
    }

    public function welcomeMessage(string $language): string
    {
        $language = $this->normalizeLanguage($language);

        return match ($language) {
            'en' => 'Write what you want to improve, what you already use or what you are unsure about. I will first understand the situation, then suggest a sensible next step.',
            'sl' => 'Napiši, kaj želiš izboljšati, kaj že uporabljaš ali kje nisi prepričan. Najprej bom razumel situacijo, nato pa predlagal smiseln naslednji korak.',
            default => 'Napiši što želiš poboljšati, što već koristiš ili oko čega nisi siguran. Prvo ću razumjeti situaciju, pa predložiti smislen sljedeći korak.',
        };
    }

    public function buildReply(string $message, array $context = []): array
    {
        $language = $this->normalizeLanguage((string) ($context['language'] ?? 'hr'));
        $sourcePath = $this->normalizePath((string) ($context['source_path'] ?? '/'));
        $message = trim($message);
        $historyContext = $this->extractHistoryContext((array) ($context['history'] ?? []));
        $previousPayload = is_array($historyContext['last_assistant_payload'] ?? null)
            ? $historyContext['last_assistant_payload']
            : [];

        $intent = $this->detectIntent($message);
        $conditionMatches = $this->findConditionMatches($message, $language);
        $themeMatches = $this->findThemeMatches($message, $language);
        $directProductMatches = $this->findDirectProductMatches($message);
        $followUpMode = $this->detectFollowUpMode($message, $historyContext);
        $reusedContext = false;

        if (
            !empty($followUpMode['reuse_previous_context'])
            && $this->payloadHasAdvisorSignals($previousPayload)
            && $conditionMatches === []
            && $directProductMatches === []
        ) {
            $previousConditionMatches = $this->filterRows((array) ($previousPayload['condition_matches'] ?? []));
            $previousThemeMatches = $this->filterRows((array) ($previousPayload['theme_matches'] ?? []));
            $conditionMatches = $previousConditionMatches;
            $themeMatches = $this->mergeRowsByKey($previousThemeMatches, $themeMatches);
            $directProductMatches = $this->filterDirectProductMatches((array) ($previousPayload['direct_product_matches'] ?? []));
            $intent = $this->mergeIntent((array) ($previousPayload['intent'] ?? []), $intent);
            $reusedContext = true;
        }

        $priorityProducts = $this->priorityProductsFromConditions($conditionMatches);
        $priorityProducts = array_values(array_unique(array_merge(
            $priorityProducts,
            $this->stackTitlesFromPayload($previousPayload)
        )));
        $preferredPatterns = $this->preferredPatternsFromConditions($conditionMatches);
        $themePatterns = $this->themePatternsFromMatches($themeMatches);
        $primaryCondition = $conditionMatches[0] ?? [];
        $lockProductScope = (bool) ($primaryCondition['lock_product_scope'] ?? false);
        $needsClarification = $this->needsClarification($message, $conditionMatches, $themeMatches, $directProductMatches);
        $questionLines = $this->buildClarificationQuestions(
            $language,
            $intent,
            $needsClarification,
            $directProductMatches !== [] && !$reusedContext
        );

        $recommendations = $this->recommendationService->recommend($message, $language, $sourcePath, [
            'priority_products' => $priorityProducts,
            'preferred_patterns' => $preferredPatterns,
            'theme_patterns' => $themePatterns,
            'lock_product_scope' => $lockProductScope,
            'limit_products' => $directProductMatches !== [] && $conditionMatches === [] ? 1 : min(3, max(1, count($priorityProducts) ?: 3)),
            'suppress_articles' => $directProductMatches !== [] && $conditionMatches === [],
            'require_article_term_overlap' => $conditionMatches !== [] || $directProductMatches !== [],
        ]);
        $recommendations = $this->preferHistoryRecommendations($recommendations, $previousPayload, $sourcePath);
        if ($reusedContext && (string) ($followUpMode['mode'] ?? '') !== '') {
            $recommendations = $this->stabilizeFollowUpRecommendations($recommendations, $previousPayload, $sourcePath);
        }

        if ($needsClarification && $conditionMatches === [] && $themeMatches === [] && $directProductMatches === []) {
            $recommendations = [
                'products' => [],
                'articles' => [],
                'top_route_path' => $sourcePath,
            ];
        }

        $primaryProduct = trim((string) ($primaryCondition['primary_product'] ?? ''));
        $supportProducts = array_values(array_filter(array_map(
            static fn (mixed $item): string => trim((string) $item),
            (array) ($primaryCondition['support_products'] ?? [])
        )));

        if (!$needsClarification && $primaryProduct === '' && !empty($recommendations['products'][0]['title'])) {
            $primaryProduct = trim((string) $recommendations['products'][0]['title']);
            $supportProducts = array_values(array_filter(array_map(
                static fn (array $row): string => trim((string) ($row['title'] ?? '')),
                array_slice((array) ($recommendations['products'] ?? []), 1, 2)
            )));
        }

        $openingNote = trim((string) ($primaryCondition['opening_note'] ?? ''));
        if ($openingNote === '' && !empty($themeMatches[0]['intro'])) {
            $openingNote = trim((string) ($themeMatches[0]['intro'] ?? ''));
        }

        if ($openingNote === '') {
            $openingNote = $this->defaultOpeningNote($language, $intent, $needsClarification);
        }

        $recommendationLines = $this->buildRecommendationLines(
            $language,
            $primaryCondition,
            $recommendations,
            $directProductMatches
        );

        $monthlyQuantityNote = trim((string) ($primaryCondition['monthly_quantity_note'] ?? ''));
        $leadCapture = $this->buildLeadCapturePayload($language, $intent);
        $followUpOverrides = $this->buildFollowUpOverrides($language, $intent, $followUpMode, $previousPayload, [
            'primary_product' => $primaryProduct,
            'support_products' => $supportProducts,
            'recommendation_lines' => $recommendationLines,
            'monthly_quantity_note' => $monthlyQuantityNote,
            'recommendations' => $recommendations,
        ]);

        if ($followUpOverrides !== []) {
            $openingNote = trim((string) ($followUpOverrides['opening_note'] ?? $openingNote));
            $recommendationLines = $this->filterStrings((array) ($followUpOverrides['recommendation_lines'] ?? $recommendationLines));
            $questionLines = $this->filterStrings((array) ($followUpOverrides['question_lines'] ?? $questionLines));
            $monthlyQuantityNote = trim((string) ($followUpOverrides['monthly_quantity_note'] ?? $monthlyQuantityNote));
        }

        $payload = [
            'language' => $language,
            'intent' => $intent,
            'follow_up_mode' => (string) ($followUpMode['mode'] ?? ''),
            'context_reused' => $reusedContext,
            'opening_note' => $openingNote,
            'recommendation_lines' => $recommendationLines,
            'question_lines' => $questionLines,
            'needs_clarification' => $questionLines !== [],
            'primary_product' => $primaryProduct,
            'support_products' => $supportProducts,
            'monthly_quantity_note' => $monthlyQuantityNote,
            'condition_matches' => $conditionMatches,
            'theme_matches' => $themeMatches,
            'direct_product_matches' => $directProductMatches,
            'recommendations' => $recommendations,
            'lead_capture' => $leadCapture,
        ];

        return [
            'body' => $this->renderReplyBody($payload, $intent),
            'payload' => $payload,
        ];
    }

    private function extractHistoryContext(array $history): array
    {
        $lastUserMessage = '';
        $lastAssistantBody = '';
        $lastAssistantPayload = [];
        $lastAdvisorPayload = [];

        foreach (array_reverse($history) as $message) {
            if (!is_array($message)) {
                continue;
            }

            $role = trim((string) ($message['role'] ?? ''));
            if ($lastUserMessage === '' && $role === 'user') {
                $lastUserMessage = trim((string) ($message['body_text'] ?? $message['body'] ?? ''));
            }

            if ($role !== 'assistant') {
                continue;
            }

            if ($lastAssistantBody === '') {
                $lastAssistantBody = trim((string) ($message['body_text'] ?? $message['body'] ?? ''));
            }

            $payload = is_array($message['payload'] ?? null) ? $message['payload'] : [];
            if ($payload === []) {
                continue;
            }

            if ($lastAssistantPayload === []) {
                $lastAssistantPayload = $payload;
            }

            if ($lastAdvisorPayload === [] && $this->payloadHasAdvisorSignals($payload)) {
                $lastAdvisorPayload = $payload;
            }

            if ($lastUserMessage !== '' && $lastAdvisorPayload !== []) {
                break;
            }
        }

        return [
            'last_user_message' => $lastUserMessage,
            'last_assistant_body' => $lastAssistantBody,
            'last_assistant_payload' => $lastAdvisorPayload !== [] ? $lastAdvisorPayload : $lastAssistantPayload,
        ];
    }

    private function payloadHasAdvisorSignals(array $payload): bool
    {
        return !empty($payload['primary_product'])
            || !empty($payload['support_products'])
            || !empty($payload['condition_matches'])
            || !empty($payload['theme_matches'])
            || !empty($payload['direct_product_matches'])
            || !empty($payload['recommendation_lines'])
            || !empty($payload['monthly_quantity_note'])
            || !empty($payload['recommendations']['products'])
            || !empty($payload['recommendations']['articles']);
    }

    private function detectFollowUpMode(string $message, array $historyContext): array
    {
        $normalized = $this->normalizeText($message);
        $hasHistorySignals = $this->payloadHasAdvisorSignals(
            is_array($historyContext['last_assistant_payload'] ?? null) ? $historyContext['last_assistant_payload'] : []
        );

        if ($normalized === '' || !$hasHistorySignals) {
            return [
                'mode' => '',
                'is_follow_up' => false,
                'reuse_previous_context' => false,
            ];
        }

        $mode = '';

        if ($this->containsAny($normalized, [
            'kako to piti', 'kako piti', 'kako se pije', 'kako uzimati', 'kako to uzimati',
            'kako ga uzimati', 'kako se uzima', 'kako koristiti', 'kako to koristiti',
            'kako ga koristiti', 'kako da pijem', 'kako da ga uzimam', 'how to take',
            'how do i take', 'how should i take that', 'how to use',
        ])) {
            $mode = 'usage';
        } elseif ($this->containsAny($normalized, [
            'koliko za mjesec dana', 'koliko za mesec dana', 'koliko za 30 dana',
            'koliko trebam za mjesec', 'koliko trebam za mesec', 'koliko kutija', 'koliko komada',
            'koliko pakiranja', 'koliko pakovanja', 'koliko boca', 'koliko boce', 'monthly',
        ])) {
            $mode = 'quantity';
        } elseif ($this->containsAny($normalized, [
            'koliko dugo', 'koliko vremena', 'dokad', 'how long', 'for how long',
        ])) {
            $mode = 'duration';
        } elseif ($this->containsAny($normalized, [
            'sto uz to', 'sta uz to', 'sto jos', 'sta jos', 'a uz to', 'what else with that',
            'what goes with that', 'what else should i add',
        ])) {
            $mode = 'additive';
        } elseif ($this->containsAny($normalized, [
            'detaljnije', 'objasni', 'pojasni', 'razradi', 'vise detalja', 'more detail',
            'tell me more', 'break it down',
        ])) {
            $mode = 'detail';
        } elseif ($this->containsAny($normalized, [
            'sto preporucujes', 'sta preporucujes', 'what do you recommend', 'what do you suggest',
            'koji smjer', 'which direction', 'sto onda', 'sta onda', 'what now', 'i sto sad', 'i sta sad',
        ])) {
            $mode = 'general';
        }

        return [
            'mode' => $mode,
            'is_follow_up' => $mode !== '',
            'reuse_previous_context' => $mode !== '',
        ];
    }

    private function mergeIntent(array $previousIntent, array $currentIntent): array
    {
        $keys = array_values(array_unique(array_merge(array_keys($previousIntent), array_keys($currentIntent))));
        $merged = [];

        foreach ($keys as $key) {
            $merged[$key] = !empty($currentIntent[$key]) || !empty($previousIntent[$key]);
        }

        return $merged;
    }

    private function preferHistoryRecommendations(array $recommendations, array $previousPayload, string $sourcePath): array
    {
        $previousRecommendations = is_array($previousPayload['recommendations'] ?? null)
            ? $previousPayload['recommendations']
            : [];

        if ($previousRecommendations === []) {
            return $recommendations;
        }

        if (empty($recommendations['products']) && !empty($previousRecommendations['products'])) {
            $recommendations['products'] = $this->filterRows((array) ($previousRecommendations['products'] ?? []));
        }

        if (empty($recommendations['articles']) && !empty($previousRecommendations['articles'])) {
            $recommendations['articles'] = $this->filterRows((array) ($previousRecommendations['articles'] ?? []));
        }

        if (
            trim((string) ($recommendations['top_route_path'] ?? $sourcePath)) === $sourcePath
            && !empty($previousRecommendations['top_route_path'])
        ) {
            $recommendations['top_route_path'] = $this->normalizePath((string) $previousRecommendations['top_route_path']);
        }

        return $recommendations;
    }

    private function stabilizeFollowUpRecommendations(array $recommendations, array $previousPayload, string $sourcePath): array
    {
        $previousRecommendations = is_array($previousPayload['recommendations'] ?? null)
            ? $previousPayload['recommendations']
            : [];

        if ($previousRecommendations === []) {
            return $recommendations;
        }

        $recommendations['products'] = $this->filterRows((array) ($previousRecommendations['products'] ?? []));
        $recommendations['articles'] = $this->filterRows((array) ($previousRecommendations['articles'] ?? []));
        $recommendations['top_route_path'] = !empty($previousRecommendations['top_route_path'])
            ? $this->normalizePath((string) $previousRecommendations['top_route_path'])
            : $sourcePath;

        return $recommendations;
    }

    private function buildFollowUpOverrides(
        string $language,
        array $intent,
        array $followUpMode,
        array $previousPayload,
        array $state
    ): array {
        $mode = (string) ($followUpMode['mode'] ?? '');
        if ($mode === '' || !$this->payloadHasAdvisorSignals($previousPayload)) {
            return [];
        }

        $primaryProduct = trim((string) ($state['primary_product'] ?? ''));
        $supportProducts = $this->filterStrings((array) ($state['support_products'] ?? []));
        $recommendationLines = $this->filterStrings((array) ($state['recommendation_lines'] ?? []));
        $monthlyQuantityNote = trim((string) ($state['monthly_quantity_note'] ?? ''));
        $recommendations = is_array($state['recommendations'] ?? null) ? $state['recommendations'] : [];

        return match ($mode) {
            'usage' => [
                'opening_note' => match ($language) {
                    'en' => 'If we stay on this same direction, I would keep the daily routine simple and consistent like this.',
                    'sl' => 'Če ostanemo na isti smeri, bi dnevno rutino ohranil preprosto in dosledno takole.',
                    default => 'Ako ostajemo na ovom istom smjeru, dnevnu rutinu bih držao jednostavnom i dosljednom ovako.',
                },
                'recommendation_lines' => $this->buildUsageRoutineLines($language, $primaryProduct, $supportProducts, $recommendations),
                'question_lines' => [],
                'monthly_quantity_note' => '',
            ],
            'quantity' => [
                'opening_note' => match ($language) {
                    'en' => 'If you want a one-month frame, I would keep the same base first and adjust only after you see how it fits your routine.',
                    'sl' => 'Če želiš okvir za en mesec, bi najprej obdržal isto osnovo in prilagodil šele, ko vidiš, kako se ujame z rutino.',
                    default => 'Ako želiš okvir za mjesec dana, prvo bih zadržao istu bazu pa prilagodio tek kad vidiš kako ti sjeda u rutinu.',
                },
                'recommendation_lines' => $recommendationLines,
                'question_lines' => [],
                'monthly_quantity_note' => $monthlyQuantityNote !== ''
                    ? $monthlyQuantityNote
                    : $this->fallbackMonthlyQuantityNote($language, $primaryProduct, $supportProducts),
            ],
            'duration' => [
                'opening_note' => match ($language) {
                    'en' => 'The fairest way to judge this direction is not after a day or two, but after a steady routine window.',
                    'sl' => 'Najbolj pošten način za oceno te smeri ni po dnevu ali dveh, ampak po stabilnem obdobju rutine.',
                    default => 'Najpošteniji način da procijeniš ovaj smjer nije nakon dan-dva, nego nakon urednog razdoblja rutine.',
                },
                'recommendation_lines' => $this->buildDurationLines($language, $intent, $primaryProduct, $supportProducts),
                'question_lines' => [],
                'monthly_quantity_note' => $monthlyQuantityNote,
            ],
            'additive' => [
                'opening_note' => match ($language) {
                    'en' => 'If we stay on the same main direction, I would add support only where it has a clear role.',
                    'sl' => 'Če ostanemo na isti glavni smeri, bi podporo dodal le tam, kjer ima jasno vlogo.',
                    default => 'Ako ostajemo na istom glavnom smjeru, dodao bih samo podršku koja ima jasnu ulogu.',
                },
                'recommendation_lines' => $this->buildAdditiveLines($language, $primaryProduct, $supportProducts, $recommendationLines),
                'question_lines' => [],
                'monthly_quantity_note' => $monthlyQuantityNote,
            ],
            'detail' => [
                'opening_note' => match ($language) {
                    'en' => 'If we keep the same recommendation lane, I would break it down like this.',
                    'sl' => 'Če ostanemo v isti priporočilni smeri, bi to razčlenil takole.',
                    default => 'Ako ostajemo u istoj preporuci, razradio bih je ovako.',
                },
                'recommendation_lines' => $recommendationLines,
                'question_lines' => [],
                'monthly_quantity_note' => $monthlyQuantityNote,
            ],
            default => [],
        };
    }

    private function buildUsageRoutineLines(
        string $language,
        string $primaryProduct,
        array $supportProducts,
        array $recommendations
    ): array {
        $lines = [];
        $stackTitles = array_values(array_unique(array_merge(
            $primaryProduct !== '' ? [$primaryProduct] : [],
            $supportProducts,
            $this->stackTitlesFromRecommendations($recommendations)
        )));

        foreach (array_slice($stackTitles, 0, 3) as $title) {
            $line = $this->usageLineForProduct($title, $language, $title === $primaryProduct);
            if ($line !== '') {
                $lines[] = $line;
            }
        }

        if ($lines !== []) {
            return $lines;
        }

        return match ($language) {
            'en' => [
                'Keep the main product as the steady daily base, and add support products only if they make the routine easier to follow.',
                'The easiest rhythm is consistency first, then adjustments only if your real goal becomes clearer.',
            ],
            'sl' => [
                'Glavni izdelek naj ostane stabilna dnevna osnova, podporne izdelke pa dodaj le, če rutino naredijo lažjo za spremljanje.',
                'Najlažji ritem je najprej doslednost, nato prilagoditve šele, ko je pravi cilj bolj jasen.',
            ],
            default => [
                'Glavni proizvod neka ostane stabilna dnevna baza, a dodatne proizvode dodaj samo ako rutinu čine lakšom za pratiti.',
                'Najjednostavniji ritam je prvo dosljednost, a prilagodbe tek kad je pravi cilj još jasniji.',
            ],
        };
    }

    private function usageLineForProduct(string $title, string $language, bool $isPrimary): string
    {
        $normalizedTitle = $this->normalizeText($title);
        $supportWord = $isPrimary
            ? match ($language) {
                'en' => 'main',
                'sl' => 'glavna',
                default => 'glavna',
            }
            : match ($language) {
                'en' => 'support',
                'sl' => 'podporna',
                default => 'support',
            };

        if (
            str_contains($normalizedTitle, 'aloe vera gel')
            || str_contains($normalizedTitle, 'aloe berry nectar')
            || str_contains($normalizedTitle, 'aloe peaches')
            || str_contains($normalizedTitle, 'aloe bits')
        ) {
            return match ($language) {
                'en' => $title . ' works best as the ' . $supportWord . ' drink base, split through the day instead of pushed all at once.',
                'sl' => $title . ' najlepše deluje kot ' . $supportWord . ' pitna osnova, razporejena čez dan namesto naenkrat.',
                default => $title . ' najbolje radi kao ' . $supportWord . ' baza za piće, raspoređena kroz dan umjesto odjednom.',
            };
        }

        if (str_contains($normalizedTitle, 'arctic sea')) {
            return match ($language) {
                'en' => $title . ' fits best with a main meal, as the omega-3 support part of the routine.',
                'sl' => $title . ' najlepše drži ritem ob glavnem obroku kot omega-3 podporni del kombinacije.',
                default => $title . ' najbolje drži ritam uz glavni obrok kao omega-3 dodatak kombinaciji.',
            };
        }

        if (str_contains($normalizedTitle, 'garlic-thyme') || str_contains($normalizedTitle, 'garlic thyme')) {
            return match ($language) {
                'en' => $title . ' fits as an add-on with food, instead of carrying the whole routine on its own.',
                'sl' => $title . ' se dobro prilega kot dodatek ob hrani, namesto da postane cela rutina sama zase.',
                default => $title . ' bolje stoji kao dodatak uz obrok, a ne da sam nosi cijelu rutinu.',
            };
        }

        if (str_contains($normalizedTitle, 'active pro b')) {
            return match ($language) {
                'en' => $title . ' is easiest to keep as one steady daily probiotic anchor inside the same routine.',
                'sl' => $title . ' je najlažje držati kot en stalen dnevni probiotični sidrni del iste rutine.',
                default => $title . ' je najlakše držati kao jednu stabilnu dnevnu probiotičku bazu unutar iste rutine.',
            };
        }

        if (str_contains($normalizedTitle, 'fiber')) {
            return match ($language) {
                'en' => $title . ' works best with enough water and a calm daily rhythm, not rushed on top of everything else.',
                'sl' => $title . ' najlepše deluje z dovolj vode in mirnim dnevnim ritmom, ne nagneten na vse ostalo.',
                default => $title . ' najbolje radi uz dovoljno vode i miran dnevni ritam, a ne natrpan preko svega ostalog.',
            };
        }

        if (str_contains($normalizedTitle, 'fields of greens')) {
            return match ($language) {
                'en' => $title . ' fits best as a simple green support layer earlier in the day.',
                'sl' => $title . ' se najbolje uklopi kot preprost zeleni podporni sloj prej v dnevu.',
                default => $title . ' se najbolje uklapa kao jednostavan zeleni dodatak ranije kroz dan.',
            };
        }

        if (str_contains($normalizedTitle, 'absorbent-d') || str_contains($normalizedTitle, 'absorbent d')) {
            return match ($language) {
                'en' => $title . ' is easiest as a steady daily vitamin D routine, ideally tied to a regular meal.',
                'sl' => $title . ' je najlažji kot stalna dnevna rutina vitamina D, idealno vezana na reden obrok.',
                default => $title . ' je najlakši kao stabilna dnevna rutina vitamina D, idealno vezana uz redovit obrok.',
            };
        }

        if (str_contains($normalizedTitle, 'absorbent-c') || str_contains($normalizedTitle, 'absorbent c')) {
            return match ($language) {
                'en' => $title . ' is easiest to keep as a regular daily vitamin C layer instead of occasional catch-up use.',
                'sl' => $title . ' je najlažje držati kot reden dnevni sloj vitamina C, umesto občasnega nadomeščanja.',
                default => $title . ' je najlakše držati kao redoviti dnevni sloj vitamina C, a ne povremeno nadoknađivanje.',
            };
        }

        if (str_contains($normalizedTitle, 'royal jelly')) {
            return match ($language) {
                'en' => $title . ' makes the most sense earlier in the day as a simple daily support layer.',
                'sl' => $title . ' ima največ smisla prej v dnevu kot preprost dnevni podporni sloj.',
                default => $title . ' se najlakše uklapa ranije kroz dan kao jednostavan dnevni dodatak.',
            };
        }

        if (str_contains($normalizedTitle, 'multi maca')) {
            return match ($language) {
                'en' => $title . ' fits best earlier in the day, when you want the routine to stay simple and energizing.',
                'sl' => $title . ' se najbolje uklopi prej v dnevu, ko želiš, da rutina ostane preprosta in energijska.',
                default => $title . ' se najbolje uklapa ranije kroz dan kada želiš da rutina ostane jednostavna i energijska.',
            };
        }

        if (str_contains($normalizedTitle, 'therm') || str_contains($normalizedTitle, 'garcinia')) {
            return match ($language) {
                'en' => $title . ' usually fits earlier in the day, so the routine does not become too heavy later on.',
                'sl' => $title . ' se običajno bolje prilega prej v dnevu, da rutina kasneje ne postane pretežka.',
                default => $title . ' se obično bolje uklapa ranije kroz dan kako rutina kasnije ne bi postala preteška.',
            };
        }

        if (str_contains($normalizedTitle, 'aloe first') || str_contains($normalizedTitle, 'msm gelly') || str_contains($normalizedTitle, 'gelly')) {
            return match ($language) {
                'en' => $title . ' belongs more to a local outer-use routine than to the inner drink combination.',
                'sl' => $title . ' bolj spada v lokalno zunanjo rutino kot v notranjo pitno kombinacijo.',
                default => $title . ' više pripada lokalnoj vanjskoj rutini nego unutarnjoj kombinaciji za piti.',
            };
        }

        return match ($language) {
            'en' => $title . ' is best kept in one steady daily slot, so the whole combination stays easy to follow.',
            'sl' => $title . ' je najbolje držati v enem stabilnem dnevnem terminu, da celotna kombinacija ostane preprosta za spremljanje.',
            default => $title . ' je najbolje držati u jednom stabilnom dnevnom terminu kako bi cijela kombinacija ostala jednostavna za pratiti.',
        };
    }

    private function buildDurationLines(string $language, array $intent, string $primaryProduct, array $supportProducts): array
    {
        $lines = [
            match ($language) {
                'en' => 'The first useful checkpoint is usually one steady month on the same base before changing too many things.',
                'sl' => 'Prvi koristen kontrolni trenutek je običajno en stabilen mesec na isti osnovi, preden spreminjaš preveč stvari.',
                default => 'Prvi koristan check-in je obično jedan uredan mjesec na istoj bazi prije nego mijenjaš previše stvari.',
            },
        ];

        if ($primaryProduct !== '') {
            $lines[] = match ($language) {
                'en' => $primaryProduct . ' should stay the main base in that window, while support products stay secondary unless there is a clear reason to expand.',
                'sl' => $primaryProduct . ' naj ostane glavna osnova v tem oknu, podporni izdelki pa naj ostanejo sekundarni, osim če postoji jasan razlog za širitev.',
                default => $primaryProduct . ' neka ostane glavna baza u tom prozoru, a dodatni proizvodi neka budu sekundarni osim ako postoji jasan razlog za širenje.',
            };
        }

        if (!empty($intent['medical_sensitive']) || !empty($intent['serious']) || !empty($intent['special_population_sensitive'])) {
            $lines[] = match ($language) {
                'en' => 'If this sits next to therapy, pregnancy, breastfeeding or a more sensitive condition, align the duration with a doctor first.',
                'sl' => 'Če je to povezano s terapijo, nosečnostjo, dojenjem ali bolj občutljivim stanjem, trajanje najprej uskladi z zdravnikom.',
                default => 'Ako je ovo uz terapiju, trudnoću, dojenje ili osjetljivije stanje, trajanje prvo uskladi s liječnikom.',
            };
        } elseif ($supportProducts !== []) {
            $lines[] = match ($language) {
                'en' => 'After that window, you can reassess whether the support layer still earns its place or whether it is better to simplify.',
                'sl' => 'Po tem obdobju lahko ponovno oceniš, ali podporni sloj še vedno ima smisla ali je bolje poenostaviti.',
                default => 'Nakon tog razdoblja možeš ponovno procijeniti ima li dodatna podrška još smisla ili je bolje pojednostaviti.',
            };
        }

        return $lines;
    }

    private function buildAdditiveLines(string $language, string $primaryProduct, array $supportProducts, array $recommendationLines): array
    {
        $supportLines = array_slice($recommendationLines, 1);
        if ($supportLines !== []) {
            return $supportLines;
        }

        if ($supportProducts === []) {
            return $recommendationLines;
        }

        $lines = [];
        foreach (array_slice($supportProducts, 0, 2) as $supportProduct) {
            $lines[] = match ($language) {
                'en' => $supportProduct . ' makes the most sense only as a support layer next to ' . ($primaryProduct !== '' ? $primaryProduct : 'the main direction') . '.',
                'sl' => $supportProduct . ' ima največ smisla le kot podporni sloj ob ' . ($primaryProduct !== '' ? $primaryProduct : 'glavni smeri') . '.',
                default => $supportProduct . ' ostaje dodatna podrška uz ' . ($primaryProduct !== '' ? $primaryProduct : 'glavni smjer') . ', ne glavna baza rutine.',
            };
        }

        return $lines;
    }

    private function fallbackMonthlyQuantityNote(string $language, string $primaryProduct, array $supportProducts): string
    {
        $stack = array_values(array_unique(array_merge(
            $primaryProduct !== '' ? [$primaryProduct] : [],
            $supportProducts
        )));

        if ($stack === []) {
            return match ($language) {
                'en' => 'For a one-month frame, keep the same base first and only then decide whether anything needs to be added.',
                'sl' => 'Za enomesečni okvir najprej obdrži isto osnovo, šele nato odloči, ali je treba kaj dodati.',
                default => 'Za okvir od mjesec dana prvo zadrži istu bazu, pa tek onda odluči treba li išta dodavati.',
            };
        }

        $stackText = implode(', ', $stack);

        return match ($language) {
            'en' => 'For a one-month frame, I would keep the same combination first: ' . $stackText . '.',
            'sl' => 'Za enomesečni okvir bi najprej obdržal isto kombinacijo: ' . $stackText . '.',
            default => 'Za okvir od mjesec dana prvo bih zadržao istu kombinaciju: ' . $stackText . '.',
        };
    }

    private function stackTitlesFromPayload(array $payload): array
    {
        $titles = [];
        $primaryProduct = trim((string) ($payload['primary_product'] ?? ''));
        if ($primaryProduct !== '') {
            $titles[] = $primaryProduct;
        }

        foreach ((array) ($payload['support_products'] ?? []) as $supportProduct) {
            $supportProduct = trim((string) $supportProduct);
            if ($supportProduct !== '') {
                $titles[] = $supportProduct;
            }
        }

        return array_values(array_unique($titles));
    }

    private function stackTitlesFromRecommendations(array $recommendations): array
    {
        $titles = [];

        foreach ((array) ($recommendations['products'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }

            $title = trim((string) ($row['title'] ?? ''));
            if ($title !== '') {
                $titles[] = $title;
            }
        }

        return array_values(array_unique($titles));
    }

    private function filterRows(array $rows): array
    {
        return array_values(array_filter($rows, static fn (mixed $row): bool => is_array($row)));
    }

    private function mergeRowsByKey(array $preferredRows, array $currentRows): array
    {
        $merged = [];
        $seen = [];

        foreach (array_merge($preferredRows, $currentRows) as $row) {
            if (!is_array($row)) {
                continue;
            }

            $key = trim((string) ($row['key'] ?? $row['label'] ?? ''));
            if ($key === '') {
                $key = md5(json_encode($row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '');
            }

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $merged[] = $row;
        }

        return $merged;
    }

    private function filterDirectProductMatches(array $rows): array
    {
        $filtered = [];

        foreach ($rows as $key => $patterns) {
            if (!is_array($patterns)) {
                continue;
            }

            $values = $this->filterStrings($patterns);
            if ($values !== []) {
                $filtered[(string) $key] = $values;
            }
        }

        return $filtered;
    }

    private function filterStrings(array $values): array
    {
        return array_values(array_filter(array_map(
            static fn (mixed $value): string => trim((string) $value),
            $values
        )));
    }

    private function detectIntent(string $message): array
    {
        $normalized = $this->normalizeText($message);

        return [
            'contact' => $this->containsAny($normalized, ['kontakt', 'kontaktir', 'nazovi', 'whatsapp', 'viber', 'email me', 'contact me', 'javi se']),
            'discount' => $this->containsAny($normalized, ['popust', 'discount', '15%', 'cijena', 'price']),
            'special_population_sensitive' => $this->containsAny($normalized, ['trudn', 'dojen', 'laktacij', 'dijete', 'dječ', 'djec', 'baby', 'child', 'otrok']),
            'medical_sensitive' => $this->containsAny($normalized, [
                'dijabet', 'inzulin', 'insulin', 'rak', 'cancer', 'kemoterap', 'transplant', 'tlak',
                'pressure', 'bubreg', 'kidney', 'jetr', 'liver', 'štitna', 'stitna', 'tromboz',
                'mozdani', 'moždani', 'karcinom', 'kolitis', 'pankreas', 'astma', 'astmat',
            ]),
            'serious' => $this->containsAny($normalized, [
                'rak', 'cancer', 'kemoterap', 'transplant', 'urgent', 'hitno', 'krv', 'krvarenje',
                'jaka bol', 'strong pain', 'dijagno', 'diagnos', 'hospital',
            ]),
        ];
    }

    private function findConditionMatches(string $message, string $language): array
    {
        $haystack = $this->normalizeText($message . ' ' . implode(' ', array_keys($this->findDirectProductMatches($message))));
        if ($haystack === '') {
            return [];
        }

        $matches = [];

        foreach ($this->snapshotLoader->productMatrix() as $key => $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $score = 0;
            $matchedPatterns = [];

            foreach ((array) ($entry['patterns'] ?? []) as $pattern) {
                $normalizedPattern = $this->normalizeText((string) $pattern);
                if ($normalizedPattern === '' || !str_contains($haystack, $normalizedPattern)) {
                    continue;
                }

                $score += strlen($normalizedPattern) >= 10 ? 40 : 22;
                $matchedPatterns[] = $normalizedPattern;
            }

            if ($score <= 0) {
                continue;
            }

            $matches[] = [
                'key' => (string) $key,
                'label' => $this->localizedText($entry, 'label', $language),
                'opening_note' => $this->localizedText($entry, 'opening_note', $language),
                'recommendation_lines' => $this->localizedLines($entry, 'recommendation_lines', $language),
                'primary_product' => trim((string) ($entry['primary_product'] ?? '')),
                'support_products' => array_values(array_filter(array_map(
                    static fn (mixed $item): string => trim((string) $item),
                    (array) ($entry['support_products'] ?? [])
                ))),
                'preferred_patterns' => array_values(array_filter(array_map(
                    fn (mixed $pattern): string => $this->normalizeText((string) $pattern),
                    (array) ($entry['preferred_patterns'] ?? [])
                ))),
                'monthly_quantity_note' => $this->localizedText($entry, 'monthly_quantity_note', $language),
                'lock_product_scope' => (bool) ($entry['lock_product_scope'] ?? false),
                'suppress_generic_questions' => (bool) ($entry['suppress_generic_questions'] ?? false),
                'sensitive_support_only' => (bool) ($entry['sensitive_support_only'] ?? false),
                'allow_special_population_support' => (bool) ($entry['allow_special_population_support'] ?? false),
                'score' => $score,
                'matched_patterns' => array_values(array_unique($matchedPatterns)),
            ];
        }

        usort($matches, static function (array $left, array $right): int {
            return ((int) ($right['score'] ?? 0) <=> (int) ($left['score'] ?? 0))
                ?: strcmp((string) ($left['key'] ?? ''), (string) ($right['key'] ?? ''));
        });

        return array_slice($matches, 0, 2);
    }

    private function findThemeMatches(string $message, string $language): array
    {
        $haystack = $this->normalizeText($message);
        if ($haystack === '') {
            return [];
        }

        $matches = [];

        foreach ($this->snapshotLoader->themeCatalog() as $key => $theme) {
            if (!is_array($theme)) {
                continue;
            }

            $score = 0;
            foreach ((array) ($theme['keywords'] ?? []) as $keyword) {
                $keyword = $this->normalizeText((string) $keyword);
                if ($keyword !== '' && str_contains($haystack, $keyword)) {
                    $score += 20;
                }
            }

            if ($score <= 0) {
                continue;
            }

            $matches[] = [
                'key' => (string) $key,
                'label' => $this->localizedText($theme, 'label', $language),
                'bioactive' => $this->localizedText($theme, 'bioactive', $language),
                'intro' => $this->localizedText($theme, 'intro', $language),
                'article_patterns' => array_values(array_filter(array_map(
                    fn (mixed $pattern): string => $this->normalizeText((string) $pattern),
                    (array) ($theme['article_patterns'] ?? [])
                ))),
                'score' => $score,
            ];
        }

        usort($matches, static function (array $left, array $right): int {
            return ((int) ($right['score'] ?? 0) <=> (int) ($left['score'] ?? 0))
                ?: strcmp((string) ($left['label'] ?? ''), (string) ($right['label'] ?? ''));
        });

        return array_slice($matches, 0, 2);
    }

    private function findDirectProductMatches(string $message): array
    {
        $normalized = $this->normalizeText($message);
        if ($normalized === '') {
            return [];
        }

        $matches = [];

        foreach ($this->snapshotLoader->directProductLookupCatalog() as $key => $patterns) {
            $matchedPatterns = [];
            foreach ((array) $patterns as $pattern) {
                $normalizedPattern = $this->normalizeText((string) $pattern);
                if ($normalizedPattern !== '' && str_contains($normalized, $normalizedPattern)) {
                    $matchedPatterns[] = $normalizedPattern;
                }
            }

            if ($matchedPatterns === []) {
                continue;
            }

            $matches[(string) $key] = array_values(array_unique($matchedPatterns));
        }

        return $matches;
    }

    private function needsClarification(string $message, array $conditionMatches, array $themeMatches, array $directProductMatches): bool
    {
        if ($conditionMatches !== [] || $themeMatches !== [] || $directProductMatches !== []) {
            return false;
        }

        return count($this->searchTokens($message)) <= 5;
    }

    private function buildClarificationQuestions(string $language, array $intent, bool $needsClarification, bool $isDirectProductLookup): array
    {
        if ($isDirectProductLookup) {
            return match ($language) {
                'en' => ['Do you want a quick explanation of what this product is usually chosen for, or a recommendation based on your goal?'],
                'sl' => ['Želiš hitro razlago, za kaj se ta izdelek običajno uporablja, ali priporočilo glede na tvoj cilj?'],
                default => ['Želiš kratko objašnjenje za što se ovaj proizvod najčešće bira ili preporuku prema tvom cilju?'],
            };
        }

        if (!$needsClarification) {
            return [];
        }

        if (!empty($intent['special_population_sensitive'])) {
            return match ($language) {
                'en' => ['Is this recommendation for you, a child, or a breastfeeding/pregnant person?'],
                'sl' => ['Je to priporočilo zate, za otroka ali za nosečo/doječo osebo?'],
                default => ['Je li preporuka za tebe, dijete ili trudnicu/dojilju?'],
            };
        }

        return match ($language) {
            'en' => [
                'What do you want to support most right now: digestion, energy, immunity, skin, mobility, or something else?',
                'Is the recommendation for you personally, or for someone else?',
            ],
            'sl' => [
                'Kaj želiš trenutno najbolj podpreti: prebavo, energijo, odpornost, kožo, gibljivost ali nekaj drugega?',
                'Je priporočilo zate osebno ali za nekoga drugega?',
            ],
            default => [
                'Što trenutno najviše želiš podržati: probavu, energiju, imunitet, kožu, pokretljivost ili nešto drugo?',
                'Je li preporuka za tebe osobno ili za nekog drugog?',
            ],
        };
    }

    private function buildRecommendationLines(string $language, array $primaryCondition, array $recommendations, array $directProductMatches): array
    {
        $lines = array_values(array_filter(array_map(
            static fn (mixed $line): string => trim((string) $line),
            (array) ($primaryCondition['recommendation_lines'] ?? [])
        )));

        if ($lines !== []) {
            return array_slice($lines, 0, 3);
        }

        if (!empty($recommendations['products'])) {
            $localizedPrefix = match ($language) {
                'en' => ['Main direction', 'Support direction', 'Optional extra'],
                'sl' => ['Glavna smer', 'Podporna smer', 'Dodatna možnost'],
                default => ['Glavni smjer', 'Dodatna podrška', 'Po želji još'],
            };

            $built = [];
            foreach (array_slice((array) $recommendations['products'], 0, 3) as $index => $row) {
                $label = $localizedPrefix[$index] ?? end($localizedPrefix);
                $summary = trim((string) ($row['summary'] ?? ''));
                $built[] = $label . ': ' . trim((string) ($row['title'] ?? '')) . ($summary !== '' ? ' — ' . $summary : '');
            }

            return $built;
        }

        if ($directProductMatches !== []) {
            $titles = array_keys($directProductMatches);

            return match ($language) {
                'en' => ['You asked about a specific Forever product, so it makes sense to first connect it with your real goal before adding anything else.'],
                'sl' => ['Sprašuješ po konkretnem Forever izdelku, zato ga je smiselno najprej povezati s tvojim resničnim ciljem, preden dodamo še kaj.'],
                default => ['Pitaš za konkretan Forever proizvod, pa ga je najkorisnije prvo povezati s tvojim stvarnim ciljem prije nego dodamo još nešto.'],
            };
        }

        return [];
    }

    private function buildLeadCapturePayload(string $language, array $intent): array
    {
        if (!empty($intent['contact']) || !empty($intent['medical_sensitive']) || !empty($intent['special_population_sensitive'])) {
            return match ($language) {
                'en' => [
                    'recommended' => true,
                    'headline' => 'Do you want personal follow-up?',
                    'text' => 'Leave your contact and AVC support can continue this recommendation personally.',
                ],
                'sl' => [
                    'recommended' => true,
                    'headline' => 'Želiš osebni nadaljnji stik?',
                    'text' => 'Pusti kontakt in AVC podpora lahko to priporočilo nadaljuje osebno.',
                ],
                default => [
                    'recommended' => true,
                    'headline' => 'Želiš osobni nastavak razgovora?',
                    'text' => 'Ostavi kontakt pa AVC podrška može osobno nastaviti ovu preporuku.',
                ],
            };
        }

        return [
            'recommended' => false,
            'headline' => '',
            'text' => '',
        ];
    }

    private function renderReplyBody(array $payload, array $intent): string
    {
        $language = (string) ($payload['language'] ?? 'hr');
        $parts = [];

        if (!empty($intent['special_population_sensitive'])) {
            $parts[] = match ($language) {
                'en' => 'For pregnancy, breastfeeding or children, check with a doctor or pediatrician before turning this into a product routine.',
                'sl' => 'Pri nosečnosti, dojenju ali otrocih je najbolje najprej preveriti pri zdravniku ali pediatru, preden to postane rutina izdelkov.',
                default => 'Kod trudnoće, dojenja i djece najbolje je prvo provjeriti s liječnikom ili pedijatrom prije nego to pretvoriš u rutinu proizvoda.',
            };
        } elseif (!empty($intent['medical_sensitive']) || !empty($intent['serious'])) {
            $parts[] = match ($language) {
                'en' => 'For this type of question, treat Forever products only as routine support and keep medical decisions with your doctor.',
                'sl' => 'Pri takem vprašanju naj Forever izdelki ostanejo le podpora rutini, medicinske odločitve pa prepusti zdravniku.',
                default => 'Kod ovakvog pitanja Forever proizvode gledaj samo kao podršku rutini, a medicinske odluke ostavi liječniku.',
            };
        }

        if (!empty($payload['opening_note'])) {
            $parts[] = $this->polishAdvisorText((string) $payload['opening_note'], $language);
        }

        if (!empty($payload['recommendation_lines'])) {
            $parts[] = implode("\n", array_map(
                fn (string $line): string => '- ' . $this->polishAdvisorText((string) preg_replace('/^[^:]+:\s*/u', '', $line), $language),
                (array) $payload['recommendation_lines']
            ));
        }

        if (!empty($payload['monthly_quantity_note']) && empty($intent['special_population_sensitive'])) {
            $parts[] = $this->polishAdvisorText((string) $payload['monthly_quantity_note'], $language);
        }

        if (!empty($payload['question_lines'])) {
            $intro = match ($language) {
                'en' => 'To avoid guessing, I need one or two quick details:',
                'sl' => 'Da ne ugibam, potrebujem eno ali dve kratki informaciji:',
                default => 'Da ne preporučim napamet, trebam još jednu ili dvije kratke informacije:',
            };
            $parts[] = $intro . "\n" . implode("\n", array_map(
                fn (string $line): string => '- ' . $this->polishAdvisorText($line, $language),
                (array) $payload['question_lines']
            ));
        } elseif (!empty($payload['recommendations']['products']) || !empty($payload['recommendations']['articles'])) {
            $parts[] = match ($language) {
                'en' => 'Below are the products and articles I would look at first.',
                'sl' => 'Spodaj so izdelki in članki, ki bi jih pogledal najprej.',
                default => 'Ispod su proizvodi i članci koje bih prvo pogledao.',
            };
        }

        return trim(implode("\n\n", array_filter($parts)));
    }

    private function polishAdvisorText(string $text, string $language): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        if ($language !== 'hr') {
            return $text;
        }

        $replacements = [
            '/\bAko želite\b/u' => 'Ako želiš',
            '/\bako želite\b/u' => 'ako želiš',
            '/\bkada želite\b/u' => 'kada želiš',
            '/\bKada želite\b/u' => 'Kada želiš',
            '/\bželite\b/u' => 'želiš',
            '/\bŽelite\b/u' => 'Želiš',
            '/\bmožete\b/u' => 'možeš',
            '/\bMožete\b/u' => 'Možeš',
            '/\bvam\b/u' => 'ti',
            '/\bVam\b/u' => 'Ti',
            '/\bsupport opcije\b/iu' => 'dodatne opcije',
            '/\bsupport opcija\b/iu' => 'dodatna opcija',
            '/\bsupport smjer\b/iu' => 'smjer podrške',
            '/\bsupport rutinu\b/iu' => 'rutinu podrške',
            '/\bsupport rutini\b/iu' => 'rutini podrške',
            '/\bsupportu\b/iu' => 'podršci',
            '/\bsupport\b/iu' => 'podrška',
            '/\bstack\b/iu' => 'kombinacija',
            '/\bweight-loss\b/iu' => 'kontrole težine',
            '/\bweight-balance\b/iu' => 'ravnoteže težine',
            '/\bdaily routine\b/iu' => 'dnevne rutine',
            '/\binside-out\b/iu' => 'iznutra i izvana',
            '/\bboost\b/iu' => 'dodatak',
            '/početni reset rutine/iu' => 'uredniji početak rutine',
            '/jači programski smjer za apetit i metabolizam/iu' => 'dodatni smjer za apetit, energiju i dosljednost',
            '/jači programski smjer/iu' => 'dodatni smjer',
            '/jači metabolički ritam/iu' => 'izraženiji ritam energije i rutine',
            '/najjači fokus na metabolički ritam/iu' => 'jasniji fokus na energiju i rutinu',
            '/metabolički ritam/iu' => 'ritam energije i rutine',
            '/bez obećavanja brzih rezultata/iu' => 'bez obećanja brzih rezultata',
            '/bez nerealnih obećanja o instant rezultatima/iu' => 'bez nerealnih obećanja',
            '/što čišći smjer/iu' => 'jasniji smjer',
            '/najčišći support/iu' => 'jasnija podrška',
            '/čistiji support/iu' => 'jasnija podrška',
            '/ovdje se najčešće gleda/iu' => 'najčešće se gleda',
            '/ovdje se najčešće kreće/iu' => 'najčešće se kreće',
            '/ovdje se najčešće preporuči/iu' => 'najčešće se preporuči',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text) ?? $text;
        }

        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    private function defaultOpeningNote(string $language, array $intent, bool $needsClarification): string
    {
        if ($needsClarification) {
            return match ($language) {
                'en' => 'The most helpful start is to understand the real goal first, then choose the product direction.',
                'sl' => 'Najbolj koristen začetek je najprej razumeti pravi cilj, nato izbrati smer izdelka.',
                default => 'Najkorisnije je prvo razumjeti pravi cilj, pa tek onda birati proizvod.',
            };
        }

        if (!empty($intent['medical_sensitive']) || !empty($intent['serious'])) {
            return match ($language) {
                'en' => 'The safest way to answer is through routine support, ingredients and a modest next step.',
                'sl' => 'Najvarnejši odgovor gre skozi podporo rutini, sestavine in zmeren naslednji korak.',
                default => 'Najsigurnije je gledati podršku rutini, sastojke i umjeren sljedeći korak.',
            };
        }

        return match ($language) {
            'en' => 'I would start with your goal, then check the ingredients and only then choose the product.',
            'sl' => 'Začel bi s tvojim ciljem, nato pogledal sestavine in šele potem izbral izdelek.',
            default => 'Krenuo bih od tvog cilja, zatim pogledao sastojke i tek onda odabrao proizvod.',
        };
    }

    private function priorityProductsFromConditions(array $conditionMatches): array
    {
        $titles = [];

        foreach ($conditionMatches as $conditionMatch) {
            $primaryProduct = trim((string) ($conditionMatch['primary_product'] ?? ''));
            if ($primaryProduct !== '') {
                $titles[] = $primaryProduct;
            }

            foreach ((array) ($conditionMatch['support_products'] ?? []) as $supportProduct) {
                $supportProduct = trim((string) $supportProduct);
                if ($supportProduct !== '') {
                    $titles[] = $supportProduct;
                }
            }
        }

        return array_values(array_unique($titles));
    }

    private function preferredPatternsFromConditions(array $conditionMatches): array
    {
        $patterns = [];

        foreach ($conditionMatches as $conditionMatch) {
            foreach ((array) ($conditionMatch['preferred_patterns'] ?? []) as $pattern) {
                $pattern = trim((string) $pattern);
                if ($pattern !== '') {
                    $patterns[] = $pattern;
                }
            }
        }

        return array_values(array_unique($patterns));
    }

    private function themePatternsFromMatches(array $themeMatches): array
    {
        $patterns = [];

        foreach ($themeMatches as $themeMatch) {
            foreach ((array) ($themeMatch['article_patterns'] ?? []) as $pattern) {
                $pattern = trim((string) $pattern);
                if ($pattern !== '') {
                    $patterns[] = $pattern;
                }
            }
        }

        return array_values(array_unique($patterns));
    }

    private function localizedText(array $entry, string $key, string $language): string
    {
        $value = $entry[$key] ?? '';
        if (is_string($value)) {
            return trim($value);
        }

        if (is_array($value)) {
            return trim((string) ($value[$language] ?? $value['hr'] ?? $value['en'] ?? reset($value) ?? ''));
        }

        return '';
    }

    private function localizedLines(array $entry, string $key, string $language): array
    {
        $value = $entry[$key] ?? [];
        if (is_array($value)) {
            $lines = $value[$language] ?? $value['hr'] ?? $value['en'] ?? $value;
            if (is_array($lines)) {
                return array_values(array_filter(array_map(
                    static fn (mixed $line): string => trim((string) $line),
                    $lines
                )));
            }
        }

        if (is_string($value) && trim($value) !== '') {
            return [trim($value)];
        }

        return [];
    }

    private function searchTokens(string $message): array
    {
        $normalized = $this->normalizeText($message);
        if ($normalized === '') {
            return [];
        }

        $parts = preg_split('/[^a-z0-9\+\-]+/u', $normalized) ?: [];
        $stopWords = ['ali', 'and', 'are', 'bio', 'da', 'for', 'how', 'ili', 'imam', 'je', 'kako', 'koji', 'na', 'od', 'sam', 'se', 'the', 'to', 'u', 'uz', 'za', 'zelim', 'želim'];
        $tokens = [];

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '' || strlen($part) < 3 || in_array($part, $stopWords, true)) {
                continue;
            }

            $tokens[$part] = true;
        }

        return array_keys($tokens);
    }

    private function containsAny(string $haystack, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            $keyword = $this->normalizeText((string) $keyword);
            if ($keyword !== '' && str_contains($haystack, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function normalizeLanguage(string $language): string
    {
        $language = strtolower(trim($language));

        return in_array($language, ['hr', 'en', 'sl'], true) ? $language : 'hr';
    }

    private function normalizePath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '/';
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parsedPath = parse_url($path, PHP_URL_PATH);

            return is_string($parsedPath) && $parsedPath !== '' ? $parsedPath : '/';
        }

        return str_starts_with($path, '/') ? $path : '/';
    }

    private function normalizeText(string $value): string
    {
        $value = mb_strtolower($value);
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        if (is_string($transliterated) && $transliterated !== '') {
            $value = $transliterated;
        }

        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }
}
