<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $name = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?float $price = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?float $pricePerKg = null;

    #[ORM\Column(length: 3, options: ['default' => 'EUR'])]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $currency = 'EUR';

    #[ORM\Column(length: 255)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $type = null;

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

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    #[ORM\Column(length: 255)]
    #[Groups(['produit:read', 'produit:write'])]
    private ?string $source = null;

// Getter et setter pour `source`
    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }

}
