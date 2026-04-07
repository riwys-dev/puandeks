<?php

function generateSlug($text, $pdo, $table = 'companies') {

    // Turkish
    $search  = ['ç','ğ','ı','ö','ş','ü','Ç','Ğ','İ','Ö','Ş','Ü'];
    $replace = ['c','g','i','o','s','u','c','g','i','o','s','u'];
    $text = str_replace($search, $replace, $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');

    // if empty fallback
    if (empty($text)) {
        $text = 'item';
    }

    $baseSlug = $text;
    $counter = 2;

    // Same slug?
    while (true) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE slug = ?");
        $stmt->execute([$text]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            break;
        }

        $text = $baseSlug . '-' . $counter;
        $counter++;
    }

    return $text;
}
