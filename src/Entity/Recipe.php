<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[UniqueEntity('title')]
#[UniqueEntity('slug')]
#[Vich\Uploadable]
class Recipe
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['recipes.index'])]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  #[Assert\Length(min: 2)]
  #[Assert\NotBlank]
  #[Groups(['recipes.index', 'write'])]
  private ?string $title = null;

  #[ORM\Column(length: 255)]
  #[Assert\Length(min: 5)]
  #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', "Invalid slug: exemple-recettes-1")]
  #[Groups(['recipes.index'])]
  private ?string $slug = null;

  #[ORM\Column(type: Types::TEXT)]
  #[Groups(['recipes.show', 'write'])]
  private ?string $content = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $updatedAt = null;

  #[ORM\Column(nullable: true)]
  #[Assert\NotBlank]
  #[Assert\Positive]
  #[Groups(['recipes.show'])]
  private ?int $duration = null;

  #[ORM\ManyToOne(inversedBy: 'recipes')]
  #[Groups(['recipes.index'])]
  private ?Category $category = null;

  // NOTE: This is not a mapped field of entity metadata, just a simple property.
  #[Vich\UploadableField(mapping: 'recipes', fileNameProperty: 'thumbnail')]
  #[Assert\Image()]
  private ?File $thumbnailFile = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $thumbnail = null;

  #[ORM\ManyToOne(inversedBy: 'recipes')]
  private ?User $user = null;

  /**
   * @var Collection<int, Quantity>
   */
  #[ORM\OneToMany(targetEntity: Quantity::class, mappedBy: 'recipe', orphanRemoval: true, cascade: ['persist'])]
  #[Assert\Valid]
  private Collection $quantities;

  public function __construct()
  {
    $this->quantities = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): static
  {
    $this->title = $title;

    return $this;
  }

  public function getSlug(): ?string
  {
    return $this->slug;
  }

  public function setSlug(string $slug): static
  {
    $this->slug = $slug;

    return $this;
  }

  public function getContent(): ?string
  {
    return $this->content;
  }

  public function setContent(string $content): static
  {
    $this->content = $content;

    return $this;
  }

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): static
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  public function getDuration(): ?int
  {
    return $this->duration;
  }

  public function setDuration(?int $duration): static
  {
    $this->duration = $duration;

    return $this;
  }

  public function getCategory(): ?Category
  {
    return $this->category;
  }

  public function setCategory(?Category $category): static
  {
    $this->category = $category;

    return $this;
  }

  public function getThumbnail(): ?string
  {
    return $this->thumbnail;
  }

  public function setThumbnail(?string $thumbnail): static
  {
    $this->thumbnail = $thumbnail;

    return $this;
  }
  public function getThumbnailFile(): ?File
  {
    return $this->thumbnailFile;
  }

  public function setThumbnailFile(?File $imageFile = null): static
  {
    $this->thumbnailFile = $imageFile;
    return $this;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): static
  {
    $this->user = $user;

    return $this;
  }

  /**
   * @return Collection<int, Quantity>
   */
  public function getQuantities(): Collection
  {
    return $this->quantities;
  }

  public function addQuantity(Quantity $quantity): static
  {
    if (!$this->quantities->contains($quantity)) {
      $this->quantities->add($quantity);
      $quantity->setRecipe($this);
    }

    return $this;
  }

  public function removeQuantity(Quantity $quantity): static
  {
    if ($this->quantities->removeElement($quantity)) {
      // set the owning side to null (unless already changed)
      if ($quantity->getRecipe() === $this) {
        $quantity->setRecipe(null);
      }
    }

    return $this;
  }
}
