<?php

namespace App\Command;

use App\Entity\Produit; // Modifiez en fonction de votre entité
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:import-products',
    description: 'Importe les produits depuis le fichier scraper.',
)]
class ImportProductsCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Importation des produits...');

        $produit = new Produit();
        $produit->setName('Exemple Produit');
        $produit->setPrice(10.99);
        $produit->setPricePerKg(9.99);

        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        $output->writeln('Produits importés avec succès !');
        return Command::SUCCESS;
    }
}
