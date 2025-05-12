<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    normalizationContext: ['groups' => ['media:read']],
    denormalizationContext: ['groups' => ['media:write']]
)]

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['media:read', 'event:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['media:read', 'media:write'])]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['media:read', 'media:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['media:read', 'media:write'])]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\File(
        mimeTypes: ["image/jpeg", "image/png", "image/webp"],
        mimeTypesMessage: "Seules les images JPG, PNG ou WebP sont autorisées."
    )]
    #[Groups(['media:read', 'media:write', 'event:read'])]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['media:read', 'media:write'])]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[Groups(['media:read', 'media:write'])]
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
