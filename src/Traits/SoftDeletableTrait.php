<?php
// src/EventListener/SoftDeleteListener.php
namespace App\Traits; // SoftDeletableTrait.php
use Doctrine\ORM\Mapping as ORM;

trait SoftDeletableTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}
