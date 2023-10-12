<?php

namespace App\Entity;

use App\Repository\ScanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScanRepository::class)]
class Scan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'scans')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $time_requested = null;

    #[ORM\Column(length: 255)]
    private ?string $target = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $time_commenced = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $time_completed = null;

    public function __construct()
    {
        $this->tools = new ArrayCollection();
        $this->vulnerabilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getTimeRequested(): ?\DateTimeImmutable
    {
        return $this->time_requested;
    }

    public function setTimeRequested(\DateTimeImmutable $time_requested): static
    {
        $this->time = $time_requested;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(string $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTimeCommenced(): ?\DateTimeInterface
    {
        return $this->time_commenced;
    }

    public function setTimeCommenced(\DateTimeInterface $time_commenced): static
    {
        $this->time_commenced = $time_commenced;

        return $this;
    }

    public function getTimeCompleted(): ?\DateTimeInterface
    {
        return $this->time_completed;
    }

    public function setTimeCompleted(\DateTimeInterface $time_completed): static
    {
        $this->time_completed = $time_completed;

        return $this;
    }
}
