<?php

namespace App\Entity;

use App\Repository\ToolRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ToolRepository::class)]
class Tool
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tools')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Scan $scan_id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?array $results = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScanId(): ?Scan
    {
        return $this->scan_id;
    }

    public function setScanId(?Scan $scan_id): static
    {
        $this->scan_id = $scan_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getResults(): ?array
    {
        return $this->results;
    }

    public function setResults(?array $results): static
    {
        $this->results = $results;

        return $this;
    }
}
