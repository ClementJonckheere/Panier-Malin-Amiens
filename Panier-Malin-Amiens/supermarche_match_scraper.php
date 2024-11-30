<?php

$baseUrl = 'https://www.supermarchesmatch.fr/fr/recherche?recherche=';
$productName = 'riz'; // Produit à rechercher

$searchUrl = $baseUrl . urlencode($productName);

// Commande pour exécuter le script Node.js
$command = "node Panier-Malin-Amiens/supermarche_match_script.js " . escapeshellarg($searchUrl) . " 2>&1";
$output = shell_exec($command);

// Vérification des données JSON récupérées
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

    // Création d'un fichier XML dans le dossier `data`
    $xmlFilePath = convertJsonToXml($products, "supermarche_match");
    echo "Données de produits enregistrées dans le fichier XML : $xmlFilePath\n";
} else {
    echo "Erreur de décodage JSON : " . json_last_error_msg() . "\n";
}

// Fonction pour convertir les données JSON en XML
function convertJsonToXml($jsonData, $filename) {
    // Vérification que le dossier `data` existe
    $directory = __DIR__ . '/data';
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $xml = new SimpleXMLElement('<products/>');

    foreach ($jsonData as $product) {
        $productNode = $xml->addChild('product');
        $productNode->addChild('name', htmlspecialchars($product['name']));
        $productNode->addChild('price', htmlspecialchars($product['price']));
        $productNode->addChild('pricePerKg', htmlspecialchars($product['pricePerKg']));
    }

    $timestamp = date('Y-m-d_H-i-s');
    $filePath = $directory . "/{$filename}_{$timestamp}.xml";

    $xml->asXML($filePath);

    return $filePath;
}
