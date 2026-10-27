<?php

namespace Tests\Unit;

use App\Support\OutreachBodyFormatter;
use PHPUnit\Framework\TestCase;

class OutreachBodyFormatterTest extends TestCase
{
    public function test_it_removes_contact_details_already_present_in_the_branded_footer(): void
    {
        $body = <<<'TEXT'
You can see examples of my work at https://dylenwolff.com/work

Kind regards,
Dylen Wolff
Software Engineer and Digital Solutions Developer
https://dylenwolff.com
connect@dylenwolff.com
TEXT;

        $formatted = OutreachBodyFormatter::forDelivery($body);

        $this->assertStringEndsWith("Kind regards,\nDylen Wolff", $formatted);
        $this->assertSame(1, substr_count($formatted, 'https://dylenwolff.com'));
        $this->assertStringNotContainsString('connect@dylenwolff.com', $formatted);
        $this->assertStringNotContainsString('Software Engineer and Digital Solutions Developer', $formatted);
    }
}
