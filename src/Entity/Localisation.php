<?php

namespace App\Entity;

use App\Repository\LocalisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: LocalisationRepository::class)]
class Localisation
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;


    #[ORM\ManyToOne(targetEntity: UserPlateform::class, inversedBy: "localisations")]
    private $user;

    #[ORM\Column(type: "string", length: 1000, nullable: true)]
    private $ville = '';


    #[ORM\Column(type: "float", nullable: true)]
    private $longitude = 0.0;


    #[ORM\Column(type: "float", nullable: true)]
    private $latitude = 0.0;


    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $ip = '';


    #[ORM\Column(type: "datetime")]
    private $dateIn;


    #[ORM\OneToMany(targetEntity: Boutique::class, mappedBy: "localisation")]
    private $boutiques;

    // #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: "localisation")]
    // private $commandes;

    // Rest of your class properties and methods
    public function __construct()
    {

        $this->dateIn = new \DateTime();
        $this->boutiques = new ArrayCollection();
        // $this->commandes = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateIn(): ?\DateTimeInterface
    {
        return $this->dateIn;
    }

    public function setDateIn(\DateTimeInterface $dateIn): self
    {
        $this->dateIn = $dateIn;

        return $this;
    }
    public function getUser(): ?UserPlateform
    {
        return $this->user;
    }

    public function setUser(?UserPlateform $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }
    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return Collection<int, Boutique>
     */
    public function getBoutiques(): Collection
    {
        return $this->boutiques;
    }

    public function addBoutique(Boutique $boutique): self
    {
        if (!$this->boutiques->contains($boutique)) {
            $this->boutiques[] = $boutique;
            $boutique->setLocalisation($this);
        }

        return $this;
    }

    public function removeBoutique(Boutique $boutique): self
    {
        if ($this->boutiques->removeElement($boutique)) {
            // set the owning side to null (unless already changed)
            if ($boutique->getLocalisation() === $this) {
                $boutique->setLocalisation(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Commande>
    //  */
    // public function getCommandes(): Collection
    // {
    //     return $this->commandes;
    // }

    // public function addCommande(Commande $commande): self
    // {
    //     if (!$this->commandes->contains($commande)) {
    //         $this->commandes[] = $commande;
    //         $commande->setLocalisation($this);
    //     }

    //     return $this;
    // }

    // public function removeCommande(Commande $commande): self
    // {
    //     if ($this->commandes->removeElement($commande)) {
    //         // set the owning side to null (unless already changed)
    //         if ($commande->getLocalisation() === $this) {
    //             $commande->setLocalisation(null);
    //         }
    //     }

    //     return $this;
    // }
}
