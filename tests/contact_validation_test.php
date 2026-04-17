<?php

declare(strict_types=1);

require __DIR__ . '/../actions/contacts.php';

$tests = [
    [
        'name' => 'valid submission',
        'input' => ['name' => 'Aubrey Garcia', 'email' => 'aubrey@example.com', 'message' => 'Hello, I want to know about events.'],
        'expected' => true,
    ],
    [
        'name' => 'missing name',
        'input' => ['name' => '', 'email' => 'aubrey@example.com', 'message' => 'Hello'],
        'expected' => false,
    ],
    [
        'name' => 'invalid email',
        'input' => ['name' => 'Aubrey Garcia', 'email' => 'aubrey-at-example.com', 'message' => 'Hello'],
        'expected' => false,
    ],
    [
        'name' => 'empty message',
        'input' => ['name' => 'Aubrey Garcia', 'email' => 'aubrey@example.com', 'message' => ''],
        'expected' => false,
    ],
    [
        'name' => 'message too long',
        'input' => ['name' => 'Aubrey Garcia', 'email' => 'aubrey@example.com', 'message' => str_repeat('x', 8001)],
        'expected' => false,
    ],
];

$failures = 0;
foreach ($tests as $test) {
    $actual = contact_validate_submission($test['input']);
    $pass = $actual === $test['expected'];
    if ($pass) {
        echo "PASS: {$test['name']}\n";
    } else {
        echo "FAIL: {$test['name']} - expected " . var_export($test['expected'], true) . " got " . var_export($actual, true) . "\n";
        $failures++;
    }
}

if ($failures === 0) {
    echo "\nAll contact validation tests passed.\n";
    exit(0);
}

echo "\n{$failures} tests failed.\n";
exit(1);
