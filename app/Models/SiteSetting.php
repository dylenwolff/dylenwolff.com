<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name', 'professional_title', 'availability', 'hero_heading', 'hero_accent',
    'hero_intro', 'about_heading', 'about_body', 'email', 'linkedin_url',
    'github_url', 'upwork_url', 'contact_heading', 'contact_body',
])]
class SiteSetting extends Model
{
    public static function current(): self
    {
        return static::query()->first() ?? new static([
            'name' => 'Dylen Andrew Wolff',
            'professional_title' => 'Systems Engineer & Digital Solutions Developer',
            'availability' => 'Available for selected projects',
            'hero_heading' => 'I turn ideas and everyday problems into',
            'hero_accent' => 'digital solutions that work.',
            'hero_intro' => 'From professional websites and custom business tools to reliable technology support and training, I build around what you actually need.',
            'about_heading' => 'Technology should make life and work easier.',
            'about_body' => 'I work across development, systems, infrastructure, and education. That range helps me look beyond a single tool and find the clearest, most practical solution for each client. As an IT lecturer, I also care deeply about making complex ideas understandable. The same clarity shapes how I communicate, document, and deliver every project.',
            'email' => 'hello@dylenwolff.com',
            'linkedin_url' => 'https://www.linkedin.com/in/dylenaw/',
            'github_url' => 'https://github.com/dylenwolff',
            'upwork_url' => 'https://www.upwork.com/freelancers/~01d1b15fc05390f9a2',
            'contact_heading' => 'Have an idea, a problem, or a project?',
            'contact_body' => 'Tell me what you are trying to accomplish. I will help you find a practical way forward—even if the answer is simpler than expected.',
        ]);
    }
}
