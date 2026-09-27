<?php

/**
 * Service categories and the ten registered activities.
 *
 * Copy stays within each registered activity: it makes no claims about owned plants,
 * fleets, licences, production capacity, equipment or certificates.
 *
 * @return array{categories: list<array<string, mixed>>, services: list<array<string, mixed>>}
 */
return [
    'categories' => [
        [
            'slug' => 'construction-infrastructure', 'sort' => 1,
            'name_ar' => 'الإنشاءات والبنية التحتية', 'name_en' => 'Construction & Infrastructure',
            'description_ar' => 'الأعمال المدنية والإنشائية، والطرق، والمباني السكنية والتجارية.',
            'description_en' => 'Civil and structural works, roads, and residential and commercial buildings.',
        ],
        [
            'slug' => 'energy-oil', 'sort' => 2,
            'name_ar' => 'الطاقة والنفط', 'name_en' => 'Energy & Oil',
            'description_ar' => 'أنشطة مسجلة مرتبطة بقطاع النفط ومشتقاته ومنتجاته.',
            'description_en' => 'Registered activities related to the oil sector, its derivatives and products.',
        ],
        [
            'slug' => 'trading-support', 'sort' => 3,
            'name_ar' => 'التجارة والخدمات المساندة', 'name_en' => 'Trading & Support Services',
            'description_ar' => 'التجارة العامة والخدمات العامة والبحرية والدعم والإسناد.',
            'description_en' => 'General trading, general and marine services, and support and logistics.',
        ],
    ],

    'services' => [
        [
            'category' => 'construction-infrastructure', 'slug' => 'general-contracting', 'sort' => 1, 'icon' => 'building-2', 'media' => 'hero-construction',
            'title_ar' => 'المقاولات العامة والإنشاءات',
            'title_en' => 'General Contracting & Construction',
            'summary_ar' => 'تنفيذ الأعمال المدنية والإنشائية للمباني السكنية والتجارية وفق الرسومات والمواصفات المعتمدة لكل مشروع.',
            'summary_en' => 'Civil and structural works for residential and commercial buildings, delivered to each project’s approved drawings and specifications.',
            'body_ar' => "تنفّذ الشركة أعمال المقاولات العامة ذات الطبيعة المدنية والإنشائية للمباني السكنية والتجارية. ويُحدَّد لكل مشروع نطاق الأعمال الهيكلية والتشطيبات والتجهيزات والأعمال المساندة وفق وثائق العقد والرسومات والمواصفات المعتمدة.\n\n## ما يشمله النشاط\n- الأعمال الإنشائية والهيكلية للمباني السكنية والتجارية.\n- أعمال الصب والجدران والأعمال المدنية المرتبطة بها.\n- أعمال الإكمال والتشطيب ضمن النطاق المتعاقد عليه.\n- الأعمال المساندة التي تحددها وثائق المشروع.\n\n## مراحل التنفيذ\nتمتد مراحل العمل، بحسب نطاق كل تعاقد، من الدراسة والتصميم الأولي والتفصيلي إلى الإنشاء، ثم أعمال الصيانة والتشغيل عند شمولها بالعقد. وتختلف المتطلبات والمواد وأساليب التنفيذ من مشروع لآخر، لذا يُضبط النطاق قبل بدء الأعمال.",
            'body_en' => "The company carries out general contracting works of a civil and structural nature for residential and commercial buildings. For each project, the scope of structural works, finishes, fit-out and supporting works is defined by the contract documents and the approved drawings and specifications.\n\n## What the activity covers\n- Structural and construction works for residential and commercial buildings.\n- Concrete casting, walls and related civil works.\n- Completion and finishing works within the contracted scope.\n- Supporting works defined in the project documents.\n\n## Delivery stages\nDepending on the scope of each contract, work can span study, preliminary and detailed design, construction, and then maintenance and operation where included. Requirements, materials and methods vary from project to project, so the scope is agreed before work begins.",
        ],
        [
            'category' => 'construction-infrastructure', 'slug' => 'infrastructure-roads', 'sort' => 2, 'icon' => 'route', 'media' => 'roads',
            'title_ar' => 'أعمال البنية التحتية والطرق',
            'title_en' => 'Infrastructure & Roads',
            'summary_ar' => 'إنشاء الطرق وتطويرها وتوسعتها وأعمال البنية التحتية المرتبطة بها ضمن حدود المناقصات والعقود المعتمدة.',
            'summary_en' => 'Road construction, development and widening, and related infrastructure works, within the scope of approved tenders and contracts.',
            'body_ar' => "تنفّذ الشركة أعمال الطرق والبنية التحتية، بما يشمل أعمال التطوير والتوسعة، ضمن حدود المناقصات والعقود والمخططات المعتمدة لكل مشروع.\n\n## ما يشمله النشاط\n- إنشاء الطرق وتطويرها.\n- أعمال التعريض والتوسعة.\n- أعمال البنية التحتية المرتبطة بالطرق والمشروعات الإنشائية.",
            'body_en' => "The company carries out road and infrastructure works, including development and widening, within the scope of the tenders, contracts and approved designs of each project.\n\n## What the activity covers\n- Road construction and development.\n- Widening and expansion works.\n- Infrastructure works related to roads and construction projects.",
        ],
        [
            'category' => 'energy-oil', 'slug' => 'oil-derivatives-transport', 'sort' => 3, 'icon' => 'truck', 'media' => 'tanker',
            'title_ar' => 'نقل المشتقات النفطية',
            'title_en' => 'Oil Derivatives Transportation',
            'summary_ar' => 'خدمات نقل المشتقات النفطية ضمن النشاط المسجل للشركة، وفق متطلبات العميل والضوابط والتعليمات النافذة.',
            'summary_en' => 'Transportation of oil derivatives within the company’s registered activity, in line with client requirements and applicable regulations.',
            'body_ar' => "يُعدّ نقل المشتقات النفطية أحد الأنشطة المدرجة ضمن نشاط الشركة المسجل. وتُحدَّد تفاصيل كل خدمة — من نوع المنتج والكميات ونقاط التحميل والتسليم إلى الجداول الزمنية ومتطلبات السلامة — في الاتفاق المبرم مع العميل، وبما يتوافق مع الضوابط والتعليمات النافذة لدى الجهات المختصة.\n\n## ما يتضمنه التنسيق\n- تحديد نوع المنتج والكميات المطلوبة.\n- الاتفاق على نقاط التحميل والتسليم والجدول الزمني.\n- مراعاة اشتراطات السلامة والتوثيق المطلوبة.\n- التنسيق مع الجهات ذات العلاقة وفق ما يحدده العقد.",
            'body_en' => "Transportation of oil derivatives is one of the activities listed in the company’s registered objects. The details of each service — product type and quantities, loading and delivery points, schedules and safety requirements — are set out in the agreement with the client and in line with the regulations of the competent authorities.\n\n## What coordination covers\n- Defining the product type and required quantities.\n- Agreeing loading and delivery points and the schedule.\n- Observing the required safety and documentation conditions.\n- Coordinating with relevant parties as defined by the contract.",
        ],
        [
            'category' => 'energy-oil', 'slug' => 'oil-services', 'sort' => 4, 'icon' => 'fuel', 'media' => 'pumpjack',
            'title_ar' => 'الخدمات النفطية',
            'title_en' => 'Oil Services',
            'summary_ar' => 'خدمات مساندة لمشروعات قطاع النفط والطاقة، يُحدَّد نطاقها وفق متطلبات الجهة المستفيدة وشروط التعاقد.',
            'summary_en' => 'Support services for oil and energy sector projects, scoped to the beneficiary’s requirements and the contract terms.',
            'body_ar' => "تشمل الأنشطة المسجلة للشركة تقديم الخدمات النفطية. ويُتفق على طبيعة الخدمة ونطاقها ومواصفاتها مع الجهة المستفيدة، وفق وثائق التعاقد والمتطلبات الفنية المعتمدة.\n\n## مجالات يمكن التعاون فيها\n- الأعمال المدنية والإنشائية المساندة في مواقع قطاع النفط والطاقة.\n- توريد المواد والخدمات التي تحتاجها المشروعات النفطية.\n- الخدمات العامة المساندة للمواقع ضمن النطاق المتعاقد عليه.",
            'body_en' => "The company’s registered activities include oil services. The nature, scope and specifications of each service are agreed with the beneficiary in accordance with the contract documents and approved technical requirements.\n\n## Possible areas of collaboration\n- Supporting civil and construction works at oil and energy sites.\n- Supply of materials and services required by oil projects.\n- General site support services within the contracted scope.",
        ],
        [
            'category' => 'energy-oil', 'slug' => 'oxidized-asphalt-production', 'sort' => 5, 'icon' => 'layers', 'media' => 'bitumen-roof',
            'title_ar' => 'إنتاج الأسفلت المؤكسد',
            'title_en' => 'Oxidized Asphalt Production',
            'summary_ar' => 'نشاط مسجل يتصل بمنتجات الأسفلت المؤكسد المستخدمة في أعمال العزل والتطبيقات الإنشائية، وتُحدَّد مواصفاته وشروط توريده في كل اتفاق.',
            'summary_en' => 'A registered activity related to oxidized asphalt used in waterproofing and construction applications; specifications and supply terms are set in each agreement.',
            'body_ar' => "يُدرج إنتاج الأسفلت المؤكسد ضمن الأنشطة المسجلة للشركة. ويُستخدم هذا النوع من الأسفلت عادةً في تطبيقات العزل المائي وأعمال الأسطح وبعض التطبيقات الصناعية والإنشائية. وتُحدَّد الدرجات والمواصفات والكميات وشروط التوريد والتسليم وفق الاتفاق مع العميل والمواصفات القياسية المطلوبة.\n\n## عند طلب عرض\n- الدرجة أو المواصفة الفنية المطلوبة.\n- الكميات وطريقة التعبئة ومواعيد التسليم.\n- موقع التسليم وأي متطلبات فحص أو مطابقة.",
            'body_en' => "Production of oxidized asphalt is one of the company’s registered activities. This type of asphalt is typically used in waterproofing, roofing and certain industrial and construction applications. Grades, specifications, quantities and supply and delivery terms are defined by agreement with the client and the required standard specifications.\n\n## When requesting a proposal\n- The required grade or technical specification.\n- Quantities, packaging and delivery dates.\n- Delivery location and any testing or conformity requirements.",
        ],
        [
            'category' => 'energy-oil', 'slug' => 'lubricating-oil-production', 'sort' => 6, 'icon' => 'droplet', 'media' => 'gears',
            'title_ar' => 'إنتاج زيت التزييت',
            'title_en' => 'Lubricating Oil Production',
            'summary_ar' => 'نشاط مسجل يتصل بزيوت التزييت، وتُحدَّد أنواعها ومواصفاتها وشروط توريدها بحسب الاتفاق مع العميل.',
            'summary_en' => 'A registered activity related to lubricating oils; types, specifications and supply terms are agreed with each client.',
            'body_ar' => "يُدرج إنتاج زيت التزييت ضمن الأنشطة المسجلة للشركة. وتُستخدم زيوت التزييت في المحركات والمعدات والتطبيقات الصناعية المختلفة، وتُحدَّد الأنواع والدرجات والمواصفات والكميات وطريقة التعبئة والتسليم وفق الاتفاق مع العميل.\n\n## عند طلب عرض\n- نوع الزيت ودرجة اللزوجة أو المواصفة المطلوبة.\n- الاستخدام المقصود: محركات، معدات، تطبيقات صناعية.\n- الكميات وطريقة التعبئة ومواعيد التسليم.",
            'body_en' => "Production of lubricating oil is one of the company’s registered activities. Lubricating oils are used in engines, equipment and a range of industrial applications. Types, grades, specifications, quantities, packaging and delivery are defined by agreement with the client.\n\n## When requesting a proposal\n- Oil type and the required viscosity grade or specification.\n- Intended use: engines, equipment or industrial applications.\n- Quantities, packaging and delivery dates.",
        ],
        [
            'category' => 'trading-support', 'slug' => 'general-trading', 'sort' => 7, 'icon' => 'package', 'media' => 'containers-port',
            'title_ar' => 'التجارة العامة',
            'title_en' => 'General Trading',
            'summary_ar' => 'توريد المواد والسلع والخدمات التي تحتاجها المشروعات، وفق المواصفات وشروط التسليم المتفق عليها مع العميل.',
            'summary_en' => 'Supply of materials, goods and services needed by projects, to the specifications and delivery terms agreed with the client.',
            'body_ar' => "يمثّل نشاط التجارة العامة جزءاً من الوصف القانوني والتشغيلي للشركة، ويرتبط بدعم احتياجات المشروعات. وتخضع طبيعة المواد أو الخدمات المورّدة ومواصفاتها وشروط تسليمها للاتفاقات الفعلية مع العميل.\n\n## ما يشمله النشاط\n- توريد مواد البناء والمستلزمات المرتبطة بالمشروعات.\n- توريد السلع والمواد وفق المواصفات المطلوبة.\n- تنسيق مواعيد التوريد والتسليم بحسب برنامج المشروع.",
            'body_en' => "General trading is part of the company’s stated legal and operational description and supports project requirements. The goods or services supplied, their specifications and delivery conditions depend on the specific client agreement.\n\n## What the activity covers\n- Supply of building materials and project-related items.\n- Supply of goods and materials to the required specifications.\n- Coordinating supply and delivery dates with the project schedule.",
        ],
        [
            'category' => 'trading-support', 'slug' => 'general-services', 'sort' => 8, 'icon' => 'wrench', 'media' => 'tools',
            'title_ar' => 'الخدمات العامة',
            'title_en' => 'General Services',
            'summary_ar' => 'خدمات عامة مساندة للمشروعات والمواقع، يُحدَّد نطاقها ومدتها ومتطلباتها في كل تعاقد.',
            'summary_en' => 'General support services for projects and sites, with scope, duration and requirements set in each contract.',
            'body_ar' => "تشمل الأنشطة المسجلة للشركة تقديم الخدمات العامة. ويُحدَّد نوع الخدمة ونطاقها ومدتها ومستوى الأداء المطلوب وفق وثائق التعاقد ومتطلبات الجهة المستفيدة.\n\n## مجالات الخدمات\n- خدمات مساندة لمواقع العمل والمشروعات.\n- أعمال الصيانة العامة ضمن النطاق المتعاقد عليه.\n- خدمات تشغيلية وإدارية يحددها العقد.",
            'body_en' => "The company’s registered activities include general services. The type of service, its scope, duration and required level of performance are defined by the contract documents and the beneficiary’s requirements.\n\n## Service areas\n- Support services for work sites and projects.\n- General maintenance works within the contracted scope.\n- Operational and administrative services as defined by the contract.",
        ],
        [
            'category' => 'trading-support', 'slug' => 'marine-services', 'sort' => 9, 'icon' => 'ship', 'media' => 'ship',
            'title_ar' => 'الخدمات البحرية',
            'title_en' => 'Marine Services',
            'summary_ar' => 'خدمات مرتبطة بالقطاع البحري والموانئ ضمن النشاط المسجل للشركة، يُتفق على طبيعتها ونطاقها في كل تعاقد.',
            'summary_en' => 'Services related to the maritime sector and ports within the company’s registered activity, with nature and scope agreed in each contract.',
            'body_ar' => "تُدرج الخدمات البحرية ضمن الأنشطة المسجلة للشركة. ويُتفق على نوع الخدمة ونطاقها وموقع تنفيذها ومتطلباتها الفنية والتنظيمية مع الجهة المستفيدة، وبما يتوافق مع التعليمات النافذة لدى الجهات المختصة.\n\n## مجالات يمكن التعاون فيها\n- خدمات مساندة للأعمال في الموانئ والمواقع البحرية.\n- توريد المواد والخدمات المرتبطة بالعمليات البحرية.\n- التنسيق اللوجستي المرتبط بالأعمال البحرية ضمن النطاق المتعاقد عليه.",
            'body_en' => "Marine services are among the company’s registered activities. The type of service, its scope, location and technical and regulatory requirements are agreed with the beneficiary, in line with the regulations of the competent authorities.\n\n## Possible areas of collaboration\n- Support services for works at ports and maritime sites.\n- Supply of materials and services related to marine operations.\n- Logistics coordination for marine works within the contracted scope.",
        ],
        [
            'category' => 'trading-support', 'slug' => 'support-logistics', 'sort' => 10, 'icon' => 'boxes', 'media' => 'storage',
            'title_ar' => 'الدعم والإسناد',
            'title_en' => 'Support & Logistics',
            'summary_ar' => 'خدمات دعم وإسناد للمشروعات والجهات المتعاقدة، تشمل التنسيق والتوريد والخدمات المساندة وفق نطاق كل عقد.',
            'summary_en' => 'Support and logistics services for projects and contracting entities, including coordination, supply and support services within each contract’s scope.',
            'body_ar' => "يشمل نشاط الدعم والإسناد تقديم الخدمات المساندة التي تحتاجها المشروعات والجهات المتعاقدة لإنجاز أعمالها. ويُحدَّد نطاق الخدمات ومدتها ومتطلباتها في وثائق التعاقد.\n\n## ما يمكن أن يشمله النشاط\n- التنسيق اللوجستي لاحتياجات المواقع.\n- توريد المستلزمات والمواد المساندة.\n- الخدمات المساندة لفرق العمل في المواقع ضمن النطاق المتفق عليه.",
            'body_en' => "Support and logistics covers the supporting services that projects and contracting entities need to carry out their work. The scope, duration and requirements of the services are defined in the contract documents.\n\n## What the activity may include\n- Logistics coordination for site requirements.\n- Supply of supporting items and materials.\n- Support services for site teams within the agreed scope.",
        ],
    ],
];
