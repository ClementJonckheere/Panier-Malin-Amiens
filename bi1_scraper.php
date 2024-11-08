<?php

$baseUrl = "https://www.bi1drive.fr/00110/search/?text=";

$productName = "riz";

$url = $baseUrl . urlencode($productName);

$command = "node bi1_script.js " . escapeshellarg($url) . " 2>&1";

$output = shell_exec($command);

$products = json_decode($output, true);

if (json_last_error() === JSON_ERROR_NONE) {
    echo "Données de produits extraites :\n";
    foreach ($products as $product) {
        echo "Nom du produit : " . $product['name'] . PHP_EOL;
        echo "Prix du produit : " . $product['price'] . PHP_EOL;
        echo "Prix par kg : " . $product['pricePerKg'] . PHP_EOL;
        echo "------------------" . PHP_EOL;
    }
} else {
    echo "Erreur de décodage JSON : " . json_last_error_msg();
}
