<?php

namespace App\Entity;

use App\Repository\EnrollmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    normalizationContext: ['groups' => ['enrollment:read']],
    denormalizationContext: ['groups' => ['enrollment:write']]
)]

#[ORM\Entity(repositoryClass: EnrollmentRepository::class)]
class Enrollment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['enrollment:read', 'participant:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['enrollment:read', 'enrollment:write'])]
    private ?string $groupe = null;

    #[ORM\Column]
    #[Groups(['enrollment:read', 'enrollment:write'])]
    private ?bool $isActive = null;

    #[ORM\Column(length: 255)]
    #[Groups(['enrollment:read', 'enrollment:write'])]
    private ?string $anneeScolaire = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['enrollment:read', 'enrollment:write'])]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'enrollment', cascade: ['persist', 'remove'])]
    #[Groups(['enrollment:read'])]
    private ?Participant $participant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupe(): ?string
    {
        return $this->groupe;
    }

    public function setGroupe(string $groupe): static
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getAnneeScolaire(): ?string
    {
        return $this->anneeScolaire;
    }

    public function setAnneeScolaire(string $anneeScolaire): static
    {
        $this->anneeScolaire = $anneeScolaire;

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

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(Participant $participant): static
    {
        // set the owning side of the relation if necessary
        if ($participant->getEnrollment() !== $this) {
            $participant->setEnrollment($this);
        }

        $this->participant = $participant;

        return $this;
    }

    public function getNomCompletParticipant(): ?string
    {
        return $this->participant
            ? $this->participant->getPrenom() . ' ' . $this->participant->getNom()
            : null;
    }

    public function getNomCompletUser(): ?string
    {
        return $this->user
            ? $this->user->getPrenom() . ' ' . $this->user->getNom()
            : null;
    }
}
