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

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $time_requested = null;

    #[ORM\Column(length: 255)]
    private ?string $target = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $time_commenced = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $time_completed = null;

    #[ORM\OneToMany(mappedBy: 'scan_id', targetEntity: Vulnerability::class)]
    private Collection $vulnerabilities;

    public function __construct()
    {
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

    public function getTimeRequested(): ?\DateTimeInterface
    {
        return $this->time_requested;
    }

    public function setTimeRequested(\DateTimeInterface $time_requested): static
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


    /**
     * @return Collection<int, Vulnerability>
     */
    public function getVulnerabilities(): Collection
    {
        return $this->vulnerabilities;
    }

    public function addVulnerability(Vulnerability $vulnerability): static
    {
        if (!$this->vulnerabilities->contains($vulnerability)) {
            $this->vulnerabilities->add($vulnerability);
            $vulnerability->setScanId($this);
        }

        return $this;
    }

    public function removeVulnerability(Vulnerability $vulnerability): static
    {
        if ($this->vulnerabilities->removeElement($vulnerability)) {
            // set the owning side to null (unless already changed)
            if ($vulnerability->getScanId() === $this) {
                $vulnerability->setScanId(null);
            }
        }

        return $this;
    }
}
