<?php

$baseUrl = 'https://www.supermarchesmatch.fr/fr/recherche?recherche=';
$productName = 'riz'; // Produit à rechercher

$searchUrl = $baseUrl . urlencode($productName);

// Commande pour exécuter le script Node.js
$command = "node /var/www/html/Panier-Malin-Amiens/supermarche_match_script.js " . escapeshellarg($searchUrl) . " 2>&1";
$output = shell_exec($command);

// Affichez la sortie brute pour diagnostiquer les problèmes
echo "Sortie brute de Node.js :\n";
echo $output . "\n";

// Nettoyer la sortie pour enlever les caractères indésirables
$output = trim($output);

// Vérifiez si la sortie est bien formatée en JSON
$products = json_decode($output, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Erreur de décodage JSON : " . json_last_error_msg() . "\n";
    echo "Sortie brute :\n" . $output . "\n";
    exit;
}

// Affichage des données récupérées
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

    if ($xml->asXML($filePath)) {
        return $filePath;
    } else {
        die("Erreur lors de l'écriture du fichier XML : $filePath\n");
    }
}
