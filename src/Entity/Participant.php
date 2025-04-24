<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    normalizationContext: ['groups' => ['enrollment:read']],
    denormalizationContext: ['groups' => ['enrollment:write']]
)]

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['participant:read', 'enrollment:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['participant:read', 'participant:write', 'enrollment:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['participant:read', 'participant:write', 'enrollment:read'])]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['participant:read', 'participant:write'])]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\OneToOne(inversedBy: 'participant', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['participant:read', 'participant:write'])]
    private ?Enrollment $enrollment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getEnrollment(): ?Enrollment
    {
        return $this->enrollment;
    }

    public function setEnrollment(Enrollment $enrollment): static
    {
        $this->enrollment = $enrollment;

        if ($enrollment->getParticipant() !== $this) {
            $enrollment->setParticipant($this);
        }

        return $this;
    }

    public function getAge(): int
    {
        $now = new \DateTimeImmutable();
        return $now->diff($this->dateNaissance)->y;
    }
}
