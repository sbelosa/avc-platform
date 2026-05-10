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
    'key' => 'aloe-wellness-habits-en-wave-1',
    'name' => 'Aloe, Wellness and Habits (EN) - wave 1',
    'notes' => 'Manual premium localized wave for aloe themes, performance, circulation, informed product choices and mental habit building.',
    'entries' => [
        $entry(
            569,
            'The Impact of Music on Performance: How to Build a Playlist That Truly Lifts Energy',
            'the-impact-of-music-on-performance-create-an-energy-boosting-playlist',
            'Music can be a powerful tool for focus, training and mood, but only when it is used on purpose instead of as random background noise. Here is how to build playlists that support performance rather than distract from it.',
            '<ul><li>Music can support focus, rhythm and energy when it matches the task and the body state.</li><li>The biggest mistake is using the same music for every kind of work and expecting the same effect.</li><li>A smarter approach builds playlists by purpose: deep work, training, recovery or energy lift.</li></ul>',
            [
                ['question' => 'Can music really improve performance?', 'answer' => 'It can help through rhythm, motivation and easier maintenance of focus or energy.'],
                ['question' => 'Why does the same playlist not always work?', 'answer' => 'Because different tasks and different body states usually need a different sound environment.'],
                ['question' => 'What makes a playlist useful?', 'answer' => 'Clear purpose, steady tone and songs that support concentration instead of interrupting it.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Playing music automatically without checking whether it helps or distracts.'],
            ],
            'Music and Performance: How to Build a Playlist That Actually Works for You',
            'Learn how to use music for better focus, training and energy by building playlists around clear performance goals.',
            'Music and Performance',
            [
                ['heading' => 'Why the right sound environment changes the task', 'html' => '<p>Music is not neutral. The same playlist can sharpen one activity and completely interrupt another, which is why intention matters more than the simple act of pressing play.</p>'],
                ['heading' => 'Why playlists work best when they have a job', 'html' => '<p>People often make music choices by taste alone, but performance support usually improves when the playlist is designed around one job: focus, motion, recovery or energy.</p>'],
            ]
        ),
        $entry(
            584,
            'Aloe Vera vs. Agave: What the Difference Is and Where Most Confusion Starts',
            'aloe-vera-vs-agave-whats-the-difference-and-which-is-the-better-choice',
            'Aloe vera and agave are often mixed together in wellness and product content even though they represent different plants, stories and uses. Here is how to separate them more clearly and avoid the usual confusion.',
            '<ul><li>Aloe vera and agave are not the same plant and do not play the same practical role.</li><li>The biggest mistake is confusing visual similarity with the same functional value.</li><li>A smarter approach separates plant identity, product use and the reason each plant is being discussed.</li></ul>',
            [
                ['question' => 'Are aloe vera and agave the same?', 'answer' => 'No. They are different plants with different practical uses and meanings.'],
                ['question' => 'Why do people confuse them?', 'answer' => 'Because they can look visually similar and are sometimes placed in the same broad “natural” conversation.'],
                ['question' => 'Where does the difference matter most?', 'answer' => 'When evaluating ingredients, products and what function a plant is actually expected to serve.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Assuming that what is true for aloe must also be true for agave.'],
            ],
            'Aloe Vera or Agave: How to Tell Them Apart and Avoid Common Confusion',
            'Understand the real difference between aloe vera and agave and where the most common misunderstandings begin.',
            'Aloe Vera vs Agave',
            [
                ['heading' => 'Why visual similarity creates easy myths', 'html' => '<p>People often connect plants by appearance first, which can quickly create confusion in natural-product conversations. A clearer distinction usually starts with what each plant is actually used for rather than how it looks.</p>'],
                ['heading' => 'Why ingredient context matters', 'html' => '<p>Natural language becomes much more useful when it is specific. The more clearly we understand a plant’s role in products or routines, the less likely we are to confuse one story with another.</p>'],
            ]
        ),
        $entry(
            588,
            'Emotional Intelligence: How to Develop Self-Awareness Without Stopping at Theory',
            'emotional-intelligence-develop-self-awareness-and-master-emotion',
            'Emotional intelligence is not just a nice concept for personal growth but a practical set of skills that matters in relationships, pressure and self-regulation. Here is how to approach it in a way that actually changes behaviour.',
            '<ul><li>Emotional intelligence matters most when it improves relationships, boundaries and daily reactions under pressure.</li><li>The biggest mistake is staying at the level of theory or personality language without practical application.</li><li>A smarter approach builds self-awareness, better emotional language and more deliberate responses.</li></ul>',
            [
                ['question' => 'What is emotional intelligence in practice?', 'answer' => 'It is the ability to understand emotions better and respond to them more skillfully.'],
                ['question' => 'Can it be developed?', 'answer' => 'Yes, through observation, reflection and more conscious communication.'],
                ['question' => 'Why is self-awareness so central?', 'answer' => 'Because without it, emotions influence decisions long before they are clearly understood.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Treating emotional intelligence as a label rather than a trainable everyday skill.'],
            ],
            'Emotional Intelligence: How to Turn It into a Practical Everyday Skill',
            'Learn how to develop emotional intelligence through self-awareness, better communication and steadier responses under pressure.',
            'Emotional Intelligence',
            [
                ['heading' => 'Why emotional skill matters more in real situations than in theory', 'html' => '<p>Most people understand emotional concepts long before they can actually use them well. The real shift usually happens when awareness enters difficult conversations, pressure and repeated daily patterns.</p>'],
                ['heading' => 'Why better language changes behaviour', 'html' => '<p>When people can name emotions more precisely, they are often less trapped by them. Clearer language usually creates more space for choice and less automatic reaction.</p>'],
            ]
        ),
        $entry(
            591,
            'Liver and Alcohol: How to Think About Recovery Without Quick Detox Myths',
            'liver-and-alcohol-recovery-steps-and-how-aloe-vera-helps',
            'The liver often enters the conversation only after a period of excess, which is exactly when marketing begins offering miracle recovery stories. Here is how to think about recovery through rhythm, hydration and realistic support instead of instant-detox language.',
            '<ul><li>Recovery after alcohol depends most on time, rest, hydration and calmer routine rather than one miracle product.</li><li>The biggest mistake is searching for instant liver cleansing without changing the pattern that created the strain.</li><li>A smarter approach uses simple recovery steps and keeps expectations realistic.</li></ul>',
            [
                ['question' => 'Can the liver recover?', 'answer' => 'Often yes, but that depends on behaviour patterns, time and broader daily support.'],
                ['question' => 'Is there a quick detox that fixes everything?', 'answer' => 'No. Time, rest and meaningful changes in habits usually matter much more.'],
                ['question' => 'Where might aloe fit?', 'answer' => 'Only as one small part of a wider routine, not as the main answer.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Repeating the same pattern while expecting a product to erase the consequences.'],
            ],
            'Liver and Alcohol: How to Think About Recovery Without Instant Detox Illusions',
            'Discover how to support recovery after alcohol through more realistic steps instead of miracle detox claims.',
            'Liver and Alcohol',
            [
                ['heading' => 'Why recovery stories attract easy exaggeration', 'html' => '<p>People naturally want a fast repair after a heavy period, which is why detox marketing often sounds especially convincing. In practice, the body tends to recover through time and calmer support much more than through dramatic product language.</p>'],
                ['heading' => 'Why routine changes matter more than rescue language', 'html' => '<p>The most useful liver-support conversation is usually not about a single fix but about reducing the pattern that created the problem. Better rhythm often does more than bigger promises.</p>'],
            ]
        ),
        $entry(
            641,
            'Choosing Reliable Superfood Manufacturers: Certifications, Traceability and Safety',
            'choosing-reliable-superfood-manufacturers-key-certifications-and-safety',
            'Superfoods sound impressive until product quality starts varying more than the label suggests. Here is how to assess manufacturers, certifications and origin with more care and less trust in pretty packaging.',
            '<ul><li>With superfoods, manufacturer quality and traceability often matter more than the product category itself.</li><li>The biggest mistake is buying on the “superfood” label alone without checking source and standards.</li><li>A smarter approach looks at transparency, certifications and the longer reputation of the brand.</li></ul>',
            [
                ['question' => 'Why do certifications matter?', 'answer' => 'Because they help signal production standards, safety and consistency.'],
                ['question' => 'Is the word “superfood” enough?', 'answer' => 'No. It often says more about marketing than actual quality.'],
                ['question' => 'What else should people look at?', 'answer' => 'Origin, traceability and how clearly the brand explains its quality controls.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Mistaking beautiful packaging for proof of real quality.'],
            ],
            'Superfoods and Safety: How to Recognize a More Trustworthy Manufacturer',
            'Learn how to choose more reliable superfood brands through certifications, traceability and clearer quality signals.',
            'Reliable Superfoods',
            [
                ['heading' => 'Why product quality begins long before the shelf', 'html' => '<p>People often judge a superfood by its trendy name, but the real quality story starts with sourcing, testing and production standards. That hidden part usually matters far more than the front label.</p>'],
                ['heading' => 'Why transparency is a practical safety signal', 'html' => '<p>Brands that explain what they do and why they do it usually give buyers more to work with. Good transparency does not guarantee perfection, but it often separates serious producers from shallow marketing.</p>'],
            ]
        ),
        $entry(
            644,
            'Circulation Problems: Where Massage and Natural Gels Can Genuinely Help',
            'circulation-problems-discover-the-power-of-massage-and-natural-gels',
            'Circulation complaints often begin as heaviness, coldness or tired legs and are easy to ignore for too long. Here is how to think about massage and natural gels as practical support rather than magical treatment for everything.',
            '<ul><li>For heavy legs and circulation discomfort, small repeatable habits often help more than occasional effort.</li><li>The biggest mistake is waiting until the discomfort becomes strong before introducing movement or support.</li><li>A smarter approach combines massage, gentler movement, cooling and skin care into a simple routine.</li></ul>',
            [
                ['question' => 'Can massage and gels help?', 'answer' => 'They can help comfort and the feeling of lighter legs, especially with regular use.'],
                ['question' => 'Are they enough for every circulation issue?', 'answer' => 'No. They are support tools for comfort, not full solutions for every cause.'],
                ['question' => 'What else usually helps?', 'answer' => 'More movement, less long sitting or standing and steadier daily rhythm.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Using one supportive product without changing the habits that keep the issue going.'],
            ],
            'Circulation and Massage: Where Natural Gels Make Sense for Everyday Comfort',
            'See how massage and natural gels can support heavy legs and circulation discomfort as part of a smarter daily routine.',
            'Circulation Problems',
            [
                ['heading' => 'Why comfort often improves through repetition', 'html' => '<p>People with heavy legs or sluggish-feeling circulation often benefit most from very small but regular habits. Repeated support usually creates more relief than a rare intense effort.</p>'],
                ['heading' => 'Why support works best when it meets movement', 'html' => '<p>Massage and gels can help the body feel better, but they usually work best alongside movement and less static daily load. Support becomes stronger when it is part of a pattern rather than a standalone attempt.</p>'],
            ]
        ),
        $entry(
            647,
            'The Antioxidant Power of Aloe Vera: What Is Worth Knowing Without Turning It into Hype',
            'antioxidant-power-of-aloe-vera-vitamins-polyphenols-and-health',
            'Aloe vera is interesting both because of its composition and its long history of use, yet antioxidant claims can easily slide into exaggeration. Here is how to look at those compounds more realistically through care, nutrition and supportive context.',
            '<ul><li>The antioxidant story of aloe is most useful when it stays connected to practical care and realistic support.</li><li>The biggest mistake is turning every interesting compound into a huge promise.</li><li>A smarter approach places aloe inside a framework of care, support and the real limits of one ingredient.</li></ul>',
            [
                ['question' => 'Why is aloe linked with antioxidants?', 'answer' => 'Because it contains compounds that make it interesting in research and practical use.'],
                ['question' => 'Does that mean aloe solves everything?', 'answer' => 'No. It can be useful in certain contexts without being a universal answer.'],
                ['question' => 'Where does this story make the most sense?', 'answer' => 'In care routines, supportive use and more informed reading of product composition.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Overpromising benefits simply because the ingredient profile sounds impressive.'],
            ],
            'Aloe Antioxidants: How to Separate Useful Facts from Wellness Hype',
            'Understand the antioxidant story of aloe vera and where it makes sense without exaggerated health claims.',
            'Aloe and Antioxidants',
            [
                ['heading' => 'Why interesting chemistry does not equal unlimited promise', 'html' => '<p>Once an ingredient sounds scientifically interesting, it is easy for marketing to start stretching the meaning. Aloe becomes much more useful when its properties are read inside a practical and limited context.</p>'],
                ['heading' => 'Why support is not the same as magic', 'html' => '<p>Supportive ingredients can absolutely matter, but they work best when people stop demanding that they do everything at once. Real value usually becomes clearer when expectations become calmer.</p>'],
            ]
        ),
        $entry(
            649,
            'Licorice: A Natural Ally for Digestion and Lungs or Just Another Overrated Classic?',
            'licorice-a-natural-ally-for-digestion-and-lungs',
            'Licorice is interesting because it bridges traditional use and modern interest in digestion and the respiratory tract, yet that same story can give it more credit than it deserves. Here is how to assess it more realistically.',
            '<ul><li>Licorice may fit supportive routines for the throat, digestion and a calmer feeling in irritated tissues.</li><li>The biggest mistake is turning it into a universal herbal answer without asking when it actually fits.</li><li>A smarter approach looks at tradition, context and the specific reason for using the herb.</li></ul>',
            [
                ['question' => 'Why is licorice often mentioned for throat and digestion?', 'answer' => 'Because it has a long tradition of use around soothing and comfort.'],
                ['question' => 'Is it right for everyone?', 'answer' => 'Not necessarily. As with other herbs, context and use matter.'],
                ['question' => 'When does it make more sense?', 'answer' => 'When it is used in a targeted, moderate and thoughtful way.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Assigning more power to the herb than it can realistically carry in practice.'],
            ],
            'Licorice for Digestion and Throat Support: Where It Helps and Where Hype Starts',
            'Learn when licorice may make sense for digestion and respiratory comfort and how to assess it more realistically.',
            'Licorice',
            [
                ['heading' => 'Why traditional herbs quickly attract big expectations', 'html' => '<p>Once a herb has a long history, people often begin adding modern miracle language on top of that legacy. Licorice makes more sense when it is seen as support rather than as a total answer.</p>'],
                ['heading' => 'Why context decides usefulness', 'html' => '<p>Plants rarely make sense in isolation. Their practical value usually becomes clearer when the symptom, the routine and the reason for using them are all seen together.</p>'],
            ]
        ),
        $entry(
            651,
            'Calcium and Vitamin D: Why Their Synergy Matters for Bones and Teeth',
            'calcium-and-vitamin-d-synergy-for-strong-bones-and-teeth',
            'Calcium and vitamin D are often mentioned together, and that pairing only makes sense when placed inside a wider story of bone health and daily habits. Here is how to understand that synergy without oversimplifying it.',
            '<ul><li>Calcium and vitamin D have a logical connection because one often makes less sense without the other.</li><li>The biggest mistake is treating them like separate magic nutrients instead of part of a wider system.</li><li>A smarter approach includes food, movement, sunlight and steadier habits rather than supplements alone.</li></ul>',
            [
                ['question' => 'Why are calcium and vitamin D mentioned together?', 'answer' => 'Because they are closely connected in support of bones and wider mineral balance.'],
                ['question' => 'Is taking one of them always enough?', 'answer' => 'Not always. Their usefulness often depends on the wider context of diet and lifestyle.'],
                ['question' => 'What else affects bone health?', 'answer' => 'Movement, food quality, sunlight and long-term daily habits.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Reducing bone health to one or two supplements without the bigger picture.'],
            ],
            'Calcium and Vitamin D: How Their Synergy Truly Supports Bones and Teeth',
            'Understand why calcium and vitamin D matter together and how to place them inside a wider bone-health routine.',
            'Calcium and Vitamin D',
            [
                ['heading' => 'Why nutrient synergy matters more than isolated supplement logic', 'html' => '<p>People often want simple answers, yet the body usually works through relationships between nutrients rather than isolated stories. Calcium and vitamin D make the most sense when they are viewed inside that larger system.</p>'],
                ['heading' => 'Why habits still shape the outcome', 'html' => '<p>Supplements can support bone health, but they cannot replace movement, sunlight and food quality. The strongest strategy almost always looks broader than a bottle or tablet.</p>'],
            ]
        ),
        $entry(
            885,
            'Perfectionism and Procrastination: How to Break the Loop in 7 More Realistic Steps',
            'perfectionism-and-procrastination-how-to-break-the-loop-in-7-steps',
            'Perfectionism and procrastination often look like separate problems, yet in practice they feed each other. Here is how to spot the loop and break it without replacing it with another unrealistic self-improvement plan.',
            '<ul><li>Perfectionism is often less about discipline and more about fear of an imperfect start or outcome.</li><li>The biggest mistake is trying to solve procrastination by creating even stricter and bigger plans.</li><li>A smarter approach lowers the starting threshold, rebuilds action through small steps and changes the relationship with mistakes.</li></ul>',
            [
                ['question' => 'How are perfectionism and procrastination linked?', 'answer' => 'Perfectionism often increases pressure, and that pressure then fuels delay and avoidance.'],
                ['question' => 'Why do stricter plans often fail?', 'answer' => 'Because they usually make the pressure bigger and the start feel even heavier.'],
                ['question' => 'What tends to help most?', 'answer' => 'Smaller starting steps, more realistic standards and action before ideal conditions.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Confusing perfectionism with quality and keeping the same loop alive.'],
            ],
            'Perfectionism and Procrastination: How to Leave the Same Loop Without New Illusions',
            'Learn how to break the loop between perfectionism and procrastination through smaller steps and more realistic standards.',
            'Perfectionism and Procrastination',
            [
                ['heading' => 'Why pressure often hides behind high standards', 'html' => '<p>Perfectionism can look admirable from the outside, but internally it often produces hesitation, tension and delay. Once that pressure is visible, it becomes easier to treat the pattern with more honesty.</p>'],
                ['heading' => 'Why smaller starts create more movement', 'html' => '<p>People stuck in delay rarely need a bigger ideal. They usually need a smaller doorway into action, one that allows progress to begin before confidence feels complete.</p>'],
            ]
        ),
    ],
];
