<?php

namespace App\Entity;

use App\Repository\TypeUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TypeUserRepository::class)]
#[ApiResource()]
class TypeUser
{
    /**
     
     * 1 => administrateur, 2 => simple client, 3 => livreur
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(["read:typeUser"])]
    private $id;


    #[ORM\Column(type: "string", length: 255)]
    #[Groups(["create:typeUser", "read:typeUser"])]
    private $libelle;


    #[ORM\Column(type: "boolean")]
    #[Groups(["create:typeUser", "read:typeUser"])]
    private $status = true;



    #[Groups(["create:user"])]
    #[ORM\OneToMany(targetEntity: UserPlateform::class, mappedBy: "typeUser")]
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->status = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, UserPlateform>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserPlateform $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setTypeUser($this);
        }

        return $this;
    }

    public function removeUser(UserPlateform $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTypeUser() === $this) {
                $user->setTypeUser(null);
            }
        }

        return $this;
    }
}
