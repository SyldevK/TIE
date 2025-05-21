<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    normalizationContext: ['groups' => ['reservation:read']],
    denormalizationContext: ['groups' => ['reservation:write']]
)]

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation:read', 'reservation:write'])]
    private ?int $id = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\Column]
    private ?int $nombrePlaces = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateReservation = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[Groups(['reservation:read', 'reservation:write'])]
    #[ORM\ManyToOne(targetEntity: EventDate::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?EventDate $eventDate = null;

    public function getEventDate(): ?EventDate
    {
        return $this->eventDate;
    }

    public function setEventDate(?EventDate $eventDate): static
    {
        $this->eventDate = $eventDate;
        return $this;
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombrePlaces(): ?int
    {
        return $this->nombrePlaces;
    }

    public function setNombrePlaces(int $nombrePlaces): static
    {
        $this->nombrePlaces = $nombrePlaces;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTimeInterface $dateReservation): static
    {
        $this->dateReservation = $dateReservation;

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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getNomCompletUser(): ?string
    {
        return $this->user
            ? $this->user->getPrenom() . ' ' . $this->user->getNom()
            : null;
    }

    public function getTitreEvent(): ?string
    {
        return $this->event
            ? $this->event->getTitre()
            : null;
    }
}
