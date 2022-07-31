<?php

namespace App\Entity;

use App\Repository\SubscriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionsRepository::class)]
class Subscriptions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    //Personne qui s'abonne
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'follower')]
    private $User;

    //Personne auquel il est abonné
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'followed')]
    private $UserFollowed;

    public function getId(): ?int
    {
        return $this->id;
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


    public function getUserFollowed(): ?User
    {
        return $this->UserFollowed;
    }

    public function setUserFollowed(?User $UserFollowed): self
    {
        $this->UserFollowed = $UserFollowed;

        return $this;
    }
}
