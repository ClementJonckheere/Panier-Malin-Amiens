<?php


namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/api/produits', name: 'api_produits', methods: ['GET'])]
    public function getProduits(EntityManagerInterface $entityManager): JsonResponse
    {
        $produits = $entityManager->getRepository(Produit::class)->findAll();

        $data = [];
        foreach ($produits as $produit) {
            $data[] = [
                'id' => $produit->getId(),
                'name' => $produit->getName(),
                'price' => $produit->getPrice(),
                'pricePerKg' => $produit->getPricePerKg(),
                'currency' => $produit->getCurrency(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/produits', name: 'api_create_produit', methods: ['POST'])]
    public function createProduit(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $produit = new Produit();
        $produit->setName($data['name']);
        $produit->setPrice(floatval($data['price']));
        $produit->setPricePerKg(floatval($data['pricePerKg']));
        $produit->setCurrency($data['currency'] ?? 'EUR');

        $entityManager->persist($produit);
        $entityManager->flush();

        return $this->json(['status' => 'Produit créé avec succès'], 201);
    }
}
