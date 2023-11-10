<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    private ?Posts $comments = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComments(): ?Posts
    {
        return $this->comments;
    }

    public function setComments(?Posts $comments): static
    {
        $this->comments = $comments;

        return $this;
    }
}
