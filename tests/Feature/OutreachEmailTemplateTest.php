<?php

namespace Tests\Feature;

use App\Mail\OutreachMessageMail;
use Tests\TestCase;

class OutreachEmailTemplateTest extends TestCase
{
    public function test_work_url_uses_the_complete_clickable_destination(): void
    {
        $html = (new OutreachMessageMail(
            'Test subject',
            'You can see examples of my work at https://dylenwolff.com/work',
            'Test team',
        ))->render();

        $this->assertStringContainsString(
            'href="https://dylenwolff.com/work"',
            $html,
        );
        $this->assertStringNotContainsString('href="https://dy"', $html);
    }
}
