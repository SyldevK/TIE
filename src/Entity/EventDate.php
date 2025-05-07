<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EventDateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventDateRepository::class)]
#[ApiResource]
class EventDate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'eventDates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?event $datetime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetime(): ?event
    {
        return $this->datetime;
    }

    public function setDatetime(?event $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }
}
