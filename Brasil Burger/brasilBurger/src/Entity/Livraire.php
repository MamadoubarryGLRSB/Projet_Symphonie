<?php

namespace App\Entity;

use App\Repository\LivraireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivraireRepository::class)]
class Livraire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomComplet = null;

    #[ORM\Column]
    private ?int $matMoto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getMatMoto(): ?int
    {
        return $this->matMoto;
    }

    public function setMatMoto(int $matMoto): self
    {
        $this->matMoto = $matMoto;

        return $this;
    }
}
