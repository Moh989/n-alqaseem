رسالة جديدة من نموذج التواصل في الموقع
=======================================

الاسم: {!! $contactMessage->name !!}
الجهة: {!! $contactMessage->organization ?: '—' !!}
البريد الإلكتروني: {!! $contactMessage->email !!}
نوع الاستفسار: {!! $contactMessage->inquiryLabel('ar') !!}
لغة الصفحة: {!! $contactMessage->locale === 'en' ? 'English' : 'العربية' !!}
التاريخ: {!! $contactMessage->created_at?->format('Y-m-d H:i') !!}

الرسالة:
{!! $contactMessage->message !!}

---
عرض الرسالة في لوحة الإدارة: {!! route('admin.messages.show', $contactMessage) !!}
