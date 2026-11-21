<?php
// The detail page of each product line. Keys match 'range.items' in en.php.
return [
    'ui' => [
        'back'      => 'All we make',
        'more'      => 'See the full line',
        'garments'  => 'What we make in this line',
        'fabrics'   => 'The fabric we use',
        'options'   => 'What you decide',
        'questions' => 'Questions people ask',
        'ask'       => 'Ask about',
        'ask_lead'  => 'Tell us what you need and how many. We reply with questions, a sample plan and a price.',
        'next'      => 'Next',
        'all'       => 'All lines',
    ],

    'lines' => [
        'office' => [
            'lead' => 'Shirts, trousers, skirts and jackets for the people who meet your customers.',
            'intro_title' => 'Clothes people wear all day, every day',
            'intro' => [
                'Office wear has a hard job. It has to look sharp at nine in the morning and still look sharp at five, after a day of sitting, standing and driving between meetings. So we start with the fit and the fabric, and leave the fancy details for last.',
                'Most office orders are for banks, company offices, reception teams and showrooms. Usually the whole team wears the same shirt and trousers in the company colours, with the logo stitched on the chest.',
            ],
            'garments' => [
                ['Shirts and blouses', 'Long or short sleeve, in a plain colour or a fine stripe. Cut separately for men and women, so both fit properly.'],
                ['Trousers and skirts', 'A straight, simple cut that sits well and leaves a little room to move.'],
                ['Jackets and blazers', 'Fully lined, with your colour inside the collar or on the buttons if you like.'],
                ['Waistcoats', 'For reception and front-desk teams who want to look smart without a full jacket.'],
                ['Ties and scarves', 'Made in the same order, so the colour matches the rest of the uniform.'],
            ],
            'fabrics' => [
                ['Cotton poplin', 'Smooth, light and cool. The classic shirt fabric, and the one we use most.'],
                ['Poly-cotton blend', 'Looks like cotton but creases less and dries faster. Good for teams who wash their shirts at home.'],
                ['Suiting', 'A heavier cloth that hangs cleanly, for trousers, skirts and jackets.'],
            ],
            'options' => [
                ['Colour', 'Send us your company colour and we match the cloth to it.'],
                ['Logo', 'Embroidered on the chest, the cuff or the collar.'],
                ['Sizes', 'A full run of sizes for men and women, and made-to-measure for anyone the standard sizes do not fit.'],
                ['Names', 'A name or a department can go under the logo.'],
            ],
            'questions' => [
                ['Can we see a sample before we order?', 'Yes. We sew one in your colour with your logo, and nothing else is made until you have tried it on and said yes.'],
                ['What if new staff join later?', 'We keep your pattern and your colour on file, so a small top-up order looks the same as the first one.'],
                ['Do you help with sizes?', 'Yes. Send us your staff list with sizes. If people are not sure, we share a size chart and a few sample pieces to try.'],
            ],
            'alts' => ['A man in a dark suit and tie', 'A man in a charcoal suit and burgundy tie', 'White, blue and pink shirts on wooden hangers'],
            'gallery' => [['Suits', 'A man buttoning a grey suit jacket over a striped tie'], ['Jackets', 'Close view of a navy jacket being buttoned'], ['Shirts and ties', 'A man in a white shirt and dark tie'], ['Trousers', 'A man in a light blue blazer and grey trousers']],
        ],

        'workwear' => [
            'lead' => 'Coveralls, jackets and trousers built for sites, workshops and factory floors.',
            'intro_title' => 'Made for the job, not for the hanger',
            'intro' => [
                'Workwear gets dragged, kneeled on, soaked and washed hot. If a seam is going to give up, it will do it on site, at the worst moment. That is why we double-stitch the seams that take the strain and put extra cloth on the knees and elbows.',
                'We make it for oil and gas crews, construction companies, factories and workshops. Tell us what the work is like, how hot it gets and what your crew complains about in the gear they wear now. That usually tells us more than a spec sheet.',
            ],
            'garments' => [
                ['Coveralls', 'One piece with a zip or button front, and room to bend and reach.'],
                ['Work jackets and trousers', 'A two-piece set for people who are in and out of a vehicle or an office.'],
                ['High-visibility vests', 'Bright orange or yellow with reflective tape, so your people are seen.'],
                ['Bib overalls', 'For workshops and mechanics, with pockets where the tools actually go.'],
                ['Work shirts', 'A lighter option for summer or indoor work, with your logo on the chest.'],
            ],
            'fabrics' => [
                ['Cotton drill', 'Thick, tough cotton with a diagonal weave. It breathes, which matters in the heat.'],
                ['Poly-cotton twill', 'A little lighter. It holds its colour and dries quickly.'],
                ['Canvas', 'The heaviest cloth we use, for jobs that tear through everything else.'],
            ],
            'options' => [
                ['Colour', 'By company, by team or by role, so a supervisor is easy to spot.'],
                ['Reflective tape', 'On the chest, arms and legs, wherever your site rules ask for it.'],
                ['Pockets', 'Tell us what your crew carries and we place the pockets for it.'],
                ['Logo', 'Embroidered or printed, on the chest and across the back.'],
            ],
            'questions' => [
                ['Is it comfortable in the summer heat?', 'For outdoor work we choose cotton that breathes, and we can add vents under the arms and across the back.'],
                ['Our site has safety rules for clothing. Can you follow them?', 'Send them with your enquiry. We will tell you plainly what we can make to those rules and what we cannot.'],
                ['How long will it last?', 'It depends on the work. We reinforce the places that normally go first: knees, elbows, pocket corners and the seat. If something still wears out early, tell us and we change the design for your next order.'],
            ],
            'alts' => ['A worker in a green coverall and yellow helmet', 'A mechanic in grey work clothes kneeling with a spanner', 'A worker in a navy coverall kneeling with a coil of rope'],
            'gallery' => [['Coveralls', 'A navy one-piece coverall with a zip front'], ['Work jackets', 'A worker in a two-tone blue work jacket'], ['High-visibility', 'A worker in orange high-visibility trousers and jacket'], ['Site wear', 'A builder in blue work clothes, a safety vest and a yellow helmet']],
        ],

        'medical' => [
            'lead' => 'Scrubs, lab coats and nursing uniforms that stay fresh through long shifts and hot washes.',
            'intro_title' => 'Clean, comfortable and easy to tell apart',
            'intro' => [
                'People in clinics and hospitals are on their feet for hours, and their clothes are washed hot again and again. So medical wear needs two things above all. It has to be comfortable enough to forget about, and it has to come out of the wash looking the way it went in.',
                'We make uniforms for hospitals, clinics, dental practices, pharmacies and laboratories. Many customers give each department its own colour, so patients and staff can tell at a glance who is who.',
            ],
            'garments' => [
                ['Scrub suits', 'A loose top and trousers with deep pockets, with a V-neck or a round neck.'],
                ['Lab coats', 'Knee length with long sleeves, a chest pocket for pens and two pockets at the hip.'],
                ['Nursing uniforms', 'Tunics and dresses with trousers, with a coloured trim if you want one.'],
                ['Patient gowns', 'Soft, easy to put on and easy to open for an examination.'],
                ['Caps', 'In the same cloth and colour as the suit.'],
            ],
            'fabrics' => [
                ['Poly-cotton poplin', 'Light and smooth, and it stands up to hot washing. Our usual choice for scrubs.'],
                ['Cotton twill', 'A little heavier, for lab coats that should hang well.'],
                ['Soft cotton', 'For patient gowns, where comfort on the skin comes first.'],
            ],
            'options' => [
                ['Colour', 'One colour for everyone, or one for each department.'],
                ['Name and role', 'Embroidered on the chest, so patients know who they are talking to.'],
                ['Fit', 'Separate cuts for men and women, in a full run of sizes.'],
                ['Pockets', 'More, fewer or in different places. Tell us what people carry.'],
            ],
            'questions' => [
                ['Will the colour fade?', 'Hot washing is hard on colour, so we use cloth that is dyed to cope with it. We send a sample you can wash a few times yourself before you order.'],
                ['Can each department have its own colour?', 'Yes. It is a simple way to help patients and staff tell who is who.'],
                ['Can we order a few more later?', 'Yes. We keep your pattern and colour, so the later pieces match the first ones.'],
            ],
            'alts' => ['A doctor in a white lab coat', 'A nurse in blue scrubs with arms folded', 'A doctor in a white coat beside a nurse in green scrubs'],
            'gallery' => [['Scrub suits', 'A nurse in a green scrub suit'], ['Lab coats', 'A doctor in a white lab coat and face mask'], ['Caps', 'A nurse in a blue scrub cap and long tunic'], ['Theatre wear', 'A surgeon in a cap, mask, green gown and blue gloves']],
        ],

        'hospitality' => [
            'lead' => 'Chef jackets, aprons and service uniforms for hotels, restaurants and cafés.',
            'intro_title' => 'Your staff are part of how the place looks',
            'intro' => [
                'Guests notice the people before they notice the menu. A clean chef jacket or a well-cut apron says the place is looked after. But a kitchen is hot and messy, and service means hours of walking, so the clothes have to work as hard as the people in them.',
                'We make uniforms for hotels, restaurants, cafés, bakeries and caterers: one look for the kitchen, one for the floor and one for housekeeping, all in colours that suit your place.',
            ],
            'garments' => [
                ['Chef jackets', 'Double-breasted, so a stained front can be buttoned the other way. In white, black or your own colour.'],
                ['Aprons', 'Bib or waist, short or long, with pockets for a pen, a pad or a cloth.'],
                ['Service shirts and waistcoats', 'For waiters and the team that welcomes your guests.'],
                ['Chef trousers and hats', 'Loose trousers for the heat, with a skull cap or a classic tall hat.'],
                ['Housekeeping uniforms', 'Tunics and trousers that are easy to move and bend in.'],
            ],
            'fabrics' => [
                ['Cotton twill', 'Thick enough to protect from heat and splashes, and still breathable.'],
                ['Gabardine', 'Smooth and neat, for service uniforms that should stay crisp.'],
                ['Poly-cotton', 'For aprons and housekeeping, where easy washing matters most.'],
            ],
            'options' => [
                ['Colour', 'Match your interior, your logo or your menu.'],
                ['Logo', 'Embroidered on the chest or printed on the apron.'],
                ['Names', 'The chef’s name on the jacket, or a first name for the people serving.'],
                ['Style', 'Classic or modern. Send us a photo of a look you like.'],
            ],
            'questions' => [
                ['Do the whites stay white?', 'We pick cloth that can be washed hot. Tell us how your laundry is done and we choose to suit it.'],
                ['We are a small café. Is that too small an order?', 'Tell us how many people you have and we will tell you what is possible.'],
                ['Can kitchen and floor staff look like one team?', 'Yes. We carry the same colour or the same trim across both.'],
            ],
            'alts' => ['A chef in a grey chef jacket beside a plate of hummus and bread', 'A chef in a white jacket', 'Two young cooks in white chef jackets'],
            'gallery' => [['Floor staff', 'A waiter in a black waistcoat and bow tie'], ['Service shirts', 'A waiter in a black polo shirt carrying a tray'], ['Aprons', 'A barista in a tan apron holding a cup of coffee'], ['Chef jackets', 'A cook in a white chef jacket and cap']],
        ],

        'school' => [
            'lead' => 'Shirts, trousers, pinafores and sportswear in every size, from the first year to the last.',
            'intro_title' => 'Made to survive the school year',
            'intro' => [
                'School clothes live a rough life. They are worn five days a week and washed every weekend, and children grow out of them or wear through the knees before the year is over. We make them to take that: strong seams, deep hems that can be let down, and cloth that does not need much ironing.',
                'We work with schools and with the shops that supply them. What matters most to a school is that next year’s uniform looks exactly like this year’s, so we keep your colours and patterns on file.',
            ],
            'garments' => [
                ['Shirts and blouses', 'White or coloured, with long or short sleeves.'],
                ['Trousers and skirts', 'With an adjustable waist for growing children.'],
                ['Pinafores', 'For younger pupils, in your school colour.'],
                ['Blazers', 'With the school crest on the pocket.'],
                ['Sportswear', 'T-shirts, shorts and tracksuits for sports lessons.'],
            ],
            'fabrics' => [
                ['Easy-care blends', 'Cotton mixed with polyester. Soft, hard-wearing and quick to iron.'],
                ['Oxford', 'A slightly thicker shirt cloth that does not go see-through.'],
                ['Twill', 'For trousers and skirts that have to last.'],
            ],
            'options' => [
                ['Colours', 'Your school colours, kept the same every year.'],
                ['Crest', 'Embroidered on the pocket or the chest.'],
                ['Sizes', 'Every age, from the smallest pupils to the oldest.'],
                ['Timing', 'Order early and it is ready before term starts.'],
            ],
            'questions' => [
                ['Will next year’s uniform match this year’s?', 'Yes. We keep your colour and pattern, and make every repeat order from the same record.'],
                ['Can you deliver before the school year starts?', 'Give us your date when you enquire and we plan around it. The earlier you order, the easier it is.'],
                ['Do you sell to parents directly?', 'We make for schools and for uniform shops. If you are a parent, ask your school where its uniform is sold.'],
            ],
            'alts' => ['A boy in a white school shirt and red tie', 'A boy in a white school shirt', 'Three boys in light blue school shirts and navy ties'],
            'gallery' => [['Sportswear', 'A boy in a red school tracksuit with a backpack'], ['Shirts and ties', 'Two boys in white shirts and red ties'], ['Ties', 'Close view of a blue school shirt and striped tie'], ['Whole-school sets', 'A group of boys in matching blue uniforms']],
        ],

        'military' => [
            'lead' => 'Field shirts, trousers, jackets and caps in ripstop and heavy twill.',
            'intro_title' => 'Cloth that has to hold up outdoors',
            'intro' => [
                'Field clothing is worn outdoors for long days, in heat, dust and rough ground. It has to be strong without being heavy, and it has to let a person move freely: kneel, climb and carry. We build it from ripstop, a cloth with a grid of stronger threads woven in, so a small tear stays small.',
                'We make to the customer’s specification. You give us the pattern, the colours and the measurements, and we follow them exactly.',
            ],
            'garments' => [
                ['Field shirts', 'Chest pockets with flaps, and sleeves that roll up and button in place.'],
                ['Field trousers', 'Cargo pockets on the thigh and a double layer at the knee and seat.'],
                ['Jackets', 'A heavier outer layer for cold mornings and winter.'],
                ['Caps and hats', 'In the same cloth as the uniform.'],
                ['T-shirts', 'Plain cotton, to wear underneath.'],
            ],
            'fabrics' => [
                ['Ripstop', 'Light and strong. The woven grid stops a tear from spreading.'],
                ['Heavy twill', 'Thicker and warmer, for jackets and winter trousers.'],
                ['Cotton-rich blends', 'They breathe in the heat and are kind to the skin.'],
            ],
            'options' => [
                ['Pattern', 'Plain olive, khaki or a camouflage print. Send us the artwork or a piece of the cloth.'],
                ['Reinforcement', 'Knees, elbows and seat, doubled where you want them.'],
                ['Fastenings', 'Buttons, zips or hook-and-loop.'],
                ['Sizes', 'A full range of sizes, in regular and long lengths.'],
            ],
            'questions' => [
                ['Can you match a pattern we already use?', 'Send us a piece of the cloth or the artwork. We make a sample for you to compare side by side.'],
                ['Does it cope with the heat?', 'We use cotton-rich cloth that breathes, and can add vents under the arms.'],
                ['How do we order?', 'Send an enquiry with the garments, the quantities and your specification. We reply with questions and a sample plan.'],
            ],
            'alts' => ['Close view of an olive field jacket', 'A man in a camouflage jacket and cap', 'Camouflage trousers and boots in step'],
            'gallery' => [['Field trousers', 'Olive field trousers and black boots'], ['Woodland ripstop', 'Green camouflage ripstop cloth'], ['Olive twill', 'Dark olive green cloth in soft folds'], ['Desert ripstop', 'Sand-coloured camouflage ripstop cloth']],
        ],

        'fabric' => [
            'lead' => 'Cotton and blended cloth, woven here and sold by the roll in your colour and weight.',
            'intro_title' => 'The cloth behind our clothes, sold on its own',
            'intro' => [
                'We weave fabric alongside our garment work. If you run a tailoring shop, a workshop or a clothing line of your own, you can buy the same cloth by the roll.',
                'Tell us what you are making and we suggest a weave and a weight. If you already know exactly what you need, send us a piece and we match it.',
            ],
            'garments_title' => 'What we weave',
            'garments' => [
                ['Plain weave', 'The simplest and most common weave. Smooth and even, for shirts and linings.'],
                ['Twill', 'You can see fine diagonal lines in it. Stronger and heavier, for trousers and workwear.'],
                ['Satin weave', 'Smooth with a soft shine, for linings and dress fabrics.'],
                ['Ripstop', 'A grid of stronger threads keeps a small tear from spreading.'],
                ['Dyed and printed cloth', 'Plain colours matched to your shade, or stripes, checks and a design of your own.'],
            ],
            'fabrics_title' => 'What it is made of',
            'fabrics' => [
                ['Cotton', 'Soft and breathable, and comfortable in the heat.'],
                ['Poly-cotton', 'Creases less, dries faster and lasts longer.'],
                ['Other blends', 'Tell us what the cloth has to do and we suggest a mix.'],
            ],
            'options' => [
                ['Colour', 'Dyed to your shade and kept the same from roll to roll.'],
                ['Weight', 'Light for shirts, medium for trousers, heavy for workwear.'],
                ['Quantity', 'Sold by the roll. Tell us how much you need.'],
                ['Finish', 'Washed and softened, or left crisp.'],
            ],
            'questions' => [
                ['Can you match a fabric we already use?', 'Send us a piece. We look at the weave, the weight and the colour, and tell you how close we can get.'],
                ['Can I see it before I buy a roll?', 'Yes. Ask us for a swatch.'],
                ['Do you also sew it?', 'Yes, that is most of what we do. If you want the cloth made into garments, look at our clothing lines.'],
            ],
            'alts' => ['Rolls of cloth in navy, olive, beige, white and blue stacked together', 'Threads running through a loom', 'Rolls of natural, white and light blue cloth on a wooden shelf'],
            'gallery' => [['Dyed cotton', 'Folded cotton in red, orange, yellow, green and blue'], ['White shirting', 'Rolls of white cloth on shelves'], ['Yarn', 'Spools of white yarn on a machine'], ['Woven cotton', 'Close view of woven navy cotton']],
        ],
    ],
];
