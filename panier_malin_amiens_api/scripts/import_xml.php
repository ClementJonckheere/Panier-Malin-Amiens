<?php

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;

// Charger l'autoloader de Symfony
require dirname(__DIR__) . '/vendor/autoload.php';

// Initialiser le kernel Symfony pour accéder aux services
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\KernelInterface;

$dotenv = new Dotenv();
$dotenv->loadEnv(dirname(__DIR__) . '/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);
$kernel->boot();

/** @var EntityManagerInterface $entityManager */
$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

// Liste des fichiers XML à importer avec leurs sources respectives
$dataSources = [
[
'source' => 'bi1',
'filePath' => '/var/www/html/data/data/bi1_2024-12-05_19-00-44.xml',
],
[
'source' => 'match',
'filePath' => '/var/www/html/data/data/supermarche_match_2024-12-05_18-54-24.xml',
],
];

// Importer les produits pour chaque fichier XML
foreach ($dataSources as $dataSource) {
$source = $dataSource['source'];
$xmlFilePath = $dataSource['filePath'];

if (!file_exists($xmlFilePath)) {
echo "Fichier XML non trouvé : $xmlFilePath\n";
continue;
}

// Charger le fichier XML
$xml = simplexml_load_file($xmlFilePath);
if (!$xml) {
echo "Impossible de lire le fichier XML : $xmlFilePath\n";
continue;
}

// Importer les produits dans la base de données
foreach ($xml->product as $product) {
$produit = new Produit();
$produit->setName((string)$product->name);
$produit->setPrice((float)$product->price);
$produit->setPricePerKg((float)$product->pricePerKg);
$produit->setType((string)$product->type);
$produit->setSource($source);

$entityManager->persist($produit);
}

echo "Données importées avec succès depuis le fichier XML : $xmlFilePath (Source : $source)\n";
}

// Finaliser l'insertion dans la base de données
$entityManager->flush();
echo "Toutes les données ont été importées avec succès.\n";
