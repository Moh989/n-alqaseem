<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['locale', 'name', 'organization', 'email', 'inquiry_type', 'message', 'ip_address', 'user_agent', 'read_at', 'archived_at'])]
class ContactMessage extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function inquiryLabel(string $locale = 'ar'): string
    {
        return __('contact.types.'.$this->inquiry_type, [], $locale);
    }
}
