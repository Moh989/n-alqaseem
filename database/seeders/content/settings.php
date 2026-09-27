<?php

/**
 * Initial site settings.
 *
 * "approved" values come from the company's own material and the brief. Anything that could
 * not be verified is seeded as "pending" (and, where helpful, pre-filled only as a suggestion)
 * so that it is never published until an administrator confirms it.
 *
 * @return list<array<string, mixed>>
 */
return [
    // Company
    [
        'key' => 'short_name', 'group' => 'company', 'type' => 'text', 'is_translatable' => true,
        'value_ar' => 'نور القسيم', 'value_en' => 'Noor AlQaseem',
        'status' => 'approved', 'label_ar' => 'الاسم المختصر', 'label_en' => 'Short name',
    ],
    [
        'key' => 'legal_name_ar', 'group' => 'company', 'type' => 'textarea', 'is_translatable' => false,
        'value' => 'شركة نور القسيم للتجارة والمقاولات العامة ونقل المشتقات النفطية والخدمات العامة والنفطية والبحرية والدعم والإسناد وإنتاج الأسفلت المؤكسد وزيت التزييت محدودة المسؤولية',
        'status' => 'approved', 'label_ar' => 'الاسم القانوني (عربي)', 'label_en' => 'Legal name (Arabic)',
    ],
    [
        'key' => 'legal_name_en', 'group' => 'company', 'type' => 'textarea', 'is_translatable' => false,
        'value' => 'Noor AlQaseem Company for General Trading and Contracting, Transportation of Oil Derivatives, General, Oil and Marine Services, Support and Logistics, and Production of Oxidized Asphalt and Lubricating Oil, Limited Liability',
        'status' => 'pending', 'label_ar' => 'الاسم القانوني (إنجليزي)', 'label_en' => 'Legal name (English)',
        'admin_note' => 'ترجمة مقترحة فقط. يجب مطابقتها مع الاسم الإنجليزي الرسمي في شهادة التسجيل قبل الاعتماد.',
    ],
    [
        'key' => 'company_type', 'group' => 'company', 'type' => 'text', 'is_translatable' => true,
        'value_ar' => 'شركة عراقية محدودة المسؤولية', 'value_en' => 'Iraqi limited liability company',
        'status' => 'approved', 'label_ar' => 'الكيان القانوني', 'label_en' => 'Legal entity',
    ],
    [
        'key' => 'founding_year', 'group' => 'company', 'type' => 'year', 'is_translatable' => false,
        'value' => null,
        'status' => 'pending', 'label_ar' => 'سنة التأسيس', 'label_en' => 'Founding year',
        'admin_note' => 'غير مذكورة في الملفات المقدمة. تُدخل من وثيقة التأسيس الرسمية ثم تُعتمد.',
    ],

    // Contact
    [
        'key' => 'city', 'group' => 'contact', 'type' => 'text', 'is_translatable' => true,
        'value_ar' => 'بغداد', 'value_en' => 'Baghdad',
        'status' => 'approved', 'label_ar' => 'المدينة', 'label_en' => 'City',
    ],
    [
        'key' => 'district', 'group' => 'contact', 'type' => 'text', 'is_translatable' => true,
        'value_ar' => 'حي الكيلاني', 'value_en' => 'Al-Kilani District',
        'status' => 'approved', 'label_ar' => 'الحي', 'label_en' => 'District',
        'admin_note' => 'يرد الاسم الإنجليزي في الملف القديم بصيغة «Al-Ghailani» وفي الأحدث «Al-Kilani». يرجى تأكيد الصيغة المعتمدة.',
    ],
    [
        'key' => 'address', 'group' => 'contact', 'type' => 'text', 'is_translatable' => true,
        'value_ar' => 'العراق – بغداد – حي الكيلاني', 'value_en' => 'Al-Kilani District, Baghdad, Iraq',
        'status' => 'approved', 'label_ar' => 'العنوان الكامل', 'label_en' => 'Full address',
    ],
    [
        'key' => 'email', 'group' => 'contact', 'type' => 'email', 'is_translatable' => false,
        'value' => 'info@n-alqaseem.com',
        'status' => 'approved', 'label_ar' => 'البريد الإلكتروني', 'label_en' => 'Email',
    ],
    [
        'key' => 'maps_query', 'group' => 'contact', 'type' => 'text', 'is_translatable' => false,
        'value' => 'حي الكيلاني، بغداد، العراق',
        'status' => 'approved', 'label_ar' => 'نص البحث في الخرائط', 'label_en' => 'Maps search text',
        'admin_note' => 'يُستخدم لرابط بحث في خرائط Google. لا تُعرض خريطة مضمّنة لعدم تأكيد نقطة الموقع الدقيقة.',
    ],

    // Social (none provided — kept empty and pending)
    [
        'key' => 'social_linkedin', 'group' => 'social', 'type' => 'url', 'is_translatable' => false,
        'value' => null, 'status' => 'pending', 'label_ar' => 'LinkedIn', 'label_en' => 'LinkedIn',
    ],
    [
        'key' => 'social_facebook', 'group' => 'social', 'type' => 'url', 'is_translatable' => false,
        'value' => null, 'status' => 'pending', 'label_ar' => 'Facebook', 'label_en' => 'Facebook',
    ],
    [
        'key' => 'social_instagram', 'group' => 'social', 'type' => 'url', 'is_translatable' => false,
        'value' => null, 'status' => 'pending', 'label_ar' => 'Instagram', 'label_en' => 'Instagram',
    ],

    // Legal review of the draft privacy and terms texts
    [
        'key' => 'legal_review', 'group' => 'legal', 'type' => 'boolean', 'is_translatable' => false,
        'value' => null, 'status' => 'pending',
        'label_ar' => 'تمت المراجعة القانونية لنصوص الخصوصية والشروط', 'label_en' => 'Privacy and terms texts legally reviewed',
        'admin_note' => 'النصوص الحالية مسودات قابلة للمراجعة. حدّد «معتمد للنشر» بعد مراجعتها من مختص قانوني.',
    ],

    // Notifications (internal, never displayed)
    [
        'key' => 'notify_email', 'group' => 'notifications', 'type' => 'email', 'is_translatable' => false,
        'value' => 'info@n-alqaseem.com',
        'status' => 'approved', 'label_ar' => 'بريد استلام إشعارات النموذج', 'label_en' => 'Contact notification email',
        'admin_note' => 'يُرسل إليه إشعار بكل رسالة جديدة عند ضبط إعدادات البريد (SMTP) وتفعيل CONTACT_NOTIFY.',
    ],

    // Documents (managed on the "Company profile PDF" screen)
    [
        'key' => 'profile_pdf_ar', 'group' => 'documents', 'type' => 'file', 'is_translatable' => false,
        'value' => null, 'status' => 'pending', 'label_ar' => 'الملف التعريفي PDF (عربي)', 'label_en' => 'Company profile PDF (Arabic)',
        'admin_note' => 'تُرفع نسخة عامة خالية من المستمسكات والوثائق الرسمية.',
    ],
    [
        'key' => 'profile_pdf_en', 'group' => 'documents', 'type' => 'file', 'is_translatable' => false,
        'value' => null, 'status' => 'pending', 'label_ar' => 'الملف التعريفي PDF (إنجليزي)', 'label_en' => 'Company profile PDF (English)',
        'admin_note' => 'تُرفع نسخة عامة خالية من المستمسكات والوثائق الرسمية.',
    ],
];
