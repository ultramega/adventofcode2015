<?php

$words = file('input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$good = 0;
foreach($words as $word) {
    $letters = str_split($word);
    $has2LetterPair = false;
    $hasRepeatLetter = false;
    foreach($letters as $key => $value) {
        if(!$has2LetterPair && $key > 0) {
            $has2LetterPair = strpos($word, $letters[$key - 1] . $value, $key + 1) !== false;
        }
        if(!$hasRepeatLetter && $key > 1) {
            $hasRepeatLetter = $value === $letters[$key - 2];
        }
        if($has2LetterPair && $hasRepeatLetter) {
            $good++;
            break;
        }
    }
}

echo 'Answer: ' . $good . PHP_EOL;
