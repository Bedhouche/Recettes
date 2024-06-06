<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: RecetteRepository::class)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $ingredients = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contenu = null;

    #[ORM\Column(nullable: true)]
    private ?float $Moyenne_note = null;

    #[ORM\Column(nullable: true)]
    private ?int $nmbr_note = null;

    #[ORM\Column]
    private ?int $temp_prep = null;

    #[ORM\Column]
    private ?int $temp_cuisson = null;


    #[ORM\Column(type: 'integer')]
    private int $nombreLikes = 0;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'recette', orphanRemoval: true)]
    private Collection $commentaires;

    /**
     * @var Collection<int, Favori>
     */
    #[ORM\OneToMany(targetEntity: Favori::class, mappedBy: 'recette')]
    private Collection $favoris;

    #[ORM\Column]
    private ?int $difficulte = null;

    /**
     * @var Collection<int, Etape>
     */
    #[ORM\OneToMany(targetEntity: Etape::class, mappedBy: 'recette')]
    private Collection $etapes;

    /**
     * @var Collection<int, Categorie>
     */
    #[ORM\ManyToMany(targetEntity: Categorie::class, inversedBy: 'recettes')]
    private Collection $categories;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->etapes = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }


    // Dans votre entité Recette

    public function isFavori(User $user): bool
    {
        foreach ($this->favoris as $favori) {
            if ($favori->getUsers()->contains($user)) {
                return true;
            }
        }
        return false;
    }


    public function getNombreLikes(): int
    {
        return $this->favoris->count();
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function setIngredients(string $ingredients): static
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }
    public function setContenu(?string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getMoyenneNote(): ?float
    {
        return $this->Moyenne_note;
    }

    public function setMoyenneNote(?float $Moyenne_note): static
    {
        $this->Moyenne_note = $Moyenne_note;

        return $this;
    }

    public function getNmbrNote(): ?int
    {
        return $this->nmbr_note;
    }

    public function setNmbrNote(?int $nmbr_note): static
    {
        $this->nmbr_note = $nmbr_note;

        return $this;
    }

    public function getTempPrep(): ?int
    {
        return $this->temp_prep;
    }

    public function setTempPrep(int $temp_prep): static
    {
        $this->temp_prep = $temp_prep;
        return $this;
    }

    public function getTempCuisson(): ?int
    {
        return $this->temp_cuisson;
    }

    public function setTempCuisson(int $temp_cuisson): static
    {
        $this->temp_cuisson = $temp_cuisson;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setRecette($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getRecette() === $this) {
                $commentaire->setRecette(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favori>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }


    public function setNombreLikes(int $nombreLikes): self
    {
        $this->nombreLikes = $nombreLikes;

        return $this;
    }

    public function addFavori(Favori $favori): static
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
            $favori->setRecette($this); // Assurez-vous que la recette est correctement définie sur le favori
        }

        return $this;
    }


    public function removeFavori(Favori $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            // set the owning side to null (unless already changed)
            if ($favori->getRecette() === $this) {
                $favori->setRecette(null);
            }
        }

        return $this;
    }

    public function getDifficulte(): ?int
    {
        return $this->difficulte;
    }

    public function setDifficulte(int $difficulte): static
    {
        $this->difficulte = $difficulte;

        return $this;
    }

    /**
     * @return Collection<int, Etape>
     */
    public function getEtapes(): Collection
    {
        return $this->etapes;
    }

    public function addEtape(Etape $etape): static
    {
        if (!$this->etapes->contains($etape)) {
            $this->etapes->add($etape);
            $etape->setRecette($this);
        }

        return $this;
    }

    public function removeEtape(Etape $etape): static
    {
        if ($this->etapes->removeElement($etape)) {
            // set the owning side to null (unless already changed)
            if ($etape->getRecette() === $this) {
                $etape->setRecette(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Categorie>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }
    public function isLiked(User $user): bool
    {
        foreach ($this->favoris as $favori) {
            if ($favori->getUsers()->contains($user)) {
                return true;
            }
        }
        return false;
    }


    public function isLikedByUser(User $user): bool
    {
        foreach ($this->favoris as $favori) {
            if ($favori->getUsers()->contains($user)) {
                return true;
            }
        }
        return false;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }


}
