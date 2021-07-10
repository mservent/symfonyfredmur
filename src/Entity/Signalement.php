<?php

namespace App\Entity;

use App\Repository\SignalementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SignalementRepository::class)
 */
class Signalement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

   

    /**
     * @ORM\ManyToOne(targetEntity=Discussion::class, inversedBy="signalements")
     */
    private $discussion;

    /**
     * @ORM\ManyToOne(targetEntity=Commentaire::class, inversedBy="signalements")
     */
    private $commentaire;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="signalements")
     */
    private $utilisateur;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $traitement = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $upDatedAt;

    /**
     * @ORM\Column(type="text")
     */
    private $motif;

    public function getId(): ?int
    {
        return $this->id;
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

  

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(?Discussion $discussion): self
    {
        $this->discussion = $discussion;

        return $this;
    }

    public function getCommentaire(): ?Commentaire
    {
        return $this->commentaire;
    }

    public function setCommentaire(?Commentaire $commentaire): self
    {
        $this->commentaire = $commentaire;

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

    public function getTraitement(): ?bool
    {
        return $this->traitement;
    }

    public function setTraitement(bool $traitement): self
    {
        $this->traitement = $traitement;

        return $this;
    }

    public function getUpDatedAt(): ?\DateTimeInterface
    {
        return $this->upDatedAt;
    }

    public function setUpDatedAt(?\DateTimeInterface $upDatedAt): self
    {
        $this->upDatedAt = $upDatedAt;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }
}
