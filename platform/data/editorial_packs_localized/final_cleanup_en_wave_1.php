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
    'key' => 'final-cleanup-en-wave-1',
    'name' => 'Final Cleanup (EN) - wave 1',
    'notes' => 'Manual premium localized cleanup for the last four remaining HR articles.',
    'entries' => [
        $entry(
            629,
            'Vitamin D: Strong Bones, Better Immunity and a Brighter Mood Through a More Realistic Lens',
            'vitamin-d-strong-bones-better-immunity-and-positive-mood',
            'Vitamin D is often treated like a solution for everything, yet its real value only shows up when it is viewed through bones, immunity, mood and real-life context. Here is how to understand it without overclaiming.',
            '<ul><li>Vitamin D can matter for bones, immunity and a wider sense of vitality.</li><li>The biggest mistake is expecting one nutrient to compensate for poor sleep, weak food choices and too little movement.</li><li>A smarter approach looks at sunlight, food and reasonable supplementation inside a bigger health picture.</li></ul>',
            [
                ['question' => 'Why is vitamin D discussed so often?', 'answer' => 'Because it is linked to bone health, immunity and wider feelings of vitality.'],
                ['question' => 'Can it affect mood?', 'answer' => 'It can be one factor, but it is never the only explanation for good or low mood.'],
                ['question' => 'What decides whether supplementation makes sense?', 'answer' => 'Season, sunlight exposure, food patterns and actual individual need.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Turning vitamin D into a universal explanation for every energy or immunity issue.'],
            ],
            'Vitamin D: How to View Bones, Immunity and Mood Support Without Overstatement',
            'Learn how vitamin D may support bones, immunity and mood when viewed through a wider and more realistic health context.',
            'Vitamin D and Vitality',
            [
                ['heading' => 'Why vitamin D sounds bigger than it should', 'html' => '<p>Because vitamin D is connected to several important body systems, it can quickly start sounding like a total answer. In practice, it makes more sense when it is read as one useful piece inside a much wider health picture.</p>'],
                ['heading' => 'Why context matters more than reputation', 'html' => '<p>A nutrient can be important and still easy to overuse in conversation. The most useful reading usually comes from matching vitamin D to the person’s season, routine and actual need.</p>'],
            ]
        ),
        $entry(
            827,
            'Herbal Teas: When to Drink Them and How Aloe Gel May Fit as Extra Support',
            'herbal-teas-when-to-drink-them-and-how-to-pair-with-aloe-gel',
            'Herbal teas have a long tradition and genuine practical value, but they help most when used with purpose and moderation. Here is how to choose the right moment for tea and where aloe gel may fit logically into a wider routine.',
            '<ul><li>Herbal teas work best when they match the time of day, the symptom and the reason for choosing them.</li><li>The biggest mistake is expecting one herbal blend to solve everything and replace every other useful habit.</li><li>A smarter approach treats tea as part of a calmer routine rather than as the entire plan.</li></ul>',
            [
                ['question' => 'When is the best time to drink herbal tea?', 'answer' => 'That depends on the tea, the goal and the part of the day when you want more calm, warmth or support.'],
                ['question' => 'Can herbal teas help digestion and the throat?', 'answer' => 'They often can bring comfort and relief as part of a wider routine.'],
                ['question' => 'Where might aloe gel fit?', 'answer' => 'As added support inside a broader routine, not as a total substitute for everything else.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Turning tea into the only support plan instead of one simple part of daily care.'],
            ],
            'Herbal Teas: When They Make Sense and Where Aloe Gel Fits as Extra Support',
            'See how to use herbal teas more thoughtfully and where aloe gel may make sense as extra support within a calm routine.',
            'Herbal Teas',
            [
                ['heading' => 'Why timing changes the value of tea', 'html' => '<p>The same tea can feel completely different depending on whether it is used for settling in the evening, warming digestion or softening a rough throat. That is why timing often matters more than people expect.</p>'],
                ['heading' => 'Why support works best in layers', 'html' => '<p>Simple routines are often strongest when several small supportive things work together. Tea can offer one layer of comfort, while another product or habit may support the rest of the picture.</p>'],
            ]
        ),
        $entry(
            837,
            'Zumba, Pilates or CrossFit: How to Choose the Group Training Style That Truly Fits You',
            'zumba-pilates-or-crossfit-how-to-choose-the-best-group-workout-for-you',
            'The best group workout is rarely the one that looks most exciting at first. Here is how to choose between Zumba, Pilates and CrossFit through your goal, your body and the kind of motivation you can actually sustain.',
            '<ul><li>The right group workout is the one that fits your body, your rhythm and your real motivation style.</li><li>The biggest mistake is choosing a trend instead of choosing what you can keep returning to.</li><li>A smarter approach looks at the goal: fun, strength, mobility, energy or social motivation.</li></ul>',
            [
                ['question' => 'Is one group workout objectively the best?', 'answer' => 'No. The best option depends on your goal, body and how you stay motivated.'],
                ['question' => 'Who often fits Zumba well?', 'answer' => 'People who want more rhythm, fun and a lighter entry into regular movement.'],
                ['question' => 'Who may prefer Pilates?', 'answer' => 'People who want more control, stability, mobility and breath-based work.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Choosing the training that looks impressive but does not suit you enough to last.'],
            ],
            'Zumba, Pilates or CrossFit: How to Choose the Group Workout You Can Actually Sustain',
            'Learn how to choose between Zumba, Pilates and CrossFit according to your goal, body and motivation style.',
            'Group Training',
            [
                ['heading' => 'Why excitement is not the same as fit', 'html' => '<p>A workout can look inspiring and still be the wrong match. Long-term progress usually comes from what feels repeatable rather than from what creates the biggest first impression.</p>'],
                ['heading' => 'Why training style should match the person', 'html' => '<p>People respond differently to structure, intensity and group energy. The better the match between the class and the person, the easier it becomes to stay consistent.</p>'],
            ]
        ),
        $entry(
            857,
            'Zero-Waste Cooking: How to Use Roots, Leaves and Stems Without Overcomplicating the Kitchen',
            'zero-waste-cooking-how-to-use-roots-leaves-and-stems',
            'Zero-waste cooking is not only an environmental idea but also a practical way to get more flavour and value from the same groceries. Here is how to use roots, stems and leaves without turning the kitchen into another stressful project.',
            '<ul><li>Zero-waste cooking works best when it stays simple, practical and realistic for daily life.</li><li>The biggest mistake is turning the idea into a perfectionist challenge nobody wants to maintain.</li><li>A smarter approach uses small habits like stocks, pestos, roasting and better storage of plant parts.</li></ul>',
            [
                ['question' => 'What can be used beyond the main part of the vegetable?', 'answer' => 'Leaves, stems, roots and even peels can often have real culinary value.'],
                ['question' => 'Does zero-waste cooking need to be complicated?', 'answer' => 'No. It usually works best through a few easy habits rather than a big kitchen overhaul.'],
                ['question' => 'Why is it useful beyond ecology?', 'answer' => 'Because it can create more flavour, less food waste and better value from the same shopping trip.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Trying to save absolutely everything and making cooking harder than it needs to be.'],
            ],
            'Zero-Waste Cooking: How to Use More of the Plant Without Extra Kitchen Stress',
            'Discover how zero-waste cooking can become simpler, tastier and more practical in everyday home cooking.',
            'Zero-Waste Cooking',
            [
                ['heading' => 'Why small habits matter more than perfect sustainability', 'html' => '<p>Most people do not need a total lifestyle reinvention to waste less food. A few repeatable kitchen habits usually create much more real change than a perfect zero-waste identity.</p>'],
                ['heading' => 'Why flavour and savings help the habit stick', 'html' => '<p>People are far more likely to keep a habit when it tastes good and feels useful. Zero-waste cooking often becomes easier to sustain when it clearly improves both the meal and the shopping value.</p>'],
            ]
        ),
    ],
];
