<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

// Load the contact module so the validation function can be tested.
// This file is in tests/, so the contact functions are one level up in actions/.
require __DIR__ . '/../actions/contacts.php';

class ContactValidationTest extends TestCase
{
    #[DataProvider('validationTestData')]
    public function testContactValidation(array $input, bool $expected): void
    {
        // Call the validation function in actions/contacts.php.
        // The function checks name, email, and message values.
        $actual = contact_validate_submission($input);

        // Assert the function returns the expected true/false result.
        $this->assertEquals($expected, $actual);
    }

    public static function validationTestData(): array
    {
        return [
            'valid submission' => [
                ['name' => 'Aubrey Garcia', 'email' => 'aubrey@example.com', 'message' => 'Hello, I want to know about events.'],
                true,
            ],
            'missing name' => [
                ['name' => '', 'email' => 'aubrey@example.com', 'message' => 'Hello'],
                false,
            ],
            'invalid email' => [
                ['name' => 'Aubrey Garcia', 'email' => 'aubrey-at-example.com', 'message' => 'Hello'],
                false,
            ],
            'empty message' => [
                ['name' => 'Aubrey Garcia', 'email' => 'aubrey@example.com', 'message' => ''],
                false,
            ],
            'message too long' => [
                ['name' => 'Aubrey Garcia', 'email' => 'aubrey@example.com', 'message' => str_repeat('x', 8001)],
                false,
            ],
        ];
    }
}
