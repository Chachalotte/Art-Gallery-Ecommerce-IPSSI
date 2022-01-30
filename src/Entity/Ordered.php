<?php

namespace App\Entity;

use App\Repository\OrderedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderedRepository::class)]
class Ordered
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: OrderItem::class, inversedBy: 'ordereds')]
    private $OrderItem;

    #[ORM\Column(type: 'string', length: 25)]
    private $status;

    #[ORM\Column(type: 'date')]
    private $creationDate;

    #[ORM\OneToMany(mappedBy: 'ordered', targetEntity: User::class)]
    private $User;

    public function __construct()
    {
        $this->User = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderItem(): ?OrderItem
    {
        return $this->OrderItem;
    }

    public function setOrderItem(?OrderItem $OrderItem): self
    {
        $this->OrderItem = $OrderItem;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUser(): Collection
    {
        return $this->User;
    }

    public function addUser(User $user): self
    {
        if (!$this->User->contains($user)) {
            $this->User[] = $user;
            $user->setOrdered($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->User->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getOrdered() === $this) {
                $user->setOrdered(null);
            }
        }

        return $this;
    }
}
