<?php

$baseUrl = 'https://www.supermarchesmatch.fr/fr/recherche?recherche=';
$productTypes = ['riz', 'lait', 'oeufs', 'farine']; // Liste des types de produits

$allProducts = []; // Stocker tous les produits de tous les types

foreach ($productTypes as $productType) {
    $searchUrl = $baseUrl . urlencode($productType);

    // Commande pour exécuter le scripts Node.js
    $command = "node /var/www/html/Panier-Malin-Amiens/supermarche_match_script.js " . escapeshellarg($searchUrl) . " 2>&1";
    $output = shell_exec($command);

    // Affichez la sortie brute pour diagnostiquer les problèmes
    echo "Sortie brute de Node.js pour $productType :\n";
    echo $output . "\n";

    // Nettoyer la sortie brute pour ne garder que le JSON
    $jsonStart = strpos($output, '['); // Trouver le début de l'objet JSON
    if ($jsonStart !== false) {
        $output = substr($output, $jsonStart); // Extraire uniquement la partie JSON
    } else {
        echo "Erreur : Aucun JSON détecté dans la sortie pour $productType.\n";
        continue;
    }

    // Vérifiez si la sortie est bien formatée en JSON
    $products = json_decode($output, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Erreur de décodage JSON pour $productType : " . json_last_error_msg() . "\n";
        echo "Sortie brute nettoyée :\n" . $output . "\n";
        continue;
    }

    // Ajouter le type au produit
    foreach ($products as &$product) {
        $product['type'] = $productType; // Ajoute le type directement dans le produit
    }

    $allProducts = array_merge($allProducts, $products);
}

// Vérifiez les données finales
echo "Données de tous les produits avant la génération du fichier XML :\n";
foreach ($allProducts as $product) {
    if (!isset($product['type'])) {
        echo "Erreur : Le produit suivant n'a pas de type :\n";
        print_r($product);
    }
}
print_r($allProducts);

// Création d'un fichier XML dans le dossier `data`
$xmlFilePath = createXmlWithDom($allProducts, "supermarche_match");
if (!$xmlFilePath) {
    echo "Le fichier XML n'a pas été généré.\n";
} else {
    echo "Le fichier XML a été enregistré à l'emplacement : $xmlFilePath\n";
}

// Fonction pour convertir les données JSON en XML avec DOMDocument
function createXmlWithDom($jsonData, $filename) {
    // Vérification que les données ne sont pas vides
    if (empty($jsonData)) {
        echo "Aucune donnée à écrire dans le fichier XML.\n";
        return false;
    }

    // Vérification que le dossier `data` existe
    $directory = __DIR__ . '/data';
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    // Initialisation de DOMDocument
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;

    // Élément racine
    $root = $dom->createElement('products');
    $dom->appendChild($root);

    foreach ($jsonData as $product) {
        $productNode = $dom->createElement('product');

        // Ajouter les sous-éléments
        foreach ($product as $key => $value) {
            $childNode = $dom->createElement($key, htmlspecialchars($value));
            $productNode->appendChild($childNode);
        }

        $root->appendChild($productNode);
    }

    $timestamp = date('Y-m-d_H-i-s');
    $filePath = $directory . "/{$filename}_{$timestamp}.xml";

    // Écriture dans le fichier
    if ($dom->save($filePath)) {
        return $filePath;
    } else {
        echo "Erreur lors de l'écriture du fichier XML : $filePath\n";
        return false;
    }
}
