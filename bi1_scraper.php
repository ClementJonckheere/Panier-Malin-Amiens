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
    $xmlFilePath = convertJsonToXml($products, "bi1"); 
    echo "Données de produits enregistrées dans le fichier XML : $xmlFilePath\n";
} else {
    echo "Erreur de décodage JSON : " . json_last_error_msg();
}

function convertJsonToXml($jsonData, $filename) {
    $xml = new SimpleXMLElement('<products/>');

    foreach ($jsonData as $product) {
        $productNode = $xml->addChild('product');
        $productNode->addChild('name', htmlspecialchars($product['name']));
        $productNode->addChild('price', htmlspecialchars($product['price']));
        $productNode->addChild('pricePerKg', htmlspecialchars($product['pricePerKg']));
    }

    $timestamp = date('Y-m-d_H-i-s');
    $filePath = "data/{$filename}_{$timestamp}.xml";
    
    $xml->asXML($filePath);

    return $filePath;
}
