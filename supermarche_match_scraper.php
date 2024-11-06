<?php

$baseUrl = 'https://www.supermarchesmatch.fr/fr/recherche?recherche=';
$productName = 'riz'; // Vous pouvez changer "riz" par n'importe quel autre produit

// Construire l'URL complète en combinant l'URL de base et le produit
$searchUrl = $baseUrl . urlencode($productName);

// Commande pour appeler le script Node.js avec l'URL de recherche
$command = "node supermarche_match_script.js " . escapeshellarg($searchUrl) . " 2>&1";
$output = shell_exec($command);

// Nettoyage de la sortie pour récupérer uniquement le JSON
$jsonEnd = strrpos($output, ']');
if ($jsonEnd !== false) {
    $output = substr($output, 0, $jsonEnd + 1);
}

// Affichage brut de la sortie pour débogage (facultatif)
var_dump($output);

// Décoder la sortie JSON pour obtenir les produits
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
    // En cas d'erreur de décodage JSON, afficher le message d'erreur
    echo "Erreur de décodage JSON : " . json_last_error_msg();
}

?>
