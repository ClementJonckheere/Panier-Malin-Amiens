<?php

$baseUrl = 'https://www.supermarchesmatch.fr/fr/recherche?recherche=';
$productName = 'riz';

$searchUrl = $baseUrl . urlencode($productName);

$command = "node supermarche_match_script.js " . escapeshellarg($searchUrl) . " 2>&1";
$output = shell_exec($command);

$jsonEnd = strrpos($output, ']');
if ($jsonEnd !== false) {
    $output = substr($output, 0, $jsonEnd + 1);
}

$products = json_decode($output, true);

if (json_last_error() === JSON_ERROR_NONE) {
    echo "Données de produits :\n";
    foreach ($products as $product) {
        echo "Nom du produit : " . $product['name'] . PHP_EOL;
        echo "Prix du produit : " . $product['price'] . PHP_EOL;
        echo "Prix par kg : " . $product['pricePerKg'] . PHP_EOL;
        echo "------------------" . PHP_EOL;
    }
} else {
    echo "Erreur de décodage JSON : " . json_last_error_msg();
}
