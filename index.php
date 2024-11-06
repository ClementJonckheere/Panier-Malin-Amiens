<?php

$baseUrl = 'https://www.supermarchesmatch.fr/fr/recherche?recherche=riz';
$maxPages = 3;

// Commande pour appeler le script Node.js avec le nombre de pages
$command = "node scraper.js " . escapeshellarg($baseUrl) . " " . escapeshellarg($maxPages) . " 2>&1";
$output = shell_exec($command);

// Obtenir uniquement le JSON
$jsonEnd = strrpos($output, ']');
if ($jsonEnd !== false) {
    $output = substr($output, 0, $jsonEnd + 1);
}

var_dump($output);

$products = json_decode($output, true);

if (json_last_error() === JSON_ERROR_NONE) {
    echo "Données de produits :\n";
    foreach ($products as $product) {
        echo "Nom du produit : " . $product['name'] . PHP_EOL;
        echo "Prix du produit : " . $product['price'] . PHP_EOL;
        echo "------------------" . PHP_EOL;
    }
} else {
    echo "Erreur de décodage JSON : " . json_last_error_msg();
}
