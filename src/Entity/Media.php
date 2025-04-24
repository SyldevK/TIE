<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\File(
        mimeTypes: ["image/jpeg", "image/png", "image/webp"],
        mimeTypesMessage: "Seules les images JPG, PNG ou WebP sont autorisées."
    )]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    private ?Event $event = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function isImage(): bool
    {
        return str_starts_with($this->type, 'image/');
    }
}
