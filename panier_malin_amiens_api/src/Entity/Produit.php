<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?float $price = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?float $pricePerKg = null;

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPricePerKg(): ?float
    {
        return $this->pricePerKg;
    }

    public function setPricePerKg(float $pricePerKg): self
    {
        $this->pricePerKg = $pricePerKg;

        return $this;
    }
}
