<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', nullable: true)]
    private $message;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'comments')]
    private $Product;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    private $User;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getProduct(): ?Product
    {
        return $this->Product;
    }

    public function setProduct(?Product $Product): self
    {
        $this->Product = $Product;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }
}
