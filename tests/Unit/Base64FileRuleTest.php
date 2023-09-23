<?php

namespace Tests\Unit;

use App\Rules\Base64File;
use PHPUnit\Framework\TestCase;

class Base64FileRuleTest extends TestCase
{
    public function test_validate_valid_base64_string(): void
    {
        $passed = true;
        $value = base64_encode('I will be base 64 encoded');

        $fail = function () use (&$passed) {
            $passed = false;
        };

        $base64File = new Base64File();
        $base64File->validate('test-attribute', $value, $fail);

        self::assertTrue($passed);
    }

    public function test_validate_invalid_base64_string(): void
    {
        $passed = true;
        $value = 'wrong-base-64-string';

        $fail = function () use (&$passed) {
            $passed = false;
        };

        $base64File = new Base64File();
        $base64File->validate('test-attribute', $value, $fail);

        self::assertFalse($passed);
    }
}
