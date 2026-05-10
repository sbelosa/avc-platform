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
    'key' => 'family-digestion-support-en-wave-1',
    'name' => 'Family, Digestion and Daily Support (EN) - wave 1',
    'notes' => 'Manual premium localized wave for family support, digestion, supplements and practical daily wellbeing topics.',
    'entries' => [
        $entry(
            574,
            'Children with ADHD: Calming Techniques, Nutrition and Support Without Quick Labels',
            'children-with-adhd-calming-techniques-nutrition-and-support',
            'Children with ADHD tend to do best when support becomes practical, gentle and repeatable rather than perfect. Here is how to think about calming, food choices and daily structure without turning the topic into one simplistic fix.',
            '<ul><li>Support for children with ADHD works best when routine, food, emotions and environment are viewed together.</li><li>The biggest mistake is looking for one trick that will solve focus and behaviour challenges overnight.</li><li>A smarter approach builds small repeatable rituals that help the child feel safer and more regulated.</li></ul>',
            [
                ['question' => 'What tends to help children with ADHD most?', 'answer' => 'Clear routine, calmer communication and support that repeats across the day.'],
                ['question' => 'Can nutrition solve everything on its own?', 'answer' => 'No. Nutrition may help, but it is only one part of wider support.'],
                ['question' => 'Why is calming so important?', 'answer' => 'Because a child usually cooperates better when they feel safer and less overwhelmed.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Changing too many things at once and expecting a sudden turnaround.'],
            ],
            'Children with ADHD: Building Support Through Routine, Food and Calmer Daily Rhythms',
            'Learn how to support children with ADHD through practical routines, calming tools and steadier daily structure.',
            'Children and ADHD',
            [
                ['heading' => 'Why practical support often beats big theory', 'html' => '<p>Families usually feel more relief when they find a few things they can repeat every day rather than chase perfect plans. Small structures often reduce chaos more effectively than constant searching for the next idea.</p>'],
                ['heading' => 'Why the child needs regulation, not pressure', 'html' => '<p>ADHD-related stress often becomes worse when the environment gets more reactive. Calmer structure and clearer expectations usually create a more useful starting point than repeated conflict.</p>'],
            ]
        ),
        $entry(
            583,
            'Vitamin D Toxicity: How to Recognize Excess Intake and Stay on the Safer Side',
            'vitamin-d-toxicity-how-to-recognize-excessive-intake-and-stay-safe',
            'Vitamin D is only helpful while it stays inside a sensible range. Here is how to recognize when supplementation becomes overuse and why more is not always better.',
            '<ul><li>Vitamin D can be valuable, but excessive use creates unnecessary risk.</li><li>The biggest mistake is taking high doses for long periods without a clear reason or any follow-up.</li><li>A smarter approach looks at need, context and safer long-term dosing.</li></ul>',
            [
                ['question' => 'What is vitamin D toxicity?', 'answer' => 'It is a problem linked to excessive intake of vitamin D supplements.'],
                ['question' => 'Why do people overdo it?', 'answer' => 'Because vitamin D has a positive reputation, so higher intake may start to feel automatically better.'],
                ['question' => 'How can someone stay safer?', 'answer' => 'By avoiding high-dose improvisation and keeping use tied to a real need.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Copying someone else’s protocol without checking whether it fits the same context.'],
            ],
            'Vitamin D Toxicity: When a Useful Supplement Turns Into Unnecessary Risk',
            'Understand how excessive vitamin D intake happens and how to stay on the safer side of supplementation.',
            'Vitamin D Toxicity',
            [
                ['heading' => 'Why more is not always more helpful', 'html' => '<p>Useful supplements often create a false sense of safety. Vitamin D works best when it matches an actual need, not when it becomes a bigger-and-bigger experiment.</p>'],
                ['heading' => 'Why context matters more than reputation', 'html' => '<p>A supplement can be helpful and still be easy to misuse. The real question is not whether vitamin D is good in general, but whether the amount and reason still make sense for the person using it.</p>'],
            ]
        ),
        $entry(
            586,
            'Best Time to Take Multivitamins: Morning, Evening or Simply With the Right Meal?',
            'best-time-to-take-multivitamins-morning-or-evening',
            'A multivitamin does not work better because it is taken at one perfect minute of the day, yet timing can affect tolerance and consistency. Here is how to choose a time that fits real life rather than a rigid rule.',
            '<ul><li>The best time for a multivitamin is the time that supports consistency and good tolerance.</li><li>The biggest mistake is chasing perfect timing while ignoring how the body actually feels with the product.</li><li>A smarter approach fits it into a meal and a rhythm that can be repeated easily.</li></ul>',
            [
                ['question' => 'Is morning always best?', 'answer' => 'Not always. Some people do better with the first main meal, while others prefer later in the day.'],
                ['question' => 'Why does taking it with food help?', 'answer' => 'Because it often improves tolerance and makes the habit easier to repeat.'],
                ['question' => 'What matters more than timing?', 'answer' => 'Consistency and whether the multivitamin fits the person’s routine without discomfort.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Dropping the habit because the time is not perfect instead of adapting it to daily life.'],
            ],
            'Multivitamins: How to Choose a Time You Can Actually Stick With',
            'See when to take a multivitamin and why real-life consistency often matters more than perfect timing.',
            'Multivitamin Timing',
            [
                ['heading' => 'Why perfect timing is often overrated', 'html' => '<p>People love exact answers, yet supplement habits usually succeed because they fit life rather than because they match a strict rule. A usable habit is almost always worth more than a theoretically ideal one.</p>'],
                ['heading' => 'Why the meal can matter more than the clock', 'html' => '<p>Food often improves tolerance and makes the ritual easier to remember. That is why the best answer is usually practical rather than obsessive.</p>'],
            ]
        ),
        $entry(
            587,
            'Natural Fertility Support: Maca, Vitex and Acupuncture Through a More Realistic Lens',
            'natural-fertility-support-maca-vitex-and-acupuncture',
            'Natural fertility support may have a place inside a wider plan, but it works best when it does not become a desperate search for one miracle answer. Here is how to assess these options with more calm and perspective.',
            '<ul><li>Supplements and complementary methods make the most sense as part of a broader fertility-support picture.</li><li>The biggest mistake is placing all hope in one herb, one treatment or one trend.</li><li>A smarter approach combines information, patience and more realistic expectations.</li></ul>',
            [
                ['question' => 'Why are maca and vitex often mentioned?', 'answer' => 'Because people frequently connect them with hormone balance and fertility support.'],
                ['question' => 'Is acupuncture a universal answer?', 'answer' => 'No. It may fit as a supportive method, but it is not the whole solution.'],
                ['question' => 'What matters most?', 'answer' => 'Realistic expectations and a broader view of health, stress and timing.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Spreading energy across too many methods without a clearer priority.'],
            ],
            'Natural Fertility Support: How to View Maca, Vitex and Acupuncture More Realistically',
            'Learn how to assess natural fertility support through a calmer, more informed and more realistic lens.',
            'Fertility Support',
            [
                ['heading' => 'Why calm decision-making matters in fertility support', 'html' => '<p>When emotions are high, almost every promising method can sound bigger than it is. Better decisions often come from slowing down and placing each tool inside a wider and more balanced plan.</p>'],
                ['heading' => 'Why no single method should carry the whole story', 'html' => '<p>Fertility is too complex for one herb or one practice to explain everything. The most grounded support usually connects several layers rather than searching for a magical fix.</p>'],
            ]
        ),
        $entry(
            589,
            'Stress in Children: How to Recognize the Signs and Calm Daily Overload Earlier',
            'stress-in-children-recognize-the-signs-and-calm-them-with-natural-methods',
            'Stress in children rarely looks exactly like adult stress, yet it often shows up through sleep, behaviour and emotional tension. Here is how to notice it earlier and bring more safety back into the child’s day.',
            '<ul><li>Stress in children often appears through irritability, changes in sleep and stronger emotional sensitivity.</li><li>The biggest mistake is dismissing those signals as simple disobedience or “just a phase.”</li><li>A smarter approach watches patterns and creates more rhythm, safety and connection.</li></ul>',
            [
                ['question' => 'How does stress usually show up in children?', 'answer' => 'Often through irritability, withdrawal, sleep changes or stronger emotional reactions.'],
                ['question' => 'Why is it easy to miss?', 'answer' => 'Because adults often misread stress signs as behaviour problems only.'],
                ['question' => 'What tends to help most?', 'answer' => 'A calmer rhythm, stronger connection and less daily overload.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Reacting only to the behaviour while missing the repeated source of tension.'],
            ],
            'Stress in Children: How to Notice It Before It Becomes a Daily Burden',
            'Understand how stress in children can show up and how calmer routines may help earlier than most people expect.',
            'Stress in Children',
            [
                ['heading' => 'Why children often show stress through behaviour', 'html' => '<p>Children do not always explain what feels heavy inside them. Their stress often becomes visible through behaviour, sleep or a changing emotional tone, which is why adults need to read patterns more carefully.</p>'],
                ['heading' => 'Why safety helps more than pressure', 'html' => '<p>When children feel overloaded, extra pressure rarely creates real calm. A more regulated environment usually gives the child a better chance to settle and recover.</p>'],
            ]
        ),
        $entry(
            592,
            'Heartburn During Pregnancy: Simpler Natural Relief That Creates Less Daily Chaos',
            'heartburn-during-pregnancy-natural-solutions-with-aloe-vera-for-relief',
            'Heartburn during pregnancy becomes exhausting because it returns at a time when the body is already more sensitive. Here is how to approach relief through food rhythm, gentler habits and a more realistic daily plan.',
            '<ul><li>Heartburn during pregnancy usually responds best to practical food and rhythm adjustments, not complicated tricks.</li><li>The biggest mistake is waiting until the discomfort is strong before changing anything at all.</li><li>A smarter approach uses smaller shifts that reduce digestive pressure through the day.</li></ul>',
            [
                ['question' => 'Why is heartburn so common during pregnancy?', 'answer' => 'Because body changes and pressure on digestion often increase as pregnancy progresses.'],
                ['question' => 'What often helps?', 'answer' => 'Smaller meals, a calmer pace of eating and fewer personal trigger foods.'],
                ['question' => 'Should everything be handled naturally at any cost?', 'answer' => 'No. Safety and realism still matter most.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Ignoring obvious patterns that show what repeatedly worsens the discomfort.'],
            ],
            'Pregnancy Heartburn: How to Reduce It Through Better Rhythm and Fewer Triggers',
            'Learn how to ease heartburn during pregnancy through simpler habits, calmer meals and a more practical daily rhythm.',
            'Pregnancy Heartburn',
            [
                ['heading' => 'Why practical adjustments usually help more than complex tricks', 'html' => '<p>Pregnancy is already demanding enough, which is why the most helpful solutions are often small and repeatable. Simpler meals and steadier rhythm usually beat complicated advice.</p>'],
                ['heading' => 'Why realism matters more than perfect control', 'html' => '<p>The goal is usually not to create a flawless digestive system but to reduce the daily burden. Practical improvement often matters more than chasing total elimination of symptoms.</p>'],
            ]
        ),
        $entry(
            630,
            'Natural Laxatives: Psyllium, Aloe Gel and Prunes Inside a Smarter Digestion Plan',
            'natural-laxatives-psyllium-aloe-gel-and-prunes',
            'With constipation and sluggish digestion, many people search for the fastest fix, and that is where frustration grows. Here is how to use natural laxatives as part of a wider digestive plan instead of treating them like a magic switch.',
            '<ul><li>Natural laxatives usually work best when they are combined with fluids, movement and a better meal rhythm.</li><li>The biggest mistake is chasing instant relief without changing what keeps the problem returning.</li><li>A smarter approach combines fibre, hydration and gentler support across days and weeks.</li></ul>',
            [
                ['question' => 'Why are psyllium and prunes so popular?', 'answer' => 'Because people connect them with fibre, softer stool and more natural relief.'],
                ['question' => 'Can aloe gel also help?', 'answer' => 'It may fit in some routines, but it is not the only factor that shapes digestion.'],
                ['question' => 'What matters alongside laxatives?', 'answer' => 'Fluid intake, movement and habits that help digestion stay active.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Taking a product while ignoring the wider cause of sluggish digestion.'],
            ],
            'Natural Laxatives: How to Use Them as Support Instead of the Whole Plan',
            'See how psyllium, aloe gel and prunes may help digestion when they are used inside a broader routine.',
            'Natural Laxatives',
            [
                ['heading' => 'Why constipation rarely changes from one tool alone', 'html' => '<p>Digestive rhythm usually reflects food, hydration, movement and stress together. That is why even helpful products work better when the whole daily pattern becomes more supportive.</p>'],
                ['heading' => 'Why a slower reset often works better than a quick fix', 'html' => '<p>People often want instant relief, but digestion usually becomes steadier through repetition rather than urgency. Gentler consistency often beats one-off reactions.</p>'],
            ]
        ),
        $entry(
            642,
            'Onion and Honey Syrup for Cough: When It Makes Sense and How to Use It Realistically',
            'onion-and-honey-syrup-for-cough-proven-recipe-and-tips',
            'Onion and honey syrup survives across generations because it can bring a feeling of warmth and throat comfort. Here is how to use it sensibly, without turning every home remedy into a total solution.',
            '<ul><li>Home syrup makes the most sense as a supportive ritual for the throat and a calmer recovery rhythm.</li><li>The biggest mistake is turning a folk remedy into the only plan when a cough lasts too long or grows stronger.</li><li>A smarter approach uses syrup for comfort while still watching the wider pattern of the symptom.</li></ul>',
            [
                ['question' => 'Why is this syrup so popular?', 'answer' => 'Because it combines warmth, tradition and a soothing effect for the throat.'],
                ['question' => 'Can it help everyone?', 'answer' => 'Not equally, but some people do find it comforting and supportive.'],
                ['question' => 'When does it make sense?', 'answer' => 'When it is used as support alongside rest, fluids and symptom awareness.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Relying on syrup alone when the bigger symptom picture needs more attention.'],
            ],
            'Onion and Honey Syrup: Where It Truly Helps the Throat and Where It Is Not Enough Alone',
            'Learn when onion and honey syrup for cough makes sense and how to use it as a reasonable home support tool.',
            'Onion and Honey Syrup',
            [
                ['heading' => 'Why traditional remedies still appeal', 'html' => '<p>Home recipes often survive because they offer comfort as much as action. That emotional and practical support can matter, as long as it is not confused with a complete answer to every cough.</p>'],
                ['heading' => 'Why comfort is useful but not universal', 'html' => '<p>Supportive rituals absolutely have a place, but they work best when paired with realistic observation. The throat may feel better while the person still needs to pay attention to the wider pattern.</p>'],
            ]
        ),
        $entry(
            653,
            'Alcohol and Coffee: How They Affect Digestion, Sleep and the Feeling of Immunity',
            'alcohol-and-coffee-how-they-affect-digestion-and-immunity',
            'Alcohol and coffee are often judged separately even though real life usually combines them inside the same pattern of stress, sleep and digestion. Here is how to understand their joint impact with less drama but also without ignoring body signals.',
            '<ul><li>Coffee and alcohol may increase digestive sensitivity, disturb sleep and reduce the feeling of recovery.</li><li>The biggest mistake is judging each habit separately while ignoring the combined effect across the whole day.</li><li>A smarter approach watches patterns, quantity and the moments when the body clearly stops coping well.</li></ul>',
            [
                ['question' => 'Why does this combination often disturb digestion?', 'answer' => 'Because both can place stress on the stomach and disrupt digestive rhythm.'],
                ['question' => 'How do they affect sleep?', 'answer' => 'They often reduce the quality of recovery even when total sleep time looks acceptable.'],
                ['question' => 'Is quantity the only issue?', 'answer' => 'No. Timing, stress and the overall daily pattern matter too.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Getting used to discomfort and no longer noticing what the body is signalling.'],
            ],
            'Alcohol and Coffee: How the Combination Shapes Digestion and Recovery',
            'Understand how alcohol and coffee can affect digestion, sleep and broader daily recovery when combined too often.',
            'Alcohol and Coffee',
            [
                ['heading' => 'Why the pattern matters more than the isolated habit', 'html' => '<p>Many people ask whether coffee or alcohol is worse, yet the more useful question is how the entire pattern feels in the body. Digestion and recovery usually respond to the combination, not only to one element alone.</p>'],
                ['heading' => 'Why body signals get easier to ignore over time', 'html' => '<p>Repeating a habit can normalize the discomfort it creates. That is why clearer observation often helps people see the real effect more honestly again.</p>'],
            ]
        ),
        $entry(
            658,
            'Stress in Adolescents: How Parents Can Help Without Adding More Pressure',
            'stress-in-adolescents-7-steps-how-parents-can-help',
            'Adolescent stress often looks like withdrawal, irritability or resistance, yet beneath that there is often overload that needs more understanding than control. Here is how parents can help through relationship and rhythm instead of only rules and pressure.',
            '<ul><li>Teenagers often respond best to support that respects autonomy while still offering a stable framework.</li><li>The biggest mistake is reacting to stress with more criticism, control or daily pressure.</li><li>A smarter approach builds conversation, trust and small changes that reduce overload.</li></ul>',
            [
                ['question' => 'How does stress in adolescents often show up?', 'answer' => 'Through withdrawal, mood changes, sleep problems or stronger irritability.'],
                ['question' => 'What do parents often get wrong?', 'answer' => 'They increase pressure at the exact moment the teenager needs calmer support.'],
                ['question' => 'How can parents help without suffocating?', 'answer' => 'By listening more, keeping calm boundaries and reducing daily chaos.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Mistaking stress signs for pure disobedience and missing the bigger picture.'],
            ],
            'Stress in Adolescents: How to Help Through Trust Instead of More Control',
            'See how parents can support stressed adolescents through relationship, rhythm and less pressure in daily life.',
            'Stress in Adolescents',
            [
                ['heading' => 'Why teenagers often need a steadier relationship, not a louder response', 'html' => '<p>Stress in adolescence easily gets misread as attitude. The more useful move is often to create a calmer relationship space where the young person feels seen instead of managed harder.</p>'],
                ['heading' => 'Why trust makes support more effective', 'html' => '<p>Teenagers are more likely to respond when they do not feel cornered. Support usually works better when boundaries stay present but the overall tone becomes calmer and more respectful.</p>'],
            ]
        ),
    ],
];
