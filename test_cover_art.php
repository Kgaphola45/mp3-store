<?php
require_once __DIR__ . '/includes/functions.php';

$tests = [
    ['title' => 'Alienated', 'artist' => 'Urban Echo', 'expected' => 'AU'],
    ['title' => 'Lost', 'artist' => '', 'expected' => 'L'],
    ['title' => '', 'artist' => 'Unknown', 'expected' => 'U'],
];

foreach ($tests as $test) {
    $data = get_cover_art_data($test['title'], $test['artist']);
    echo "Testing '{$test['title']}' by '{$test['artist']}':\n";
    echo "  Background: {$data['background']}\n";
    echo "  Text: {$data['text']}\n";
    
    if ($data['text'] !== $test['expected']) {
        echo "  FAIL: Expected {$test['expected']}, got {$data['text']}\n";
        exit(1);
    }
    
    if (strpos($data['background'], 'linear-gradient') === false) {
        echo "  FAIL: Background format incorrect\n";
        exit(1);
    }
}

echo "All tests passed.\n";
