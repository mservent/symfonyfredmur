<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity(
 *   fields={"email"},
  *  message="Cet email est déjà utilisé."
 * )
 */
class Utilisateur  implements UserInterface                  {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomUtilisateur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit contenir au minimum
     *  8 caractères ")
     */
    private $password;

     /**
      * @Assert\EqualTo(propertyPath="password",  message="Vos mots de passe doivent être identiques !")
     */

    public $confirm_password;

    public $userName;
    public $userIdentifier;

    /**
     * @ORM\Column(type="json_array")
    */
   private $roles = array();


    

    /**
     * @ORM\OneToMany(targetEntity=Discussion::class, mappedBy="utilisateur",  orphanRemoval=true)
     */
    private $discussion;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="utilisateur", orphanRemoval=true)
     */
    private $commentaire;

    /**
     * @ORM\OneToMany(targetEntity=Discussionlike::class, mappedBy="utilisateur")
     */
    private $likes;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $isValid = false;

    /**
     * @ORM\OneToMany(targetEntity=Signalement::class, mappedBy="utilisateur")
     */
    private $signalements;

    public function __construct()
    {
        $this->relation = new ArrayCollection();
        $this->commentaire = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->signalements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getNomUtilisateur(): ?string
    {
        return $this->nomUtilisateur;
    }

    public function setNomUtilisateur(string $nomUtilisateur): self
    {
        $this->nomUtilisateur = $nomUtilisateur;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
            $discussion->setUtilisateur($this);
        }

        return $this;
    }

    public function removeRelation(Discussion $discussion): self
    {
        if ($this->discussion->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getUtilisateur() === $this) {
                $discussion->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaire(): Collection
    {
        return $this->commentaire;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaire->contains($commentaire)) {
            $this->commentaire[] = $commentaire;
            $commentaire->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaire->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUtilisateur() === $this) {
                $commentaire->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getRoles() { 
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles) : self
    {
        $this->roles = $roles;

        return $this;
    }

    
    public function eraseCredentials() {}
    public function getSalt() {}
    public function getUsername(){
        return $this->email;
    }
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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
            $like->setUtilisateur($this);
        }

        return $this;
    }

    public function removeLike(Discussionlike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUtilisateur() === $this) {
                $like->setUtilisateur(null);
            }
        }

        return $this;
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
            $signalement->setUtilisateur($this);
        }

        return $this;
    }

    public function removeSignalement(Signalement $signalement): self
    {
        if ($this->signalements->removeElement($signalement)) {
            // set the owning side to null (unless already changed)
            if ($signalement->getUtilisateur() === $this) {
                $signalement->setUtilisateur(null);
            }
        }

        return $this;
    }
}
