<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class ApiProduitController extends AbstractController
{
    private $entityManager;
    private $produitRepository;

    public function __construct(EntityManagerInterface $entityManager, ProduitRepository $produitRepository)
    {
        $this->entityManager = $entityManager;
        $this->produitRepository = $produitRepository;
    }

    #[Route('/produits', name: 'api_produits_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $produits = $this->produitRepository->findAll();
        return $this->json($produits, Response::HTTP_OK, [], ['groups' => 'produit:read']);
    }

    #[Route('/produits/{id}', name: 'api_produits_detail', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $produit = $this->produitRepository->find($id);

        if (!$produit) {
            return $this->json(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($produit, Response::HTTP_OK, [], ['groups' => 'produit:read']);
    }

    #[Route('/produits', name: 'api_produits_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $produit = new Produit();
        $produit->setName($data['name']);
        $produit->setPrice($data['price']);
        $produit->setPricePerKg($data['pricePerKg']);
        $produit->setType($data['type']);
        $produit->setSource($data['source']);

        $this->entityManager->persist($produit);
        $this->entityManager->flush();

        return $this->json($produit, Response::HTTP_CREATED, [], ['groups' => 'produit:read']);
    }

    #[Route('/produits/{id}', name: 'api_produits_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $produit = $this->produitRepository->find($id);

        if (!$produit) {
            return $this->json(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $produit->setName($data['name'] ?? $produit->getName());
        $produit->setPrice($data['price'] ?? $produit->getPrice());
        $produit->setPricePerKg($data['pricePerKg'] ?? $produit->getPricePerKg());
        $produit->setType($data['type'] ?? $produit->getType());
        $produit->setSource($data['source'] ?? $produit->getSource());

        $this->entityManager->flush();

        return $this->json($produit, Response::HTTP_OK, [], ['groups' => 'produit:read']);
    }

    #[Route('/produits/{id}', name: 'api_produits_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $produit = $this->produitRepository->find($id);

        if (!$produit) {
            return $this->json(['message' => 'Produit non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($produit);
        $this->entityManager->flush();

        return $this->json(['message' => 'Produit supprimé'], Response::HTTP_NO_CONTENT);
    }


    #[Route('/comparaison/{type}', name: 'api_produits_comparaison', methods: ['GET'])]
    public function comparaison(string $type): JsonResponse
    {
        // Récupérer les produits du type donné
        $produits = $this->produitRepository->findBy(['type' => $type]);

        if (!$produits) {
            return $this->json(['message' => 'Aucun produit trouvé pour ce type'], Response::HTTP_NOT_FOUND);
        }

        // Trier les produits par prix (du moins cher au plus cher)
        usort($produits, function ($a, $b) {
            return $a->getPrice() <=> $b->getPrice();
        });

        // Créer un tableau structuré avec distinction des sources
        $comparaison = [];
        foreach ($produits as $produit) {
            $comparaison[] = [
                'name' => $produit->getName(),
                'price' => $produit->getPrice(),
                'pricePerKg' => $produit->getPricePerKg(),
                'source' => $produit->getSource(),
            ];
        }

        return $this->json($comparaison, Response::HTTP_OK);
    }
}
