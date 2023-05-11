<?php

namespace App\Entity;

use App\Repository\ModePaiementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

#[ORM\Entity(repositoryClass: ModePaiementRepository::class)]
class ModePaiement
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;


    #[ORM\Column(type: "string", length: 255)]
    private $libelle;


    #[ORM\Column(type: "string", length: 255)]
    private $siteId;


    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: "modePaiement")]
    private $transactions;


    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: "modePaiement")]
    private $ModePaiement;



    public function __construct()
    {

        $this->transactions = new ArrayCollection();
        $this->ModePaiement = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteId(): ?string
    {
        return $this->siteId;
    }

    public function setSiteId(string $siteId): self
    {
        $this->siteId = $siteId;

        return $this;
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

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setModePaiement($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getModePaiement() === $this) {
                $transaction->setModePaiement(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Commande>
     */
    public function getModePaiement(): Collection
    {
        return $this->ModePaiement;
    }

    public function addModePaiement(Commande $modePaiement): self
    {
        if (!$this->ModePaiement->contains($modePaiement)) {
            $this->ModePaiement[] = $modePaiement;
            $modePaiement->setModePaiement($this);
        }

        return $this;
    }

    public function removeModePaiement(Commande $modePaiement): self
    {
        if ($this->ModePaiement->removeElement($modePaiement)) {
            // set the owning side to null (unless already changed)
            if ($modePaiement->getModePaiement() === $this) {
                $modePaiement->setModePaiement(null);
            }
        }

        return $this;
    }
}
