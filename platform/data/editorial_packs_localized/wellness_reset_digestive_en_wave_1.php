<?php

declare(strict_types=1);

$entry = static fn (
    int $sourceId,
    string $languageCode,
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
    'language_code' => $languageCode,
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
    'key' => 'wellness-reset-digestive-en-wave-1',
    'name' => 'Wellness Reset and Digestive Guides (EN) - wave 1',
    'notes' => 'A larger localized editorial wave for burnout, yoga, weight-loss context and targeted digestive or respiratory symptom guides.',
    'entries' => [
        $entry(
            142,
            'en',
            'Heavy Metal Detox: Where Chelators and Aloe Fit, and Where They Do Not',
            'heavy-metal-detoxification-how-chelators-and-aloe-vera-can-help',
            'Heavy metals are one of those topics that quickly slide into panic and online exaggeration. Here is how to think about chelators, supportive routines and detoxification more responsibly, without creating new problems through poorly guided “cleanses.”',
            '<ul><li>Heavy metal detox is not a home experiment but a topic that needs far more context and caution.</li><li>The biggest mistake is reaching for aggressive protocols without a clear need or proper framework.</li><li>A smarter approach separates general body support from true medical situations that require specialized care.</li></ul>',
            [
                ['question' => 'Is this a topic everyone should worry about?', 'answer' => 'Not in the same way. It is important to separate general concern from real confirmed exposure or risk.'],
                ['question' => 'Can chelators be risky?', 'answer' => 'Yes, especially when used without professional guidance and without a clear reason.'],
                ['question' => 'Where does aloe vera fit?', 'answer' => 'More as part of gentler body support, not as the main tool for specific medical detox interventions.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Starting an aggressive “detox” out of fear rather than from real evidence and need.'],
            ],
            'Heavy Metal Detox: How to Separate Supportive Care from a Risky Experiment',
            'Learn how to think more responsibly about heavy metals, chelators and detox routines without online panic.',
            'Heavy Metal Detox',
            [
                ['heading' => 'Why heavy metal talk easily becomes distorted', 'html' => '<p>This topic often attracts strong emotions and dramatic claims. That is exactly why it benefits from better context, stronger evidence and more restraint than the internet usually offers.</p>'],
                ['heading' => 'Why support and treatment are not the same thing', 'html' => '<p>General body care can be useful, but that is very different from an actual detox intervention. Clearer boundaries between those ideas protect people from confusion and from unnecessary risk.</p>'],
            ]
        ),
        $entry(
            171,
            'en',
            'When Is It Time for a Break? Burnout Signs That Should Not Be Ignored',
            'when-is-it-time-for-a-break-burnout-and-how-to-prevent-it',
            'Burnout rarely arrives overnight. More often it builds across months of low recovery, inner pressure and life that no longer leaves space to breathe. Here is how to spot the early signs before exhaustion becomes the whole system.',
            '<ul><li>Burnout often begins long before people admit that normal functioning is already slipping.</li><li>The biggest mistake is treating rest like weakness or luxury instead of protection.</li><li>A smarter approach notices earlier signs and brings recovery back before the whole system breaks down.</li></ul>',
            [
                ['question' => 'How can early burnout be recognized?', 'answer' => 'Through constant exhaustion, low motivation, irritability and the sense that rest no longer restores you properly.'],
                ['question' => 'Is one free weekend enough?', 'answer' => 'Sometimes not, because the issue often sits inside the whole structure of life and pressure, not only in one busy week.'],
                ['question' => 'Why do people react too late?', 'answer' => 'Because they often function on inertia, duty and the belief that they can push a little longer.'],
                ['question' => 'What helps most?', 'answer' => 'Earlier limits, sleep, less overload and small regular spaces for real recovery.'],
            ],
            'Burnout: When It Is Time for a Break and Which Signs Matter Most',
            'Recognize the early signs of burnout and learn how to restore recovery before deeper collapse takes over.',
            'Burnout',
            [
                ['heading' => 'Why burnout is usually a slow build, not a sudden event', 'html' => '<p>Many people think burnout begins on the day they finally “break.” In reality, it usually grows through long periods of strain where recovery becomes too small for the load being carried.</p>'],
                ['heading' => 'Why recovery has to be allowed before it feels urgent', 'html' => '<p>Waiting until the body collapses makes change much harder. The healthier strategy is often to treat rest as a maintenance practice rather than as an emergency response.</p>'],
            ]
        ),
        $entry(
            172,
            'en',
            'Beginner’s Guide to Yoga: How to Start Without Pressure or the Wrong Expectations',
            'beginners-guide-to-yoga-how-to-start-and-which-supplements-to-take',
            'Yoga for beginners does not need to be an aesthetic performance or a flexibility test. It helps most when it brings breathing, presence and smarter movement into daily life without the pressure to do everything perfectly from day one.',
            '<ul><li>Starting yoga makes the most sense when it reduces tension and builds connection with the body rather than creating a new performance standard.</li><li>The biggest mistake is thinking you must already be flexible, calm or “ready” before beginning.</li><li>A smarter approach chooses a simpler practice and a pace gentle enough to keep.</li></ul>',
            [
                ['question' => 'Do beginners need special equipment?', 'answer' => 'Not much. A mat, comfortable clothing and a little space are usually enough to begin.'],
                ['question' => 'Do you need to be flexible already?', 'answer' => 'No. Many people begin yoga precisely to slowly build more mobility and ease.'],
                ['question' => 'How often should beginners practice?', 'answer' => 'Short and regular is usually better than rare and overly ambitious.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Comparing a beginner body and routine with advanced social-media presentations.'],
            ],
            'Yoga for Beginners: A Calmer, Simpler Way to Start and Stay With It',
            'Start yoga through breathing, basic movement and a routine that feels realistic rather than pressuring.',
            'Yoga for Beginners',
            [
                ['heading' => 'Why beginners do better without performance pressure', 'html' => '<p>Yoga often becomes more useful when it is treated as a supportive practice instead of a visual standard to live up to. That shift usually makes the habit much easier to begin and keep.</p>'],
                ['heading' => 'Why gentler repetition creates stronger progress', 'html' => '<p>A body usually learns more from calm regular exposure than from dramatic effort. That is why softer beginnings often build more lasting confidence than aggressive starts.</p>'],
            ]
        ),
        $entry(
            468,
            'en',
            'Ozempic and Weight Loss: What Deserves Context Before the Hype',
            'ozempic-and-weight-loss-what-you-need-to-know-before-use',
            'Ozempic has become central to weight-loss conversations, yet the real story is much more complex than before-and-after social media narratives. Here is how to think about it with more medical context, more caution and less trend-driven simplification.',
            '<ul><li>Ozempic and similar drugs are serious therapeutic tools, not trend accessories.</li><li>The biggest mistake is treating them like a simple shortcut without understanding who they are for and under what conditions.</li><li>A smarter approach looks at health context, habits and what long-term sustainability actually means.</li></ul>',
            [
                ['question' => 'Is Ozempic a universal weight-loss solution?', 'answer' => 'No. It is a medication with specific indications and should be viewed in that medical context.'],
                ['question' => 'Why is there so much hype around it?', 'answer' => 'Because public visibility of rapid results makes complex treatment look simple.'],
                ['question' => 'What do people often overlook?', 'answer' => 'Side effects, follow-up, health context and what happens over the long term.'],
                ['question' => 'What is the smarter frame?', 'answer' => 'To discuss it as a therapeutic option rather than an instant wellness trend.'],
            ],
            'Ozempic and Weight Loss: Benefits, Limits and Why It Is Not Just a Trend',
            'Understand how to think more responsibly about Ozempic and weight loss through medical context instead of hype.',
            'Ozempic',
            [
                ['heading' => 'Why social media often flattens the full story', 'html' => '<p>When people see only fast visible change, it becomes easy to miss the full medical and lifestyle context. That is why clearer framing matters so much with this kind of medication.</p>'],
                ['heading' => 'Why long-term thinking matters more than novelty', 'html' => '<p>Weight loss tools should be judged not only by what happens quickly, but also by what happens sustainably. That longer perspective usually leads to wiser questions and fewer illusions.</p>'],
            ]
        ),
        $entry(
            469,
            'en',
            'The Best Weight-Loss Diet: How to Compare Plans Without Losing Yourself',
            'best-weight-loss-diet-comparison-of-popular-plans-and-tips',
            'The best diet is rarely the strictest one. More often it is the one you can follow long enough to create real change. Here is how to compare popular plans without getting trapped in another cycle of frustration and quitting.',
            '<ul><li>The best weight-loss diet is the one that fits your life, health and long-term capacity to keep going.</li><li>The biggest mistake is choosing a plan by promised speed rather than by sustainability.</li><li>A smarter approach compares restriction, satiety, flexibility and psychological cost.</li></ul>',
            [
                ['question' => 'Is there one best diet for everyone?', 'answer' => 'No. People differ in routines, health needs and what they can realistically sustain.'],
                ['question' => 'Why do stricter plans often fail?', 'answer' => 'Because they may work short-term while asking too much from everyday life over time.'],
                ['question' => 'What matters more than the diet label?', 'answer' => 'How sustainable it is, how well it manages hunger and what it does to your relationship with food.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Switching plans too often without giving any one structure enough time to work.'],
            ],
            'Best Weight-Loss Diet: How to Choose a Plan You Can Actually Live With',
            'Compare weight-loss diets through sustainability, satiety and real-life fit instead of only fast promises.',
            'Best Diet',
            [
                ['heading' => 'Why the best diet is usually the one you can stay with', 'html' => '<p>Weight-loss success often depends less on the cleverness of the diet name and more on how well the structure fits daily life. A good plan tends to support reality rather than fight it.</p>'],
                ['heading' => 'Why flexibility often protects results', 'html' => '<p>Plans that allow some movement and adjustment are often easier to keep and recover from. That flexibility can matter more than strictness when the goal is lasting change.</p>'],
            ]
        ),
        $entry(
            484,
            'en',
            'Natural Heartburn Relief: Aloe Vera and Habits That Truly Calm the Pattern',
            'natural-heartburn-remedy-aloe-vera-and-proven-strategies-for-quick-relief',
            'With heartburn, understanding the pattern usually helps more than repeatedly putting out the same fire. Here is how food timing, meal size and support tools such as aloe vera may help create more stable relief.',
            '<ul><li>Heartburn often needs habit change, not only occasional symptom control.</li><li>The biggest mistake is ignoring triggers and expecting one product to solve a recurring pattern.</li><li>A smarter approach looks at meals, timing, food amount and body position after eating.</li></ul>',
            [
                ['question' => 'Can aloe vera help with heartburn?', 'answer' => 'For some people it may be part of gentler support, but it does not replace understanding the pattern itself.'],
                ['question' => 'What commonly worsens heartburn?', 'answer' => 'Large meals, late eating, richer foods, stress and lying down soon after eating.'],
                ['question' => 'When is more caution needed?', 'answer' => 'When symptoms are frequent, worsen at night or come with more serious warning signs.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Treating symptoms without changing the food and timing habits that keep them returning.'],
            ],
            'Heartburn Relief: Aloe Vera, Food Timing and Habits That Make More Sense',
            'Learn how heartburn may improve through better meal habits, timing and gentler support such as aloe vera.',
            'Heartburn',
            [
                ['heading' => 'Why heartburn is often a pattern, not a random event', 'html' => '<p>Many people think about heartburn only when the burning starts, yet the body is often responding to repeated food and rhythm patterns. That is why lasting relief usually depends on more than one soothing product.</p>'],
                ['heading' => 'Why simpler meals often support the whole system', 'html' => '<p>Meal size, timing and posture after eating can change the experience significantly. The most useful support often works best when those habits change together.</p>'],
            ]
        ),
        $entry(
            485,
            'en',
            'Natural Remedies for Diarrhea: Aloe Vera, Probiotics and Smarter Recovery',
            'natural-remedies-for-diarrhea-aloe-vera-and-probiotics-for-quick-relief',
            'When diarrhea appears, the body usually needs calm, fluids and digestive recovery more than panic and random kitchen remedies. Here is what natural support may truly offer and where the line of caution still matters.',
            '<ul><li>Diarrhea requires hydration, caution and awareness of the underlying cause, not only fast symptom suppression.</li><li>The biggest mistake is underestimating fluid loss or waiting too long when the situation worsens.</li><li>A smarter approach combines gentle digestive recovery, targeted support and attention to warning signs.</li></ul>',
            [
                ['question' => 'Are probiotics useful here?', 'answer' => 'They can be, depending on the cause and when they are introduced as part of recovery.'],
                ['question' => 'What matters most first?', 'answer' => 'Fluids, digestive rest and watching for signs of dehydration.'],
                ['question' => 'Can aloe vera help?', 'answer' => 'Sometimes as part of gentler support, but it is not the main answer to every cause.'],
                ['question' => 'When should extra help be considered?', 'answer' => 'When symptoms worsen, last longer or are paired with stronger warning signs.'],
            ],
            'Diarrhea Relief: Hydration, Probiotics and a More Careful Recovery Plan',
            'Understand how diarrhea may be supported more naturally through fluids, probiotics and a calmer recovery approach.',
            'Diarrhea',
            [
                ['heading' => 'Why hydration remains the central priority', 'html' => '<p>Digestive symptoms can feel dramatic, but the most important concern is often how much fluid and stability the body is losing. That is why recovery begins with support, not panic.</p>'],
                ['heading' => 'Why recovery works best when it stays gentle', 'html' => '<p>The digestive system often responds better to less pressure, not more intervention. A softer recovery pattern usually protects the body better than overreacting with too many products.</p>'],
            ]
        ),
        $entry(
            486,
            'en',
            'How to Stop Vomiting Naturally: Ginger, Rehydration and Less Guesswork',
            'how-to-stop-vomiting-naturally-aloe-vera-ginger-and-rehydration',
            'Vomiting quickly drains the body, which is why protecting fluid balance usually matters more than aggressive tricks. Here is how to support recovery more gently through rehydration, ginger and a slower return to food.',
            '<ul><li>When vomiting happens, the top priority is to protect fluids and let the digestive system calm down gradually.</li><li>The biggest mistake is reintroducing heavy food too quickly or ignoring signs of dehydration.</li><li>A smarter approach uses small sips, quiet recovery and careful timing.</li></ul>',
            [
                ['question' => 'What matters most after vomiting?', 'answer' => 'Gradually bringing fluids back in and not overwhelming the stomach too quickly.'],
                ['question' => 'Can ginger help?', 'answer' => 'For some people it may help ease nausea when used gently and in the right form.'],
                ['question' => 'When is more caution needed?', 'answer' => 'When fluids cannot be kept down or weakness and dehydration signs keep rising.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Going back to a normal heavy meal far too early.'],
            ],
            'Vomiting Relief: Rehydration, Ginger and a Gentler Way Back to Food',
            'Learn how to support vomiting recovery through fluids, ginger and a safer return to eating.',
            'Vomiting',
            [
                ['heading' => 'Why the body needs a slower recovery pace', 'html' => '<p>Vomiting leaves the stomach and the whole system more vulnerable for a while. That is why the most useful support often feels simple: smaller sips, more patience and less pressure.</p>'],
                ['heading' => 'Why timing matters as much as the support tool', 'html' => '<p>Even a useful remedy can become unhelpful when introduced too fast. Recovery usually depends on pace as much as on the specific thing being used.</p>'],
            ]
        ),
        $entry(
            487,
            'en',
            'Natural Cough Relief: Aloe Vera, Honey and Smarter Throat Support',
            'natural-cough-remedy-aloe-vera-honey-and-proven-herbal-strategies',
            'Not every cough is the same problem, which is why not every “natural remedy” will make equal sense. Here is how to separate throat soothing from real recovery and where aloe vera, honey and herbs may fit more intelligently.',
            '<ul><li>Natural cough support works best when it matches the type of irritation and the wider symptom picture.</li><li>The biggest mistake is treating every cough the same regardless of duration or cause.</li><li>A smarter approach soothes the throat, protects sleep and watches how the pattern changes over time.</li></ul>',
            [
                ['question' => 'Can honey help with cough?', 'answer' => 'For an irritated throat it can often bring soothing comfort and reduce irritation.'],
                ['question' => 'Where does aloe vera fit?', 'answer' => 'More as part of calming throat support than as the main answer to every type of cough.'],
                ['question' => 'Why is not every cough the same?', 'answer' => 'Because cough can come from different triggers and therefore respond differently to support.'],
                ['question' => 'When is more caution needed?', 'answer' => 'If the cough lasts a long time, worsens or comes with stronger symptoms.'],
            ],
            'Cough Relief: Honey, Aloe Vera and More Sensible Natural Throat Support',
            'See how cough may be supported more naturally through honey, herbs and gentler throat-soothing routines.',
            'Cough',
            [
                ['heading' => 'Why cough care starts with understanding the pattern', 'html' => '<p>People often search for a single remedy, yet cough support works best when the type of irritation and symptom pattern are clearer. That context helps simple soothing tools work more intelligently.</p>'],
                ['heading' => 'Why sleep support often matters more than people think', 'html' => '<p>When cough steals rest, the whole recovery picture becomes harder. Supporting the throat in ways that protect sleep can therefore matter far beyond the momentary symptom itself.</p>'],
            ]
        ),
        $entry(
            488,
            'en',
            'Tea for Diarrhea: Which Herbal Blends Make Sense and How to Use Them Carefully',
            'tea-for-diarrhea-proven-herbal-blends-and-aloe-vera-for-quick-recovery',
            'Herbal teas may support a calmer digestive recovery, but only when they are used as support rather than a substitute for hydration and basic caution. Here is how to fit them into a gentler recovery plan without overestimating their role.',
            '<ul><li>Tea can support comfort and digestive calm, but it is never the main answer when fluid loss matters most.</li><li>The biggest mistake is relying on tea alone while ignoring hydration and warning signs.</li><li>A smarter approach uses herbs as part of gentle recovery, not as a miracle repair tool.</li></ul>',
            [
                ['question' => 'Does tea make sense during diarrhea?', 'answer' => 'It can, as part of a gentler recovery and comfort routine, but not as a replacement for fluids and caution.'],
                ['question' => 'What matters more than tea?', 'answer' => 'Hydration, digestive rest and paying attention to how symptoms are changing.'],
                ['question' => 'Can aloe vera be part of support?', 'answer' => 'Sometimes, but only with care and attention to what the body actually tolerates.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Thinking “natural” automatically means enough for every digestive problem.'],
            ],
            'Tea for Diarrhea: When It Helps and When the Bigger Picture Matters More',
            'Learn how herbal tea may support diarrhea recovery and why fluids and caution still stay central.',
            'Tea for Diarrhea',
            [
                ['heading' => 'Why herbal tea can be supportive without being central', 'html' => '<p>Warm herbal support may make recovery feel calmer and more manageable, but the body still needs enough fluid and gentleness first. Tea works best when it respects its role rather than replacing more important priorities.</p>'],
                ['heading' => 'Why digestive recovery benefits from simplicity', 'html' => '<p>The gut often responds better to calm than to experimentation. That is why smaller, softer support choices usually fit recovery better than a long list of remedies used all at once.</p>'],
            ]
        ),
    ],
];
