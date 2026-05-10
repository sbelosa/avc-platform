<?php

declare(strict_types=1);

$entry = static fn (
    int $sourceId,
    string $title,
    string $slug,
    string $excerpt,
    string $summaryHtml,
    array $faqItems,
    string $metaTitle,
    string $metaDescription,
    string $breadcrumbTitle,
    array $sections
): array => [
    'source_translation_id' => $sourceId,
    'language_code' => 'en',
    'title' => $title,
    'slug' => $slug,
    'excerpt' => $excerpt,
    'summary_html' => $summaryHtml,
    'faq_items' => $faqItems,
    'meta_title' => $metaTitle,
    'meta_description' => $metaDescription,
    'breadcrumb_title' => $breadcrumbTitle,
    'sections' => $sections,
];

return [
    'key' => 'symptoms-immunity-wellness-en-wave-1',
    'name' => 'Symptoms, Immunity and Practical Wellness (EN) - wave 1',
    'notes' => 'A larger localized editorial wave for digestive or respiratory issues, chronic symptoms, immunity and more practical wellness routines.',
    'entries' => [
        $entry(
            493,
            'How Probiotics and Healthy Diets Can Work Together Without Food Fanaticism',
            'how-probiotics-and-healthy-diets-can-work-hand-in-hand',
            'Probiotics and food are often treated like two separate worlds, yet they work best when they support each other. Here is how to connect supplements and eating patterns in a way that actually helps the gut without turning food into a rigid belief system.',
            '<ul><li>Probiotics work best when they are supported by food patterns that reduce stress on the digestive system.</li><li>The biggest mistake is taking a probiotic while meals and digestive triggers stay chaotic.</li><li>A smarter approach looks at the whole picture: what you eat, how you eat and where a supplement really fits.</li></ul>',
            [
                ['question' => 'Do probiotics make sense for everyone?', 'answer' => 'Not automatically. Their value depends on symptoms, food quality and the real digestive goal.'],
                ['question' => 'Can good food be enough without a supplement?', 'answer' => 'Sometimes yes, especially when digestion is stable and the meal pattern is already strong and varied.'],
                ['question' => 'When does a supplement fit better?', 'answer' => 'When there is a clear reason, such as recovery support or a need for more digestive structure.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Expecting a capsule to do the work that meal rhythm and food quality still are not doing.'],
            ],
            'Probiotics and Healthy Diets: How to Combine Them So They Truly Help the Gut',
            'Learn how to connect probiotics and healthy eating into a smarter gut-support plan without rigid food rules.',
            'Probiotics and Diet',
            [
                ['heading' => 'Why gut support works best as a combined strategy', 'html' => '<p>Supplements and food habits often get discussed separately, but the gut experiences them together. That is why more useful digestive progress usually comes from better alignment rather than from one tool used in isolation.</p>'],
                ['heading' => 'Why food structure still leads the bigger story', 'html' => '<p>A supplement may support the process, yet the everyday pattern of meals and tolerable foods usually shapes the gut more powerfully over time. Better routines therefore make probiotics easier to judge and more likely to help.</p>'],
            ]
        ),
        $entry(
            519,
            'Celiac Symptoms in Adults: How to Recognize Early Signs Without Self-Diagnosing',
            'celiac-disease-symptoms-in-adults-recognize-early-signs-and-make-your-life-easier',
            'Celiac disease in adults does not always look dramatic and does not always begin with obvious digestive symptoms. Here is how to notice the earlier pattern, what not to ignore and why rushing into self-diagnosis often creates more confusion than clarity.',
            '<ul><li>Celiac disease in adults can appear through more than bloating or diarrhea alone, including fatigue and nutritional problems.</li><li>The biggest mistake is removing gluten before the proper diagnostic process is completed.</li><li>A smarter approach notices patterns but leaves diagnosis to the correct medical pathway.</li></ul>',
            [
                ['question' => 'Are celiac symptoms always digestive?', 'answer' => 'No. In adults they may also show up through fatigue, deficiency patterns, skin issues or a broader sense of poor wellbeing.'],
                ['question' => 'Why should gluten not be removed too soon?', 'answer' => 'Because doing so may complicate or blur the later diagnostic process.'],
                ['question' => 'When is celiac suspicion more reasonable?', 'answer' => 'When several repeating symptoms and patterns point toward poor tolerance or malabsorption.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Assuming every digestive problem must be about gluten without deeper evaluation.'],
            ],
            'Celiac Symptoms in Adults: Early Signs and Why Self-Diagnosis Can Backfire',
            'Recognize possible early celiac patterns in adults and see why proper diagnosis matters more than guessing.',
            'Celiac Symptoms',
            [
                ['heading' => 'Why adult celiac patterns can be easy to miss', 'html' => '<p>Many people expect one clear digestive picture, yet adult celiac disease can show itself more subtly. That is exactly why better awareness matters before people jump to conclusions or quick eliminations.</p>'],
                ['heading' => 'Why diagnosis needs the right order', 'html' => '<p>Trying to “solve” the problem alone too early often makes the real answer harder to confirm. Clearer decisions usually come from respecting the diagnostic pathway instead of improvising it.</p>'],
            ]
        ),
        $entry(
            556,
            'Persistent Cough: How to Calm It in Adults and Ease an Irritated Throat',
            'persistent-cough-how-to-relieve-it-in-adults-and-soothe-throat-tickling',
            'A persistent cough drains both the body and the nerves, especially when it disrupts sleep and lingers longer than expected. Here is how to approach it more intelligently through throat care, moisture, daily rhythm and better pattern awareness.',
            '<ul><li>Persistent cough should be viewed through duration, intensity, associated symptoms and the triggers that keep it going.</li><li>The biggest mistake is treating it the same way for weeks without noticing whether it is changing.</li><li>A smarter approach calms the throat, protects sleep and watches whether the wider picture needs more attention.</li></ul>',
            [
                ['question' => 'Why does cough sometimes last so long?', 'answer' => 'Because the throat and airways may take longer to settle or because the irritation pattern remains active.'],
                ['question' => 'What bothers adults most about persistent cough?', 'answer' => 'Often sleep disruption, throat tickling and the feeling that the symptom keeps returning.'],
                ['question' => 'When is more caution needed?', 'answer' => 'If the cough lasts unusually long, worsens or comes with heavier associated symptoms.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Changing remedies without understanding what keeps the irritation going.'],
            ],
            'Persistent Cough in Adults: How to Calm It and When to Look at the Bigger Picture',
            'Learn how to approach persistent cough in adults through calmer throat support, better routine and more caution when needed.',
            'Persistent Cough',
            [
                ['heading' => 'Why persistent cough needs pattern thinking', 'html' => '<p>Longer-lasting cough often becomes frustrating because it feels repetitive and vague. That is why support works better when it considers what is prolonging the irritation rather than only suppressing the sound of coughing.</p>'],
                ['heading' => 'Why sleep protection matters so much', 'html' => '<p>Once cough begins to interfere with rest, the whole recovery process becomes harder. Better throat care and calmer routines often matter as much because they protect the night, not only the symptom.</p>'],
            ]
        ),
        $entry(
            557,
            'Cough Tea: Which Herbal Blends Actually Make Sense for Faster Relief',
            'cough-tea-best-herbal-blends-and-tips-for-quick-relief',
            'Herbal cough tea may be useful, but mainly when you know what you are trying to calm: dryness, irritation, warmth before sleep or throat scratchiness. Here is how to use tea more intelligently instead of assuming every “natural” blend does the same thing.',
            '<ul><li>Cough tea works best as a soothing ritual that supports throat comfort and a calmer recovery rhythm.</li><li>The biggest mistake is expecting one herbal blend to solve every cough type and every cause.</li><li>A smarter approach chooses tea according to throat feeling, time of day and the overall symptom picture.</li></ul>',
            [
                ['question' => 'Can tea really help cough?', 'answer' => 'It can help with comfort and soothing, especially when the throat is dry or irritated.'],
                ['question' => 'Is every herbal tea equally useful?', 'answer' => 'No. The best fit depends on the herb profile and what exactly needs calming.'],
                ['question' => 'When is tea most useful?', 'answer' => 'Often in the evening or whenever soothing the throat may help reduce irritation and protect sleep.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Relying on tea alone when the symptom is longer-lasting or becoming more intense.'],
            ],
            'Cough Tea: How to Choose a Herbal Blend That Truly Helps the Throat',
            'See when cough tea makes sense and how to use herbal blends for more comfort, less irritation and calmer nights.',
            'Cough Tea',
            [
                ['heading' => 'Why tea helps most as supportive care', 'html' => '<p>Warm herbal drinks often work because they slow things down and bring the throat some relief. That supportive role is valuable, but it becomes much more useful when expectations remain realistic.</p>'],
                ['heading' => 'Why matching the tea to the symptom matters', 'html' => '<p>Different cough experiences do not always need the same support. A little more attention to timing and throat feel usually makes tea a much more intelligent part of the routine.</p>'],
            ]
        ),
        $entry(
            558,
            'Psoriasis: Natural Strategies, Triggers and How to Give the Skin More Calm',
            'psoriasis-natural-strategies-causes-and-relief-tips',
            'Psoriasis requires patience and a wider view because the skin often reacts to stress, irritation, routine and the wider state of the body. Here is how to think about natural strategies more realistically, without false hope and without aggressive experimentation.',
            '<ul><li>Psoriasis needs continuity more than miracle interventions.</li><li>The biggest mistake is changing products too often while ignoring stress, triggers and everyday habits.</li><li>A smarter approach builds a gentler routine, fewer irritants and better awareness of flare patterns.</li></ul>',
            [
                ['question' => 'Can natural strategies help psoriasis?', 'answer' => 'They can help as part of a wider skin-calming routine, but not through unrealistic promises of instant disappearance.'],
                ['question' => 'Why does stress matter so much?', 'answer' => 'Because for many people stress clearly amplifies inflammatory patterns and skin reactivity.'],
                ['question' => 'What does the skin usually need most?', 'answer' => 'More consistency, less irritation and a calmer long-term care approach.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Changing ten things at once and then not knowing what helped or what made the skin worse.'],
            ],
            'Psoriasis: Natural Support, Triggers and a Calmer Skin-Care Pattern',
            'Learn how psoriasis may be supported through more consistent routines, fewer triggers and gentler natural strategies.',
            'Psoriasis',
            [
                ['heading' => 'Why psoriasis care needs more patience than novelty', 'html' => '<p>People often want quick visible change, yet psoriasis usually responds better to calm repetition and lower irritation than to constant new experiments. That steadier approach often protects the skin much more effectively.</p>'],
                ['heading' => 'Why triggers deserve as much attention as products', 'html' => '<p>Even the best product routine can disappoint if the daily trigger pattern keeps worsening the skin. Better results often come when product choices and lifestyle awareness improve together.</p>'],
            ]
        ),
        $entry(
            562,
            'Natural Relief for Heavy Legs: Hydromassage, Aloe Cream and a Lighter Day',
            'natural-relief-for-heavy-legs-hydromassage-and-aloe-cream',
            'Heavy legs often reflect long sitting, standing, heat and the feeling of slowed circulation by the end of the day. Here is how simple rituals like rinsing, massage and skin support may bring more ease and comfort back.',
            '<ul><li>Heavy legs often respond well to small repeated physical rituals that improve comfort and the feeling of movement.</li><li>The biggest mistake is ignoring the symptom until it becomes a daily frustration or more intense problem.</li><li>A smarter approach uses cooling, walking breaks and practical body care more consistently.</li></ul>',
            [
                ['question' => 'Why do legs feel heavy?', 'answer' => 'Long sitting, standing, heat and reduced movement often build that sensation over the day.'],
                ['question' => 'Can massage and rinsing help?', 'answer' => 'Yes, they often bring a sense of relief and freshness when used regularly.'],
                ['question' => 'Where does aloe cream fit?', 'answer' => 'As part of the care ritual and the soothing, cooling feel after a demanding day.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Waiting until discomfort becomes strong instead of using small daily relief habits.'],
            ],
            'Heavy Legs: Natural Relief Through Cooling, Movement and Simpler Care Routines',
            'Discover how heavy legs may feel lighter through hydromassage, aloe cream and practical daily circulation rituals.',
            'Heavy Legs',
            [
                ['heading' => 'Why heavy legs often need small repeated care', 'html' => '<p>The body frequently responds well to little moments of relief rather than one dramatic intervention. That is why a few practical habits can sometimes change the whole feeling of the day.</p>'],
                ['heading' => 'Why prevention often feels easier than rescue', 'html' => '<p>When heavy-leg routines become regular, they tend to work better than waiting for strong discomfort. Earlier support usually creates more ease than last-minute reaction.</p>'],
            ]
        ),
        $entry(
            567,
            'Parkinson’s Disease and Diet: Antioxidants, Meals and Everyday Function Support',
            'parkinsons-disease-and-diet-antioxidants-in-symptom-relief',
            'Diet does not solve everything in Parkinson’s disease, yet it may still influence energy, digestion and how manageable the day feels. Here is how to think about antioxidants and meals more calmly, with less hype and more practical support.',
            '<ul><li>Diet in Parkinson’s care makes the most sense as support for everyday quality of life, not as a replacement for treatment.</li><li>The biggest mistake is chasing one miracle nutrient instead of looking at meals, digestion and energy as a whole.</li><li>A smarter approach uses food for more stability, easier digestion and better daily rhythm.</li></ul>',
            [
                ['question' => 'Do antioxidants make sense here?', 'answer' => 'They may, as part of a broader food pattern, but not as the whole answer to a complex condition.'],
                ['question' => 'Why does diet matter in Parkinson’s care?', 'answer' => 'Because it may affect energy, digestion, comfort and day-to-day manageability.'],
                ['question' => 'Should food be expected to solve everything?', 'answer' => 'No. Food support makes the most sense as one layer inside a wider medical and practical framework.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Focusing on a single “superfood” while ignoring the wider meal structure and routine.'],
            ],
            'Parkinson’s Disease and Diet: Where Antioxidants May Help and Where Realism Matters',
            'Understand how diet may support everyday life in Parkinson’s disease without turning food into a miracle story.',
            'Parkinson and Diet',
            [
                ['heading' => 'Why food support matters even when it is not the whole answer', 'html' => '<p>People sometimes underestimate diet because it cannot replace treatment. Yet the daily food pattern can still influence comfort, energy and how manageable the whole day feels.</p>'],
                ['heading' => 'Why broader structure beats isolated nutrient hype', 'html' => '<p>One ingredient rarely changes a complex condition on its own. A more useful strategy usually builds around meal timing, digestion and a steadier day-to-day rhythm.</p>'],
            ]
        ),
        $entry(
            572,
            'Reishi and Shiitake: How Medicinal Mushrooms May Support Immunity and Vitality',
            'reishi-and-shiitake-how-medicinal-mushrooms-strengthen-immunity-and-heart',
            'Medicinal mushrooms sound fascinating, which is exactly why they are so vulnerable to exaggerated health claims. Here is how to think about reishi and shiitake through immunity, food quality and sustainable support instead of miracle thinking.',
            '<ul><li>Reishi and shiitake may be interesting as part of a wider immunity and vitality routine.</li><li>The biggest mistake is expecting mushrooms to do what sleep, food quality and recovery are still not doing.</li><li>A smarter approach uses them as a complement to varied nutrition rather than as a miracle powder or capsule.</li></ul>',
            [
                ['question' => 'Why are reishi and shiitake so popular?', 'answer' => 'Because of tradition, interest in their compounds and the idea that they may support immunity and vitality.'],
                ['question' => 'Can they replace healthy eating?', 'answer' => 'No. They fit best as one part of a broader and more varied food pattern.'],
                ['question' => 'When do they make more sense?', 'answer' => 'When the goal is gentler long-term support rather than a dramatic quick effect.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Buying mushroom products through hype without knowing how they fit the wider routine.'],
            ],
            'Reishi and Shiitake: Where They Make Sense for Immunity and Where Hype Starts',
            'Learn how reishi and shiitake may fit into immunity and vitality support without unrealistic expectations.',
            'Reishi and Shiitake',
            [
                ['heading' => 'Why medicinal mushrooms attract so much projection', 'html' => '<p>People naturally project big hopes onto unusual “functional” ingredients. That is why mushrooms like reishi and shiitake are easiest to use well when they are kept inside a calmer, more realistic frame.</p>'],
                ['heading' => 'Why they work best as part of a larger food pattern', 'html' => '<p>Their value makes more sense when they complement a stronger base rather than trying to carry the whole immunity story alone. That bigger pattern usually decides much more than one special ingredient.</p>'],
            ]
        ),
        $entry(
            577,
            'Flu Season: How Zinc, Vitamin D and Probiotics May Support Immunity More Realistically',
            'flu-season-boost-your-immunity-with-zinc-vitamin-d-and-probiotics',
            'Flu season always brings back the same questions about what is worth taking and how the body can be supported before the pressure rises. Here is how to think about zinc, vitamin D and probiotics with less panic and more practical sense.',
            '<ul><li>Zinc, vitamin D and probiotics may fit a seasonal routine, but only on top of sleep, food quality and basic hygiene.</li><li>The biggest mistake is expecting three products to carry the immune system through a demanding season by themselves.</li><li>A smarter approach treats supplements as support for a stronger foundation, not as a substitute for one.</li></ul>',
            [
                ['question' => 'Do zinc and vitamin D make sense during flu season?', 'answer' => 'They can, especially when the season and the person’s needs clearly justify the support.'],
                ['question' => 'Why are probiotics mentioned too?', 'answer' => 'Because they are often considered part of the wider story of resilience and gut-related support.'],
                ['question' => 'Should everything be taken blindly?', 'answer' => 'No. It makes more sense to consider needs, food and daily rhythm first.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Buying many supplements in panic while the basic routine stays weak.'],
            ],
            'Flu Season: Zinc, Vitamin D and Probiotics in a More Realistic Immunity Routine',
            'See when zinc, vitamin D and probiotics may support flu-season resilience and where basic habits still lead the story.',
            'Flu Season',
            [
                ['heading' => 'Why flu season support still begins with basics', 'html' => '<p>When pressure rises, people often want a compact supplement answer. Yet the immune system still responds most deeply to sleep, food, stress and general daily rhythm before any seasonal add-ons are layered on top.</p>'],
                ['heading' => 'Why calmer decisions create better seasonal support', 'html' => '<p>Less panic usually leads to better supplement choices. A more measured approach helps people choose only what fits instead of throwing everything at the problem at once.</p>'],
            ]
        ),
        $entry(
            579,
            'Restarting the Body After a Break: A Safer Return to Movement Without Overdoing It',
            'restarting-your-body-after-a-break-a-safe-plan-to-return-to-activity',
            'After a longer break, the body does not need punishment. It needs a return that builds trust, capacity and confidence again. Here is how to restart activity without letting the first week destroy the motivation for what should come next.',
            '<ul><li>Returning to activity works best when it is gentle enough not to damage motivation or the body in the first few days.</li><li>The biggest mistake is trying to “catch up for lost time” through immediate high intensity.</li><li>A smarter approach builds the routine through walking, basic movement and gradual load increase.</li></ul>',
            [
                ['question' => 'How should someone begin after a longer break?', 'answer' => 'Usually through lighter movement, shorter sessions and more attention to recovery.'],
                ['question' => 'Why do people quit again so quickly?', 'answer' => 'Because they start too hard, then run into pain, fatigue and the feeling that the comeback is too much.'],
                ['question' => 'How fast should intensity go up?', 'answer' => 'Gradually, so the body has time to adapt without overload.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Comparing yourself with a past version of your body and forcing a level that is no longer current.'],
            ],
            'Returning to Activity After a Break: How to Start Safely Without Losing Momentum',
            'Learn how to restart movement after a break through a safer, steadier plan that protects motivation and recovery.',
            'Return to Activity',
            [
                ['heading' => 'Why the first week sets the emotional tone', 'html' => '<p>When the comeback feels too punishing, motivation often collapses quickly. That is why a kinder opening pace usually creates stronger long-term consistency than an aggressive restart.</p>'],
                ['heading' => 'Why movement trust needs to be rebuilt', 'html' => '<p>After time away, the body often needs a return to confidence as much as a return to fitness. A steadier progression helps rebuild both at the same time.</p>'],
            ]
        ),
    ],
];
