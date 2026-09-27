<?php

return [
    'fields' => [
        'name' => 'Name',
        'organization' => 'Organization / company',
        'email' => 'Email',
        'inquiry_type' => 'Inquiry type',
        'message' => 'Message',
        'consent' => 'Privacy policy consent',
        'cf-turnstile-response' => 'Security check',
    ],
    'placeholders' => [
        'message' => 'Briefly describe the project or request: scope, location and expected schedule.',
    ],
    'optional' => 'optional',
    'required' => 'required',
    'choose' => 'Choose an inquiry type',
    'types' => [
        'general' => 'General inquiry',
        'quote' => 'Proposal request / project collaboration',
        'tender' => 'Tender or invitation to bid',
        'supply' => 'Supply and trading',
        'partnership' => 'Partnership',
        'other' => 'Other',
    ],
    'consent_label' => 'I agree that my details may be used to respond to my inquiry in line with the :link.',
    'consent_link' => 'privacy policy',
    'submit' => 'Send message',
    'sending' => 'Sending…',
    'success' => 'Thank you for contacting us. We have received your message and will get back to you as soon as possible.',
    'error_summary' => 'Please review the following fields:',
    'throttled' => 'You have sent several messages in a short time. Please try again later or contact us by email.',
    'errors' => [
        'token' => 'We could not verify the form. Please wait a moment and submit again.',
        'expired' => 'The form has expired. Please submit it again.',
        'captcha' => 'The security check could not be completed. Please try again.',
    ],
    'characters' => 'characters',
];
