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
    'key' => 'vitality-nutrition-forever-en-wave-1',
    'name' => 'Vitality, Nutrition and Forever Guides (EN) - wave 1',
    'notes' => 'A larger localized editorial wave for vitality, nutrition habits, migraines, seasonal support and selected Forever guides.',
    'entries' => [
        $entry(
            141,
            'en',
            'Aloe Vera and the Body: Where Hydrolat and Gentle Support Truly Fit',
            'aloe-vera-and-the-body-hydrolat-form-for-health-and-skin-care',
            'Aloe vera is often discussed both inside and outside the body, yet it helps most when the product form and real expectation are clear. Here is how to think about aloe through skin comfort, routine and more practical daily use.',
            '<ul><li>Aloe vera works best when it has a clear place in the routine rather than being treated as a universal answer.</li><li>The biggest mistake is mixing up product forms and purposes without understanding what each one is actually for.</li><li>A smarter approach chooses the formula according to skin, need and real-life use.</li></ul>',
            [
                ['question' => 'Is every aloe product the same?', 'answer' => 'No. Differences in composition, processing and purpose can change the whole experience.'],
                ['question' => 'When does aloe vera make the most sense?', 'answer' => 'When the skin needs gentler support, freshness or a routine that is simple enough to repeat.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Expecting aloe to do the work of a wider care, nutrition or recovery plan.'],
                ['question' => 'How can someone choose more wisely?', 'answer' => 'By looking at purpose, formula and how well the product fits the actual routine.'],
            ],
            'Aloe Vera and the Body: Where It Helps and Where Marketing Exaggerates',
            'Learn where aloe vera makes real sense for skin comfort and daily care and how to use it with better expectations.',
            'Aloe and the Body',
            [
                ['heading' => 'Why aloe needs a clear role to be useful', 'html' => '<p>Aloe vera becomes far more helpful when people know exactly why they are using it. The most effective routines usually give aloe a focused, realistic role instead of expecting it to solve every problem at once.</p>'],
                ['heading' => 'Why product form matters more than the buzzword', 'html' => '<p>Different aloe formats can serve very different purposes. That is why reading the product through its function is often more useful than relying on the aloe label alone.</p>'],
            ]
        ),
        $entry(
            150,
            'en',
            'How to Eat 5 Servings of Fruit and Vegetables a Day Without Feeling on a Diet',
            'how-to-eat-5-servings-of-fruit-and-vegetables-a-day-simple-daily-tips',
            'Five servings of fruit and vegetables sounds easy until shopping, work and real meal prep take over. Here is how to make the habit cheaper, lighter and realistic enough for normal weekdays.',
            '<ul><li>More fruit and vegetables usually enter the diet through small repeated habits, not through perfect meal plans.</li><li>The biggest mistake is trying to fix everything at once and then giving up as soon as the week gets busy.</li><li>A smarter approach creates a few predictable points in the day where produce appears naturally.</li></ul>',
            [
                ['question' => 'What counts as a serving?', 'answer' => 'A serving can be a handful of vegetables, a piece of fruit or another simple practical portion size.'],
                ['question' => 'Does everything have to be fresh?', 'answer' => 'No. Frozen and easy-prep options often make the routine much more sustainable.'],
                ['question' => 'How can this work for families too?', 'answer' => 'Through repeatable combinations that are easy to serve and do not create extra cooking stress.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Buying too much with no plan and then wasting it later in the week.'],
            ],
            '5 Servings of Fruit and Vegetables a Day: How to Make It Easier and More Realistic',
            'See how to reach 5 daily servings of fruit and vegetables through practical habits instead of diet rigidity.',
            '5 Servings a Day',
            [
                ['heading' => 'Why consistency matters more than ambition here', 'html' => '<p>People rarely struggle because they do not know fruit and vegetables are healthy. The real challenge is making them easy enough to repeat when time, budget and energy are limited.</p>'],
                ['heading' => 'Why structure reduces waste and frustration', 'html' => '<p>Simple planning often makes produce easier to use and less likely to be forgotten. A few reliable meal moments usually work better than grand intentions without a system.</p>'],
            ]
        ),
        $entry(
            151,
            'en',
            'Recharge Your Batteries: Better Ways to Fight Chronic Fatigue',
            'recharge-your-batteries-the-best-ways-to-fight-chronic-fatigue',
            'Chronic fatigue rarely comes from one single cause, and it almost never disappears through one quick fix. Here is how to spot deeper patterns of exhaustion and where food, rhythm and supportive routines may truly help.',
            '<ul><li>Chronic fatigue usually reflects several overlapping issues rather than one missing piece.</li><li>The biggest mistake is treating exhaustion only with caffeine and motivation bursts.</li><li>A smarter approach looks at sleep, stress, meals and how much genuine recovery the day allows.</li></ul>',
            [
                ['question' => 'Why does fatigue keep returning?', 'answer' => 'Because the cause is often a mix of sleep, stress, food patterns, low recovery and ongoing life pressure.'],
                ['question' => 'Do supplements help?', 'answer' => 'Sometimes, but usually only after the core patterns and causes are understood better.'],
                ['question' => 'What should be observed first?', 'answer' => 'Sleep, energy swings, food rhythm, movement and signs that the body never fully recovers.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Mistaking deep exhaustion for poor discipline and pushing through for too long.'],
            ],
            'Chronic Fatigue: How to Recover Energy Without Leaning on Quick Fixes',
            'Understand the bigger patterns behind chronic fatigue and how to rebuild energy through more sustainable changes.',
            'Chronic Fatigue',
            [
                ['heading' => 'Why fatigue is usually a pattern problem', 'html' => '<p>Many people search for a single explanation, yet long-term fatigue often grows from several smaller strains working together. That is why better energy usually comes from pattern recognition rather than one magic solution.</p>'],
                ['heading' => 'Why recovery has to become part of the system', 'html' => '<p>Energy rarely returns just because people want it badly enough. The body usually needs real space for sleep, rest, nutrition and less internal friction before it begins to feel truly recharged again.</p>'],
            ]
        ),
        $entry(
            153,
            'en',
            'Cooking with Aloe Vera: 3 Unusual Recipes That Still Make Sense',
            'cooking-with-aloe-vera-3-unusual-recipes-for-curious-food-lovers',
            'Cooking with aloe vera can sound exotic or unnecessarily complicated, yet it can be interesting when used gently and with a clear culinary reason. Here are three recipe directions that combine curiosity, flavour and practicality.',
            '<ul><li>Aloe vera in the kitchen works best when it complements a recipe instead of trying to dominate it.</li><li>The biggest mistake is overusing it or forcing it into meals where the taste and texture make little sense.</li><li>A smarter approach keeps the recipes simple and gives aloe a supporting role.</li></ul>',
            [
                ['question' => 'Can aloe vera really be used in cooking?', 'answer' => 'Yes, when used in an appropriate form and in recipes where it suits the texture and idea.'],
                ['question' => 'Which dishes work best?', 'answer' => 'Usually lighter combinations and fresher recipes where aloe can stay subtle.'],
                ['question' => 'What is the most common mistake?', 'answer' => 'Using too much and turning an interesting ingredient into the whole dish.'],
                ['question' => 'Do the recipes need to be complicated?', 'answer' => 'No. The best ones are still simple enough for a normal week.'],
            ],
            'Cooking with Aloe Vera: 3 Recipes That Stay Interesting Without Going Too Far',
            'Try three simpler directions for cooking with aloe vera without losing flavour or practicality.',
            'Cooking with Aloe',
            [
                ['heading' => 'Why aloe works better as a support ingredient', 'html' => '<p>In food, aloe usually makes the most sense when it adds a subtle note rather than becoming the entire concept. That often keeps both flavour and texture more enjoyable.</p>'],
                ['heading' => 'Why curiosity still needs restraint', 'html' => '<p>Experimenting in the kitchen can be fun, but the most useful ideas are the ones people can repeat. A calmer approach often makes unusual ingredients far more practical.</p>'],
            ]
        ),
        $entry(
            161,
            'en',
            'Best Forever Starter Packs: How to Choose the One That Really Fits You',
            'best-forever-starter-packs-which-one-is-perfect-for-you',
            'A starter pack only makes sense when it matches your goal, budget and the habits you will actually keep. Here is how to choose a Forever pack without overbuying or feeling pressured to start with everything at once.',
            '<ul><li>The best starter pack is not the biggest one but the one you will use consistently enough to matter.</li><li>The biggest mistake is choosing according to someone else’s excitement instead of your own goal and routine.</li><li>A smarter approach chooses based on energy, digestion, body care or a simpler entry into the brand.</li></ul>',
            [
                ['question' => 'How should a starter pack be chosen?', 'answer' => 'By real goal, budget and routine rather than by pack size alone.'],
                ['question' => 'Do beginners need multiple products at once?', 'answer' => 'Usually no. A smaller, clearer start often works better than overload.'],
                ['question' => 'Is one pack right for everyone?', 'answer' => 'No. A useful pack depends on the person, the aim and what feels realistic to use.'],
                ['question' => 'What is a common beginner mistake?', 'answer' => 'Buying too much and using only part of it consistently.'],
            ],
            'Forever Starter Packs: How to Choose More Wisely Without Buying Too Much',
            'Learn how to choose a Forever starter pack based on your real goal, budget and routine instead of hype.',
            'Starter Packs',
            [
                ['heading' => 'Why the best start is usually the clearest start', 'html' => '<p>Beginners often feel pressure to choose a bigger package in order to “do it right.” Yet long-term use usually depends more on clarity and fit than on how many products arrive on day one.</p>'],
                ['heading' => 'Why habits matter more than bundle size', 'html' => '<p>A product only creates value when it becomes part of real life. That is why the best starter pack is usually the one that the user can actually repeat and understand.</p>'],
            ]
        ),
        $entry(
            162,
            'en',
            'Collagen and Forever Marine Collagen: Where the Benefits Are Realistic',
            'collagen-and-forever-marine-collagen-benefits-for-skin-and-joints',
            'Collagen is one of the most popular support products for skin and joints, yet expectations often grow far beyond what a supplement can really do. Here is how to look at Marine Collagen through routine, consistency and wider body support rather than hype.',
            '<ul><li>Collagen may make sense as part of a bigger skin, joint and connective tissue support routine.</li><li>The biggest mistake is expecting dramatic change without time, consistency and basic body care.</li><li>A smarter approach combines collagen with hydration, protein intake and habits that truly protect skin and joints.</li></ul>',
            [
                ['question' => 'Can collagen help the skin?', 'answer' => 'It may support the routine, especially when used consistently and alongside other good habits.'],
                ['question' => 'Does it make sense for joints too?', 'answer' => 'It can, especially when it is part of a broader recovery and movement plan.'],
                ['question' => 'When do results usually show?', 'answer' => 'Not instantly. Time and consistency matter more than short bursts of use.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Expecting collagen to solve what poor sleep, low protein and weak body-care habits are still hurting.'],
            ],
            'Marine Collagen: Realistic Expectations for Skin, Joints and Daily Use',
            'See where collagen makes sense for skin and joints and how to use it with more realistic expectations.',
            'Marine Collagen',
            [
                ['heading' => 'Why collagen works best inside a wider routine', 'html' => '<p>Supplements often get too much credit or blame on their own. In practice, collagen tends to work best when hydration, protein intake and everyday self-care already support the tissues people want to help.</p>'],
                ['heading' => 'Why patience matters more than hype', 'html' => '<p>Collagen support is rarely dramatic overnight. It usually makes more sense to treat it as a steady part of a longer routine rather than an instant beauty or joint fix.</p>'],
            ]
        ),
        $entry(
            163,
            'en',
            'Forever and Athletes: Can Professionals Use FLP Supplements Smartly?',
            'forever-and-athletes-can-professionals-use-flp-supplements',
            'Athletes need more clarity and safety than the average supplement buyer, because every product has to make sense in a real training environment. Here is how to view Forever supplements through recovery, routine and more responsible selection.',
            '<ul><li>Sports supplementation only makes sense when it addresses a real need and fits an already solid training and nutrition base.</li><li>The biggest mistake is letting supplements become the main strategy instead of a supporting tool.</li><li>A smarter approach uses fewer products, clearer purpose and safer routines.</li></ul>',
            [
                ['question' => 'Do athletes need special supplements?', 'answer' => 'Sometimes, but only when a clear need exists and the basics are already strong.'],
                ['question' => 'Are all products useful for every athlete?', 'answer' => 'No. Needs depend on sport type, training load and the actual performance goal.'],
                ['question' => 'What matters more than supplements?', 'answer' => 'Training quality, recovery, sleep and basic nutrition.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Copying someone else’s supplement routine without understanding your own needs.'],
            ],
            'Forever and Athletes: How to Choose FLP Supplements More Intelligently',
            'Learn how athletes can use Forever supplements more responsibly through clearer purpose and stronger basics.',
            'Forever and Sport',
            [
                ['heading' => 'Why athletes need more precision, not more products', 'html' => '<p>Performance routines become stronger when every element has a purpose. That is why athletes often benefit more from careful selection than from adding multiple products at once.</p>'],
                ['heading' => 'Why recovery still leads the whole picture', 'html' => '<p>Even good supplements cannot compensate for weak recovery. Training adaptation usually depends most on sleep, food, load management and how well the body can absorb the work already being done.</p>'],
            ]
        ),
        $entry(
            165,
            'en',
            'Natural Relief for Migraines: Aloe, Magnesium and Riboflavin in a Smarter Plan',
            'natural-relief-for-migraines-aloe-magnesium-and-riboflavin',
            'Migraine support works best when triggers and routine are understood, not when everything “natural” gets tested at once. Here is how magnesium, riboflavin and daily rhythm may become part of a more structured support plan.',
            '<ul><li>Natural migraine support helps most when it is linked to triggers, sleep rhythm and stress load.</li><li>The biggest mistake is reacting only to the attack without working on the pattern leading up to it.</li><li>A smarter approach combines prevention, symptom tracking and more targeted support.</li></ul>',
            [
                ['question' => 'Can magnesium and riboflavin help?', 'answer' => 'For some people they can be useful supportive tools, especially when used consistently and with purpose.'],
                ['question' => 'Why is migraine tracking important?', 'answer' => 'Because support becomes more useful when the real triggers and patterns are clearer.'],
                ['question' => 'Is it enough to react only when migraine begins?', 'answer' => 'Usually not. Prevention and rhythm often matter just as much as acute relief.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Changing too many things at once and then not knowing what actually helped.'],
            ],
            'Migraine Support: Magnesium, Riboflavin and a Smarter Preventive Plan',
            'Understand how migraines can be supported more naturally through tracking, routine and targeted support tools.',
            'Migraine Support',
            [
                ['heading' => 'Why migraine support is about pattern recognition', 'html' => '<p>Many people focus only on the pain episode itself, yet the most useful migraine support often begins before that point. Tracking sleep, food, stress and timing often reveals why certain days become more vulnerable than others.</p>'],
                ['heading' => 'Why less chaos creates better prevention', 'html' => '<p>When routines are calmer and support choices are more targeted, migraines often become easier to understand. That clarity makes prevention much more realistic than random experimentation.</p>'],
            ]
        ),
        $entry(
            166,
            'en',
            'Vitamin D and K2 in Winter: Smarter Support for Bones and Seasonal Balance',
            'vitamin-d-and-k2-in-winter-how-they-help-bones-and-why-theyre-important',
            'Winter often raises questions about vitamin D, yet without context people easily slip into random or excessive supplement use. Here is how to think about D and K2 through bones, season and real body needs instead of seasonal hype.',
            '<ul><li>Vitamin D often matters more in winter, but supplementation only makes sense when it fits a real need and context.</li><li>The biggest mistake is taking high doses without a plan or understanding the wider picture.</li><li>A smarter approach looks at D, K2, food intake and actual body requirements together.</li></ul>',
            [
                ['question' => 'Why is vitamin D more relevant in winter?', 'answer' => 'Because sunlight exposure is often lower, making it easier for levels to drop over time.'],
                ['question' => 'Why is K2 mentioned too?', 'answer' => 'Because it is often discussed as part of the wider conversation around bone-related nutrient balance.'],
                ['question' => 'Should everyone use the same dose?', 'answer' => 'No. Needs vary by person, food pattern, sun exposure and sometimes lab context.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Copying another person’s winter supplement routine without checking whether it fits your own situation.'],
            ],
            'Vitamin D and K2 in Winter: How to Use Them More Intelligently',
            'See where vitamin D and K2 make sense in winter and how to avoid the most common seasonal supplement mistakes.',
            'Vitamin D and K2',
            [
                ['heading' => 'Why winter changes the context', 'html' => '<p>Season matters because sunlight exposure often shifts dramatically. That change alone is enough to make vitamin D a more relevant question for many people during colder months.</p>'],
                ['heading' => 'Why context protects better than copying trends', 'html' => '<p>Supplement routines can look simple online, but they are rarely universal. The smarter decision usually depends on the individual body, food pattern and what the season actually changes for that person.</p>'],
            ]
        ),
        $entry(
            170,
            'en',
            'Happiness Hormones: How Food, Movement and Daily Rhythm Support Mood',
            'happiness-hormones-how-to-boost-them-with-diet-exercise-and-aloe-vera',
            'So-called happiness hormones sound simple until it becomes clear how deeply they connect with sleep, movement, food and connection. Here is how to support serotonin, dopamine and related pathways without chasing unrealistic instant fixes.',
            '<ul><li>Mood and inner energy depend on several systems, not on one simple “happiness hormone.”</li><li>The biggest mistake is chasing a fast mood boost while ignoring the basic rhythm of daily life.</li><li>A smarter approach builds small sources of wellbeing through light, food, movement, recovery and human connection.</li></ul>',
            [
                ['question' => 'Can food affect mood?', 'answer' => 'Yes, especially as part of a wider daily rhythm that supports steadier energy and better digestion.'],
                ['question' => 'How important is movement?', 'answer' => 'Very important, because regular movement often supports both the nervous system and mental freshness.'],
                ['question' => 'Is there one supplement for happiness?', 'answer' => 'No. Mood reflects several connected factors rather than one isolated formula.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Ignoring sleep, light, stress and daily rhythm while expecting mood to improve from motivation alone.'],
            ],
            'Happiness Hormones: What Actually Supports Mood Through Food and Daily Life',
            'Understand how mood can be supported through food, movement, sleep and small repeatable wellbeing habits.',
            'Happiness Hormones',
            [
                ['heading' => 'Why mood is bigger than one chemical label', 'html' => '<p>People like simple explanations, but emotional wellbeing is built from multiple overlapping systems. That is why support tends to work best when the whole rhythm of life becomes more supportive, not just one part.</p>'],
                ['heading' => 'Why small daily wins often help most', 'html' => '<p>Light exposure, movement, nourishing food and better rest may sound basic, yet they often shape mood much more reliably than the search for a dramatic emotional shortcut.</p>'],
            ]
        ),
    ],
];
