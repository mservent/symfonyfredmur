<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Discussion::class, mappedBy="categorie")
     */
    private $discussion;

    public function __construct()
    {
        $this->discussion = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Discussion[]
     */
    public function getDiscussion(): Collection
    {
        return $this->discussion;
    }

    public function addDiscussion(Discussion $discussion): self
    {
        if (!$this->discussion->contains($discussion)) {
            $this->discussion[] = $discussion;
            $discussion->setCategorie($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): self
    {
        if ($this->discussion->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getCategorie() === $this) {
                $discussion->setCategorie(null);
            }
        }

        return $this;
    }
}
