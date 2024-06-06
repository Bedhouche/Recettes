<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $couleur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombre_recettes = null;

    /**
     * @var Collection<int, Recette>|null
     *
     * @ORM\ManyToMany(targetEntity=Recette::class, mappedBy="categories")
     */
    private ?Collection $recettes = null;

    public function __construct()
    {
        $this->recettes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getNombreRecettes(): ?int
    {
        return $this->nombre_recettes;
    }

    public function setNombreRecettes(int $nombre_recettes): static
    {
        $this->nombre_recettes = $nombre_recettes;

        return $this;
    }

    /**
     * @return Collection<int, Recette>
     */

    public function getRecettes(): Collection
    {
        return $this->recettes?: new ArrayCollection();
    }


    public function addRecette(Recette $recette): static
    {
        if ($this->recettes === null) {
            $this->recettes = new ArrayCollection();
        }

        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->addCategory($this);
            $this->nombre_recettes = $this->recettes->count();
        }

        return $this;
    }


    public function removeRecette(Recette $recette): static
    {
        if ($this->recettes->removeElement($recette)) {
            $recette->removeCategory($this);
            $this->nombre_recettes = $this->recettes->count();


        }

        return $this;
    }




}
