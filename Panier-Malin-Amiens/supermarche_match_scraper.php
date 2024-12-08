<?php

$baseUrl = 'https://www.supermarchesmatch.fr/fr/recherche?recherche=';
$productTypes = ['riz', 'lait', 'oeufs', 'farine'];

$allProducts = [];

foreach ($productTypes as $productType) {
    $searchUrl = $baseUrl . urlencode($productType);

    $command = "node /var/www/html/Panier-Malin-Amiens/supermarche_match_script.js " . escapeshellarg($searchUrl) . " 2>&1";
    $output = shell_exec($command);

    echo "Sortie brute de Node.js pour $productType :\n";
    echo $output . "\n";

    $jsonStart = strpos($output, '[');
    if ($jsonStart !== false) {
        $output = substr($output, $jsonStart);
    } else {
        echo "Erreur : Aucun JSON détecté dans la sortie pour $productType.\n";
        continue;
    }

    $products = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Erreur de décodage JSON pour $productType : " . json_last_error_msg() . "\n";
        echo "Sortie brute nettoyée :\n" . $output . "\n";
        continue;
    }

    foreach ($products as &$product) {
        $product['type'] = $productType;
    }

    $allProducts = array_merge($allProducts, $products);
}

echo "Données de tous les produits avant la génération du fichier XML :\n";
foreach ($allProducts as $product) {
    if (!isset($product['type'])) {
        echo "Erreur : Le produit suivant n'a pas de type :\n";
        print_r($product);
    }
}
print_r($allProducts);

$xmlFilePath = createXmlWithDom($allProducts, "supermarche_match");
if (!$xmlFilePath) {
    echo "Le fichier XML n'a pas été généré.\n";
} else {
    echo "Le fichier XML a été enregistré à l'emplacement : $xmlFilePath\n";
}

function createXmlWithDom($jsonData, $filename) {
    // Vérification que les données ne sont pas vides
    if (empty($jsonData)) {
        echo "Aucune donnée à écrire dans le fichier XML.\n";
        return false;
    }

    $directory = __DIR__ . '/data';
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;

    $root = $dom->createElement('products');
    $dom->appendChild($root);

    foreach ($jsonData as $product) {
        $productNode = $dom->createElement('product');

        foreach ($product as $key => $value) {
            $childNode = $dom->createElement($key, htmlspecialchars($value));
            $productNode->appendChild($childNode);
        }

        $root->appendChild($productNode);
    }

    $timestamp = date('Y-m-d_H-i-s');
    $filePath = $directory . "/{$filename}_{$timestamp}.xml";

    if ($dom->save($filePath)) {
        return $filePath;
    } else {
        echo "Erreur lors de l'écriture du fichier XML : $filePath\n";
        return false;
    }
}
