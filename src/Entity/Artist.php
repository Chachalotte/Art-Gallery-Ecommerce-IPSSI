<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArtistRepository::class)]
class Artist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'string', length: 50)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 20)]
    private $gender;

    #[ORM\OneToMany(mappedBy: 'artist', targetEntity: Product::class)]
    private $Product;

    #[ORM\OneToOne(targetEntity: Page::class, cascade: ['persist', 'remove'])]
    private $Page;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    private $avatar;

    public function __construct()
    {
        $this->Product = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProduct(): Collection
    {
        return $this->Product;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->Product->contains($product)) {
            $this->Product[] = $product;
            $product->setArtist($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->Product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getArtist() === $this) {
                $product->setArtist(null);
            }
        }

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->Page;
    }

    public function setPage(?Page $Page): self
    {
        $this->Page = $Page;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
