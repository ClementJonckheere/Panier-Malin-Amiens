<?php

// URL de base pour le site Bi1
$baseUrl = "https://www.bi1drive.fr/00110/search/?text=";

// Nom du produit à rechercher (modifiable facilement)
$productName = "riz"; // Vous pouvez changer "riz" par n'importe quel autre produit

// Construire l'URL complète
$url = $baseUrl . urlencode($productName); // Utiliser urlencode pour gérer les espaces ou caractères spéciaux dans le nom du produit

// Construire la commande pour exécuter le script Node.js avec l'URL en paramètre
$command = "node bi1_script.js " . escapeshellarg($url) . " 2>&1";

// Exécuter le script Node.js et récupérer la sortie
$output = shell_exec($command);

// Afficher la sortie brute pour le débogage (facultatif)
// var_dump($output);

// Décoder la sortie JSON
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
    // Gérer l'erreur de décodage JSON
    echo "Erreur de décodage JSON : " . json_last_error_msg();
}

