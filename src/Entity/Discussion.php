<?php

namespace App\Entity;

use App\Entity\Utilisateur;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DiscussionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=DiscussionRepository::class)
 * @Vich\Uploadable
 */
class Discussion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


 

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "Le titre de la discussion doit avoir une longueur minimum de {{ limit }} caractères",
     *      maxMessage = "Le titre de la discussion doit avoir une longueur maximum de {{ limit }} caractères"
     * )
     */
    private $titre;

  
    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "le contenu de la discussion doit avoir une longueur minimum de {{ limit }} caractères"
     * )
     */
    private $contenu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;
    

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="discussion_image", fileNameProperty="image")
     */

    private $imageFile;


  
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $upDateAt;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="discussion", orphanRemoval=true)
     */
    private $commentaires;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="relation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $utilisateur;

    /**
     * @ORM\OneToMany(targetEntity=Discussionlike::class, mappedBy="discussion", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isValid;

    /**
     * @ORM\OneToMany(targetEntity=DiscussionDislike::class, mappedBy="discussion", orphanRemoval=true)
     */
    private $dislikes;

    /**
     * @ORM\OneToMany(targetEntity=Signalement::class, mappedBy="discussion")
     */
    private $signalements;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="discussion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
        $this->signalements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

   
    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }


    /**
     * @return null|File
     */

  public function getImageFile(): ?File
  {
      return $this->imageFile;
  }

  /**
   * @param null|File $imageFile
   * 
   */

  public function setImageFile(?File $imageFile = null)
  {
      $this->imageFile = $imageFile;

      if (null !== $imageFile) {
        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        $this->updateAt = new \DateTime();
    }
  }


 
   

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpDateAt(): ?\DateTimeInterface
    {
        return $this->upDateAt;
    }

    public function setUpDateAt(\DateTimeInterface $upDateAt): self
    {
        $this->upDateAt = $upDateAt;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setDiscussion($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getDiscussion() === $this) {
                $commentaire->setDiscussion(null);
            }
        }

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection|Discussionlike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Discussionlike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setDiscussion($this);
        }

        return $this;
    }

    public function removeLike(Discussionlike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getDiscussion() === $this) {
                $like->setDiscussion(null);
            }
        }

        return $this;
    }

    /**
     * Permet de savoir si cet article est "liké" par un utilisateur
     *
     * 
     *  @return boolean
     */
    public function isLikedByUser(Utilisateur $user) : bool {
        foreach ($this->likes as $like) {
            if($like->getUtilisateur() === $user) return true;
            # code...
        }
        return false; // on a passé tous les like sans avoir trouvé de user alors on retourne false
    }


      /**
     * Permet de savoir si cet article est "disliké" par un utilisateur
     *
     * 
     *  @return boolean
     */
    public function isDisLikedByUser(Utilisateur $user) : bool {
        foreach ($this->dislikes as $dislike) {
            if($dislike->getUtilisateur() === $user) return true;
            # code...
        }
        return false; // on a passé tous les dislike sans avoir trouvé de user alors on retourne false
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * @return Collection|DiscussionDislike[]
     */
    public function getDislikes(): Collection
    {
        return $this->dislikes;
    }

    public function addDislike(DiscussionDislike $dislike): self
    {
        if (!$this->dislikes->contains($dislike)) {
            $this->dislikes[] = $dislike;
            $dislike->setDiscussion($this);
        }

        return $this;
    }

    public function removeDislike(DiscussionDislike $dislike): self
    {
        if ($this->dislikes->removeElement($dislike)) {
            // set the owning side to null (unless already changed)
            if ($dislike->getDiscussion() === $this) {
                $dislike->setDiscussion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Signalement[]
     */
    public function getSignalements(): Collection
    {
        return $this->signalements;
    }

    public function addSignalement(Signalement $signalement): self
    {
        if (!$this->signalements->contains($signalement)) {
            $this->signalements[] = $signalement;
            $signalement->setDiscussion($this);
        }

        return $this;
    }

    public function removeSignalement(Signalement $signalement): self
    {
        if ($this->signalements->removeElement($signalement)) {
            // set the owning side to null (unless already changed)
            if ($signalement->getDiscussion() === $this) {
                $signalement->setDiscussion(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

 
 

 
}
