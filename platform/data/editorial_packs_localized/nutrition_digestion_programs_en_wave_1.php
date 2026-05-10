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
    'key' => 'nutrition-digestion-programs-en-wave-1',
    'name' => 'Nutrition, Digestion and Programs (EN) - wave 1',
    'notes' => 'Manual premium localized wave for diet plans, digestion themes, micronutrients and Clean 9 support articles.',
    'entries' => [
        $entry(
            566,
            'Medical Diet: What to Realistically Expect from a 15-Day Weight-Loss Plan',
            'medical-diet-15-day-weight-loss-plan',
            'Short medical-style diet plans are appealing because they promise a fast shift, yet they only make sense when they do not turn into another cycle of restriction and rebound. Here is how to assess that kind of plan more realistically and more safely.',
            '<ul><li>Short diet plans only make sense as temporary structure, not as a long-term philosophy of eating.</li><li>The biggest mistake is mistaking a fast drop for a truly sustainable change.</li><li>A smarter approach looks at energy, sustainability and what happens after the plan ends.</li></ul>',
            [
                ['question' => 'Why is the medical diet so popular?', 'answer' => 'Because it promises a fast result and a very clear short-term structure.'],
                ['question' => 'Is quick weight loss always a good sign?', 'answer' => 'No. What matters is how the body feels and what happens once the plan is over.'],
                ['question' => 'When does this kind of plan make more sense?', 'answer' => 'When it is used as a short reset with realistic expectations rather than as a repeated extreme.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Returning to the same old habits immediately after finishing the diet.'],
            ],
            'Medical Diet: How to Assess a 15-Day Weight-Loss Plan Without Quick-Fix Illusions',
            'Learn when a short medical diet plan may make sense and how to avoid the biggest mistakes of restrictive weight-loss approaches.',
            'Medical Diet',
            [
                ['heading' => 'Why short plans only work with a next step', 'html' => '<p>Quick plans usually feel powerful because they create immediate structure. Their real value depends on whether the person has a calmer and more sustainable follow-up instead of falling back into the same old pattern.</p>'],
                ['heading' => 'Why speed alone is a poor success measure', 'html' => '<p>Fast change can feel motivating, but it does not automatically mean the plan is a good long-term fit. Lasting results usually come when structure and everyday life start working together better.</p>'],
            ]
        ),
        $entry(
            593,
            'Omega-3 Fatty Acids: 7 Benefits That Only Make Sense in the Right Context',
            'omega-3-fatty-acids-7-key-benefits',
            'Omega-3 products are famous enough to become an exaggerated answer for almost everything. Here is how to understand their benefits more realistically through food quality, inflammation balance and long-term habits.',
            '<ul><li>Omega-3 makes the most sense inside a wider picture of nutrition, recovery and cardiovascular support.</li><li>The biggest mistake is expecting one supplement to do the work of an entire lifestyle.</li><li>A smarter approach looks at consistency, food sources and the real reason for taking it.</li></ul>',
            [
                ['question' => 'Why do people usually take omega-3?', 'answer' => 'Mostly for support related to the heart, brain and inflammatory balance.'],
                ['question' => 'Can omega-3 replace poor nutrition?', 'answer' => 'No. It makes sense as a support tool, not as a substitute for better daily habits.'],
                ['question' => 'When does it make more sense?', 'answer' => 'When there is a clear goal and it fits into a broader routine.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Buying omega-3 because it sounds healthy without a clear reason or plan.'],
            ],
            'Omega-3 Benefits: When They Truly Make Sense and How to Read Them More Realistically',
            'Understand the main benefits of omega-3 fatty acids and where they genuinely fit daily nutrition and recovery.',
            'Omega-3',
            [
                ['heading' => 'Why omega-3 is bigger than one supplement story', 'html' => '<p>Omega-3 is often discussed as if it could solve every modern health issue by itself. In reality, its value is usually clearest when it supports a much wider pattern of food quality and everyday consistency.</p>'],
                ['heading' => 'Why the reason for use matters', 'html' => '<p>Supplements tend to work best when there is a clear reason behind them. A person who knows why they want omega-3 is usually better able to use it consistently and judge whether it belongs in the routine.</p>'],
            ]
        ),
        $entry(
            594,
            'Vitamin D: Why It Matters and How to Improve Intake Without Overdoing It',
            'vitamin-d-why-its-important-and-how-to-optimize-your-intake',
            'Vitamin D has almost become shorthand for immunity and wellbeing, yet its real value only shows up when it is used thoughtfully. Here is how to approach it through sun, food and supplements without drifting into guesswork.',
            '<ul><li>Vitamin D matters for bones, immunity and wider vitality, but not everyone needs the same strategy.</li><li>The biggest mistake is copying someone else’s supplement logic without checking personal context.</li><li>A smarter approach looks at sun exposure, food choices and reasonable support over time.</li></ul>',
            [
                ['question' => 'Why is vitamin D so important?', 'answer' => 'Because it plays a role in bone health, immunity and wider regulatory processes in the body.'],
                ['question' => 'Does everyone need a supplement?', 'answer' => 'Not always. It depends on season, sun exposure, food intake and personal context.'],
                ['question' => 'What is the best way to improve intake?', 'answer' => 'Usually through a sensible mix of sunlight, diet and supplements when there is a good reason for them.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Turning vitamin D into a universal answer instead of looking at the whole health picture.'],
            ],
            'Vitamin D: How to Improve Intake More Intelligently and Without Overdoing It',
            'See why vitamin D matters and how to approach sun, food and supplementation with more realism and less guesswork.',
            'Vitamin D',
            [
                ['heading' => 'Why vitamin D needs context, not hype', 'html' => '<p>Vitamin D has earned a strong reputation, which can make it sound like a fix for almost everything. Its real value becomes much clearer when it is matched to the person’s season, routine and actual need.</p>'],
                ['heading' => 'Why a balanced approach wins', 'html' => '<p>The smartest use of vitamin D usually combines sensible sun exposure, food awareness and carefully chosen supplementation. Extremes in either direction are rarely the most useful path.</p>'],
            ]
        ),
        $entry(
            606,
            'Spring Detox with Soups, Smoothies and Exercise: What Helps and What Is Mostly a Mood',
            'spring-detox-with-soups-smoothies-exercise',
            'Spring detox language sounds attractive because it promises a fresh start, yet the real benefit only appears when the reset does not become another short extreme. Here is how to use soups, smoothies and movement without dramatic detox mythology.',
            '<ul><li>A spring reset works best as a move towards simpler eating and more movement, not as a mythical cleansing ritual.</li><li>The biggest mistake is building a short wave of enthusiasm with no plan for what comes next.</li><li>A smarter approach uses the season for more order, hydration and lighter meal rhythm.</li></ul>',
            [
                ['question' => 'Does a detox need to be extreme to help?', 'answer' => 'No. A calmer and simpler reset is often more helpful and much easier to sustain.'],
                ['question' => 'Why are soups and smoothies so popular?', 'answer' => 'Because they can make hydration and vegetable intake feel easier and lighter.'],
                ['question' => 'Where does exercise fit?', 'answer' => 'As part of a wider rhythm and energy pattern, not as punishment for previous habits.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Confusing a short reset with a real shift in everyday food and movement habits.'],
            ],
            'Spring Detox: How to Turn It into a Lighter Reset Instead of a Short Extreme',
            'Learn how a spring detox with soups, smoothies and exercise can make sense without exaggerated detox claims.',
            'Spring Detox',
            [
                ['heading' => 'Why reset language can help and mislead at the same time', 'html' => '<p>People often need a moment that feels like a fresh start, and that part of detox language can be useful. The problem begins when a symbolic reset is mistaken for a magical change with no follow-up structure.</p>'],
                ['heading' => 'Why lighter systems usually last longer', 'html' => '<p>Simple food choices and gentler movement habits are often much easier to continue than intense protocols. That is why the most useful spring changes usually look less dramatic than the marketing around them.</p>'],
            ]
        ),
        $entry(
            608,
            'C9 Recipes: 5 Quick Meals That Make the Clean 9 Program Easier to Follow',
            'c9-recipes-5-quick-meals-for-success-in-the-clean-9-program',
            'The hardest part of a structured program is often not discipline but practicality. Here is how a few quick meals can make Clean 9 easier to follow and far less stressful in everyday life.',
            '<ul><li>Simple and predictable meals usually make structured programs easier to follow.</li><li>The biggest mistake is starting Clean 9 without a clear food plan and a few easy fallback options.</li><li>A smarter approach removes decision fatigue and turns meals into support instead of extra stress.</li></ul>',
            [
                ['question' => 'Why do recipes matter so much on Clean 9?', 'answer' => 'Because simple meals improve consistency and reduce food-related stress.'],
                ['question' => 'Do the meals need to be complicated?', 'answer' => 'No. Clear, fast and repeatable meals are usually the most helpful.'],
                ['question' => 'What matters before starting?', 'answer' => 'Basic shopping, simple planning and a few reliable meal ideas.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Leaving food decisions to chance while trying to follow a structured program.'],
            ],
            'C9 Recipes: 5 Practical Meals for a Smoother and More Sustainable Clean 9 Plan',
            'Discover how five quick C9 recipes can make the Clean 9 program easier, calmer and more realistic to follow.',
            'C9 Recipes',
            [
                ['heading' => 'Why practical food always beats perfect food', 'html' => '<p>When a plan is already structured, the best meals are often the ones that reduce friction. Food becomes much more useful when it simplifies the day instead of creating another layer of decision stress.</p>'],
                ['heading' => 'Why repetition is often a strength', 'html' => '<p>People sometimes assume variety is always better, but in a short program consistency usually wins. A few reliable meals often make the whole plan easier to trust and repeat.</p>'],
            ]
        ),
        $entry(
            610,
            'Low-Temperature Cooking: How to Preserve More Nutrients in Everyday Meals',
            'low-temperature-cooking-preserve-nutrients-in-every-bite',
            'How food is prepared often matters almost as much as the food itself. Here is how lower-temperature cooking can help preserve texture, moisture and some more delicate compounds without turning cooking into a lab project.',
            '<ul><li>Gentler heat can help preserve texture, moisture and some more sensitive nutritional qualities.</li><li>The biggest mistake is assuming healthier cooking must always be more complicated or slower.</li><li>A smarter approach balances practicality, taste and the needs of the ingredient.</li></ul>',
            [
                ['question' => 'What counts as low-temperature cooking?', 'answer' => 'It means preparing food with gentler heat so texture and flavour are better preserved.'],
                ['question' => 'Are all nutrients heat-sensitive?', 'answer' => 'Not equally, but some compounds and vitamins are more delicate than others.'],
                ['question' => 'Does this method have to be complicated?', 'answer' => 'No. Even simple daily cooking can be gentler and more thoughtful.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Turning healthier cooking into an unrealistic and overly technical process.'],
            ],
            'Low-Temperature Cooking: How to Keep More Texture, Taste and Nutritional Value',
            'See how lower-temperature cooking can help preserve food quality and nutrients without making daily meals more complicated.',
            'Low-Temperature Cooking',
            [
                ['heading' => 'Why cooking method shapes the meal more than people think', 'html' => '<p>Food quality is not only about ingredients but also about what happens to them during preparation. Gentler cooking often helps preserve more of what people actually enjoy in a meal: taste, moisture and a less harsh overall result.</p>'],
                ['heading' => 'Why simple technique often matters more than perfect technique', 'html' => '<p>The goal is not to cook like a scientist. It is usually enough to understand when gentler heat makes sense and then use that insight in a way that still fits normal daily life.</p>'],
            ]
        ),
        $entry(
            613,
            'Ayurvedic Nutrition: Doshas, Seasons and How to Use the Idea Without the Mystique',
            'ayurvedic-approach-to-nutrition-through-doshas-and-seasons',
            'Ayurveda sounds appealing because it connects food with body rhythms and the seasons, yet it becomes useful only when translated into daily life. Here is how to use that perspective without turning it into rigid or mystical food rules.',
            '<ul><li>An Ayurvedic lens can be useful when it helps people pay more attention to rhythm, digestion and seasonality.</li><li>The biggest mistake is turning it into rigid doctrine with little everyday usefulness.</li><li>A smarter approach keeps the practical parts: warmth of meals, seasonal shifts and personal food response.</li></ul>',
            [
                ['question' => 'What are doshas in practical terms?', 'answer' => 'They are a traditional framework for describing different patterns of energy, body type and digestion.'],
                ['question' => 'Does everything need to be followed literally?', 'answer' => 'No. The most useful approach is to take what is practical and meaningful in daily life.'],
                ['question' => 'Why do seasons matter in this model?', 'answer' => 'Because food choices and body rhythms often shift with climate, temperature and daily routine.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Replacing practical food awareness with a strict belief system that is hard to live with.'],
            ],
            'Ayurvedic Nutrition: How to Use Doshas and Seasons Without Overcomplicating Food',
            'Learn how to apply Ayurvedic nutrition more practically through seasonal rhythm, digestion and realistic food awareness.',
            'Ayurvedic Nutrition',
            [
                ['heading' => 'Why people are drawn to seasonal food frameworks', 'html' => '<p>Many people feel that modern eating has become disconnected from rhythm and season, which is why older frameworks still feel attractive. Their strength usually lies in restoring attention rather than demanding blind obedience.</p>'],
                ['heading' => 'Why practical use matters more than ideology', 'html' => '<p>The best parts of traditional systems are often the ones that help people notice their body more clearly. A useful framework usually makes life simpler, not more confusing or rigid.</p>'],
            ]
        ),
        $entry(
            632,
            'Food Allergies: A Practical Guide to Dairy-Free, Gluten-Free and Peanut-Free Eating',
            'food-allergies-a-guide-to-dairy-gluten-and-peanut-free-diets',
            'A food plan without dairy, gluten or peanuts can quickly become stressful if it lacks structure. Here is how to approach food allergies with more safety, planning and less nutritional panic.',
            '<ul><li>With food allergies, safety, clarity and reliable meal planning matter most.</li><li>The biggest mistake is removing everything at once without understanding the real trigger and the new structure needed.</li><li>A smarter approach builds a plan that is safe, nourishing and workable in daily life.</li></ul>',
            [
                ['question' => 'Why is this kind of plan so demanding?', 'answer' => 'Because it usually requires more label reading, planning and reliable replacements.'],
                ['question' => 'Should several foods be removed at once?', 'answer' => 'Only when there is a clear reason and a structure that stays nutritionally sensible.'],
                ['question' => 'What helps most in practice?', 'answer' => 'Planning, safe fallback meals and simple substitutes that reduce chaos.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Turning an elimination plan into a wide ban with no sustainable structure.'],
            ],
            'Food Allergies: How to Build a Safer Dairy-Free, Gluten-Free and Peanut-Free Plan',
            'Discover how to organise food-allergy eating in a safer, clearer and more sustainable way without unnecessary panic.',
            'Food Allergies',
            [
                ['heading' => 'Why allergy planning is mostly about clarity', 'html' => '<p>When a food feels risky, uncertainty creates stress very quickly. That is why the strongest allergy routines are usually the simplest ones: clear ingredients, clear fallback meals and less last-minute guessing.</p>'],
                ['heading' => 'Why restriction should still stay organised', 'html' => '<p>Removing a trigger is only one part of the work. The next challenge is making sure the person still has enough reliable, practical and nourishing food in daily life.</p>'],
            ]
        ),
        $entry(
            636,
            'Autoimmune Bowel Inflammation: Diet and Probiotics for Crohn’s and Colitis in a Realistic Frame',
            'autoimmune-bowel-inflammation-probiotics-and-diet-for-crohns-and-colitis',
            'With Crohn’s and colitis, food matters, but it is not a magic switch that solves a complex condition alone. Here is how to view diet and probiotics as support for daily life rather than as overpromised certainty.',
            '<ul><li>Diet and probiotics can support comfort, routine and digestive stability, but not in exactly the same way for everyone.</li><li>The biggest mistake is searching for one perfect diet that must work for all people with the same diagnosis.</li><li>A smarter approach looks at personal tolerance, symptom phase and gradual change.</li></ul>',
            [
                ['question' => 'Can probiotics help with Crohn’s or colitis?', 'answer' => 'They may help in some cases, but not equally in every phase or for every person.'],
                ['question' => 'Why is diet not the same for everyone?', 'answer' => 'Because food tolerance and symptom intensity can vary a great deal.'],
                ['question' => 'What is most useful in practice?', 'answer' => 'Watching patterns, keeping notes and changing things slowly and thoughtfully.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Copying someone else’s plan without checking personal reactions and limits.'],
            ],
            'Crohn’s and Colitis: How Diet and Probiotics Can Support Without False Promises',
            'Understand how food and probiotics may support Crohn’s and colitis more realistically, through tolerance and gradual adjustment.',
            'Crohn’s and Colitis',
            [
                ['heading' => 'Why digestive support has to stay personal', 'html' => '<p>People often want one rule that explains what to eat and what to avoid, but inflammatory bowel conditions rarely behave that neatly. Support becomes more useful when it respects the individual pattern rather than forcing a universal formula.</p>'],
                ['heading' => 'Why gradual change is usually safer than dramatic change', 'html' => '<p>Big dietary shifts can create confusion if the body is already sensitive. Slower changes often make it easier to learn what helps and what simply adds noise.</p>'],
            ]
        ),
        $entry(
            813,
            'Clean 9 and Health Conditions: Can You Start and How Should the Plan Be Adjusted?',
            'clean-9-and-health-conditions-can-you-start-and-how-to-adjust',
            'Clean 9 is not a program to enter blindly, especially when there are existing health issues or a more sensitive body context. Here is how to judge whether the program makes sense and what smart adjustment really means.',
            '<ul><li>Structured programs only make sense when they fit the real health context of the person using them.</li><li>The biggest mistake is starting Clean 9 with an “I just need to push through” mindset.</li><li>A smarter approach asks about safety, adjustment and whether the program is even the right choice right now.</li></ul>',
            [
                ['question' => 'Can everyone start Clean 9?', 'answer' => 'Not necessarily. Existing health conditions can change whether the program is a good fit.'],
                ['question' => 'What does adjusting the plan mean?', 'answer' => 'It means reducing rigidity and matching the plan to real energy, tolerance and personal context.'],
                ['question' => 'When might it make less sense?', 'answer' => 'When it would create more strain than benefit or when the body clearly needs a different approach.'],
                ['question' => 'What is a common mistake?', 'answer' => 'Treating the plan like a discipline test instead of a tool that must suit the person.'],
            ],
            'Clean 9 and Health Conditions: When the Program Fits and When It Needs Adjusting',
            'See when Clean 9 may make sense alongside health issues and what safer, smarter plan adjustment really looks like.',
            'Clean 9 and Health',
            [
                ['heading' => 'Why no program should sit above the person', 'html' => '<p>Structured systems often feel motivating because they are clear, but clarity alone does not make them appropriate. The person’s health context always matters more than the elegance of the plan.</p>'],
                ['heading' => 'Why smart adjustment is not failure', 'html' => '<p>People often read adjustment as weakness, yet thoughtful adaptation is often what makes a program safer and more useful. Flexibility is frequently a sign of realism, not lower commitment.</p>'],
            ]
        ),
    ],
];
