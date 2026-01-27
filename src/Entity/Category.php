<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $socialParentId = null;

    #[ORM\Column]
    private ?int $socialId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSocialParentId(): ?int
    {
        return $this->socialParentId;
    }

    public function setSocialParentId(int $socialParentId): static
    {
        $this->socialParentId = $socialParentId;

        return $this;
    }

    public function getSocialId(): ?int
    {
        return $this->socialId;
    }

    public function setSocialId(int $socialId): static
    {
        $this->socialId = $socialId;

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
}
