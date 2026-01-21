<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'title',
        'description',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get all contact links for contact section
     */
    public function contactLinks()
    {
        if ($this->section === 'contact') {
            return ContactLink::active()->orderBy('order')->get();
        }
        return collect([]);
    }
}
