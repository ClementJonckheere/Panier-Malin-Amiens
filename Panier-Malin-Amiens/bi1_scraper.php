<?php

$baseUrl = "https://www.bi1drive.fr/00110/search/?text=";
$productName = "riz";

$url = $baseUrl . urlencode($productName);

// Exécution du script Node.js
$command = "node Panier-Malin-Amiens/bi1_script.js " . escapeshellarg($url) . " 2>&1";
$output = shell_exec($command);

echo "Données brutes récupérées :\n";
echo $output . "\n";

// Décodage JSON
$products = json_decode($output, true);

// Vérification des erreurs JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Erreur de décodage JSON : " . json_last_error_msg() . "\n");
}

// Chemin absolu du dossier data
$absolutePath = '/var/www/html/Panier-Malin-Amiens/data/';

// Vérification du répertoire 'data'
if (!is_dir($absolutePath)) {
    echo "Le dossier 'data' n'existe pas au chemin absolu : $absolutePath. Création...\n";
    if (!mkdir($absolutePath, 0777, true)) {
        die("Impossible de créer le dossier 'data'. Vérifiez les permissions.\n");
    }
}

// Test d'écriture dans le répertoire
if (!file_put_contents($absolutePath . 'test.txt', 'Test d\'écriture')) {
    die("Impossible d'écrire dans le répertoire 'data'. Vérifiez les permissions.\n");
}

echo "Répertoire courant : " . getcwd() . PHP_EOL;

// Conversion des données JSON en XML
$xmlFilePath = convertJsonToXml($products, "bi1", $absolutePath);
echo "Données de produits enregistrées dans le fichier XML : $xmlFilePath\n";

// Affichage des produits
echo "Données de produits extraites :\n";
foreach ($products as $product) {
    echo "Nom du produit : " . $product['name'] . PHP_EOL;
    echo "Prix du produit : " . $product['price'] . PHP_EOL;
    echo "Prix par kg : " . $product['pricePerKg'] . PHP_EOL;
    echo "------------------" . PHP_EOL;
}

/**
 * Fonction pour convertir un tableau JSON en fichier XML.
 */
function convertJsonToXml($jsonData, $filename, $absolutePath) {
    $xml = new SimpleXMLElement('<products/>');

    foreach ($jsonData as $product) {
        $productNode = $xml->addChild('product');
        $productNode->addChild('name', htmlspecialchars($product['name']));
        $productNode->addChild('price', htmlspecialchars($product['price']));
        $productNode->addChild('pricePerKg', htmlspecialchars($product['pricePerKg']));
    }

    $timestamp = date('Y-m-d_H-i-s');
    $filePath = "{$absolutePath}{$filename}_{$timestamp}.xml";

    if (!$xml->asXML($filePath)) {
        die("Erreur lors de l'écriture du fichier XML : $filePath\n");
    }

    return $filePath;
}
