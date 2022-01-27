<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToMany(mappedBy: 'orderItem', targetEntity: Product::class)]
    private $prodId;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'orderItems')]
    private $Ordered;

    #[ORM\OneToMany(mappedBy: 'Ordered', targetEntity: self::class)]
    private $orderItems;

    #[ORM\OneToMany(mappedBy: 'OrderItem', targetEntity: Ordered::class)]
    private $ordereds;

    public function __construct()
    {
        $this->prodId = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->ordereds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProdId(): Collection
    {
        return $this->prodId;
    }

    public function addProdId(Product $prodId): self
    {
        if (!$this->prodId->contains($prodId)) {
            $this->prodId[] = $prodId;
            $prodId->setOrderItem($this);
        }

        return $this;
    }

    public function removeProdId(Product $prodId): self
    {
        if ($this->prodId->removeElement($prodId)) {
            // set the owning side to null (unless already changed)
            if ($prodId->getOrderItem() === $this) {
                $prodId->setOrderItem(null);
            }
        }

        return $this;
    }

    public function getOrdered(): ?self
    {
        return $this->Ordered;
    }

    public function setOrdered(?self $Ordered): self
    {
        $this->Ordered = $Ordered;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(self $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setOrdered($this);
        }

        return $this;
    }

    public function removeOrderItem(self $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrdered() === $this) {
                $orderItem->setOrdered(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ordered[]
     */
    public function getOrdereds(): Collection
    {
        return $this->ordereds;
    }

    public function addOrdered(Ordered $ordered): self
    {
        if (!$this->ordereds->contains($ordered)) {
            $this->ordereds[] = $ordered;
            $ordered->setOrderItem($this);
        }

        return $this;
    }

    public function removeOrdered(Ordered $ordered): self
    {
        if ($this->ordereds->removeElement($ordered)) {
            // set the owning side to null (unless already changed)
            if ($ordered->getOrderItem() === $this) {
                $ordered->setOrderItem(null);
            }
        }

        return $this;
    }
}
