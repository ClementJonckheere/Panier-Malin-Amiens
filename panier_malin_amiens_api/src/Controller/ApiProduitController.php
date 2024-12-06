<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiProduitController extends AbstractController
{
    #[Route('/api/produits', methods: ['GET'])]
    public function getAllProduits(EntityManagerInterface $em): JsonResponse
    {
        $produits = $em->getRepository(Produit::class)->findAll();

        $data = array_map(function (Produit $produit) {
            return [
                'id' => $produit->getId(),
                'name' => $produit->getName(),
                'price' => $produit->getPrice(),
                'pricePerKg' => $produit->getPricePerKg(),
                'type' => $produit->getType(),
            ];
        }, $produits);

        return new JsonResponse($data);
    }

    #[Route('/api/produits', methods: ['POST'])]
    public function addProduit(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $produit = new Produit();
        $produit->setName($data['name']);
        $produit->setPrice($data['price']);
        $produit->setPricePerKg($data['pricePerKg']);
        $produit->setType($data['type']);

        $em->persist($produit);
        $em->flush();

        return new JsonResponse(['message' => 'Produit ajouté avec succès'], 201);
    }
}
