<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;


#[ApiResource(
    normalizationContext: ['groups' => ['event:read']],
    denormalizationContext: ['groups' => ['event:write']]
)]
#[ApiFilter(BooleanFilter::class, properties: ['isVisible'])]
#[ApiFilter(SearchFilter::class, properties: ['lieu' => 'partial'])]


#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['event:read', 'reservation:read', 'media:read'])]
    private ?int $id = null;

    #[Groups(['event:read', 'event:write', 'reservation:read'])]
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[Groups(['event:read'])]
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[Groups(['event:read', 'event:write'])]
    #[ORM\Column(type: 'boolean')]
    private ?bool $isVisible = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'event', orphanRemoval: true)]
    private Collection $reservations;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'event')]
    private Collection $media;

    /**
     * @var Collection<int, EventDate>
     */
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventDate::class, cascade: ['persist', 'remove'])]
    #[Groups(['event:read', 'event:write'])]
    private Collection $dates;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->dates = new ArrayCollection();
    }

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

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl !== null
            ? str_replace('\\', '/', $this->imageUrl)
            : null;
    }


    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isVisible(): ?bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): static
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setEvent($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getEvent() === $this) {
                $reservation->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->setEvent($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getEvent() === $this) {
                $medium->setEvent(null);
            }
        }

        return $this;
    }

    public function getDates(): Collection
    {
        return $this->dates;
    }

    public function addDate(EventDate $date): static
    {
        if (!$this->dates->contains($date)) {
            $this->dates->add($date);
            $date->setEvent($this);
        }
        return $this;
    }

    public function removeDate(EventDate $date): static
    {
        if ($this->dates->removeElement($date)) {
            if ($date->getEvent() === $this) {
                $date->setEvent(null);
            }
        }
        return $this;
    }
}
