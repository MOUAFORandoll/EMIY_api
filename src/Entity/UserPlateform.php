<?php

namespace App\Entity;


use App\Repository\UserPlateformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UserCreateController;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use App\FunctionU\MyFunction;


#[ORM\Entity(repositoryClass: UserPlateformRepository::class)]
#[ApiResource(
    itemOperations: [
        'get' => [],
        'patch' => [
            'denormalization_context' => [
                'groups' => ['create:user']
            ],
            'controller' => UserCreateController::class
        ],
        'delete' => []
    ],
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:user']
            ],
            'security' => "is_granted('IS_AUTHENTICATED_FULLY')"
        ],
        'post' => [
            'denormalization_context' => [
                'groups' => ['create:user']
            ],
            'controller' => UserCreateController::class
        ]
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'prenom' => 'exact',
        'nom' => 'exact',
        'phone' => 'exact',
        'email' => 'exact',
        'typeUser' => 'exact'
    ]
)]
class UserPlateform implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(["create:user", "read:user"])]
    private $nom;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(["create:user", "read:user"])]
    private $prenom;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Groups(["create:user", "read:user"])]
    private $email;

    #[ORM\Column(type: "json")]
    private $roles = ['ROLE_USER'];

    #[ORM\Column(type: "integer", length: 255, unique: true)]
    #[Groups(["create:user", "read:user"])]
    private $phone;

    #[ORM\Column(type: "boolean")]
    #[Groups(["create:user", "read:user"])]
    private $status = true;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups(["create:user"])]
    private $password;

    #[ORM\ManyToOne(targetEntity: TypeUser::class, inversedBy: "users")]
    #[Groups(["create:user"])]
    private $typeUser;

    #[ORM\Column(type: "date")]
    private $dateCreated;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: "client")]
    #[Groups(["read:user"])]
    private $transactions;

    #[ORM\Column(type: "string", length: 10000000000, nullable: true)]
    private $keySecret;

    #[ORM\OneToMany(targetEntity: Boutique::class, mappedBy: "user")]
    private $boutiques;

    #[ORM\OneToMany(targetEntity: Panier::class, mappedBy: "user")]
    private $paniers;

    #[ORM\OneToMany(targetEntity: ListCommandeLivreur::class, mappedBy: "livreur")]
    private $listCommandeLivreurs;

    #[ORM\OneToMany(targetEntity: Compte::class, mappedBy: "user")]
    private $comptes;

    #[ORM\Column(type: "string",   nullable: true)]
    #[Groups(["create:user", "read:user"])]
    private $codeParrainage;


    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $codeRecup;


    #[ORM\OneToMany(targetEntity: Localisation::class, mappedBy: "user")]
    private $localisations;


    #[ORM\OneToMany(targetEntity: HistoriquePaiement::class, mappedBy: "user")]
    private $historiquePaiements;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: LikeProduit::class)]
    private Collection $LikeProduits;

    #[ORM\OneToMany(mappedBy: 'initiateur', targetEntity: NegociationProduit::class)]
    private Collection $negociationProduits;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: AbonnementBoutique::class)]
    private Collection $abonnementBoutiques;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Communication::class)]
    private Collection $communications;

    #[ORM\OneToMany(mappedBy: 'initiateur', targetEntity: MessageNegociation::class)]
    private Collection $messageNegociations;

    #[ORM\OneToMany(mappedBy: 'initiateur', targetEntity: MessageCommunication::class)]
    private Collection $messageCommunications;

    #[ORM\OneToMany(mappedBy: 'parrain', targetEntity: Parrainage::class)]
    private Collection $parrainages;

    #[ORM\OneToMany(mappedBy: 'user_plateform', targetEntity: UserObject::class)]
    private Collection $userObjects;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: ShortLike::class)]
    private Collection $shortLikes;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: ShortComment::class)]
    private Collection $shortComments;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: ShortCommentLike::class)]
    private Collection $shortCommentLikes;

    #[ORM\OneToMany(mappedBy: 'initiateur', targetEntity: Notification::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'recepteur', targetEntity: Notification::class)]
    private Collection $notification_recepteur;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: UserReadShort::class)]
    private Collection $userReadShorts;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $user_tag = null;
  
    public function __construct( )
    {
      
        $this->dateCreated = new \DateTime();

        $this->status = true;

        $this->transactions = new ArrayCollection();
        $this->boutiques = new ArrayCollection();
        $this->paniers = new ArrayCollection();
        $this->listCommandeLivreurs = new ArrayCollection();
        $this->comptes = new ArrayCollection();
        $this->localisations = new ArrayCollection();
        $this->historiquePaiements = new ArrayCollection();
        $this->LikeProduits = new ArrayCollection();
        $this->negociationProduits = new ArrayCollection();
        $this->abonnementBoutiques = new ArrayCollection();
        $this->communications = new ArrayCollection();
        $this->messageNegociations = new ArrayCollection();
        $this->messageCommunications = new ArrayCollection();
        $this->parrainages = new ArrayCollection();
        $this->userObjects = new ArrayCollection();
        $this->shortLikes = new ArrayCollection();
        $this->shortComments = new ArrayCollection();
        $this->shortCommentLikes = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->notification_recepteur = new ArrayCollection();

        $this->userReadShorts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    public function getProfile()
    {
    //     return count($this->getUserObjects())  == 0 ? '' : new MyFunction()->getBackendUrl() . '/images/users/' . $this->getUserObjects()->first()->getSrc();
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function   getUserIdentifier(): string
    {
        return (string) $this->phone;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->phone;
    }

    public function getTypeUser(): ?TypeUser
    {
        return $this->typeUser;
    }

    public function setTypeUser(?TypeUser $typeUser): self
    {
        $this->typeUser = $typeUser;

        return $this;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

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
            $transaction->setClient($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getClient() === $this) {
                $transaction->setClient(null);
            }
        }

        return $this;
    }

    public function getKeySecret(): ?string
    {
        return $this->keySecret;
    }

    public function setKeySecret(string $keySecret): self
    {
        $this->keySecret = $keySecret;

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
            $boutique->setUser($this);
        }

        return $this;
    }

    public function removeBoutique(Boutique $boutique): self
    {
        if ($this->boutiques->removeElement($boutique)) {
            // set the owning side to null (unless already changed)
            if ($boutique->getUser() === $this) {
                $boutique->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
            $panier->setUser($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getUser() === $this) {
                $panier->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ListCommandeLivreur>
     */
    public function getListCommandeLivreurs(): Collection
    {
        return $this->listCommandeLivreurs;
    }

    public function addListCommandeLivreur(ListCommandeLivreur $listCommandeLivreur): self
    {
        if (!$this->listCommandeLivreurs->contains($listCommandeLivreur)) {
            $this->listCommandeLivreurs[] = $listCommandeLivreur;
            $listCommandeLivreur->setLivreur($this);
        }

        return $this;
    }

    public function removeListCommandeLivreur(ListCommandeLivreur $listCommandeLivreur): self
    {
        if ($this->listCommandeLivreurs->removeElement($listCommandeLivreur)) {
            // set the owning side to null (unless already changed)
            if ($listCommandeLivreur->getLivreur() === $this) {
                $listCommandeLivreur->setLivreur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Compte>
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setUser($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getUser() === $this) {
                $compte->setUser(null);
            }
        }

        return $this;
    }

    public function getCodeParrainage(): ?string
    {
        return $this->codeParrainage;
    }

    public function setCodeParrainage(string $codeParrainage): self
    {
        $this->codeParrainage = $codeParrainage;

        return $this;
    }

    public function getCodeRecup(): ?string
    {
        return $this->codeRecup;
    }

    public function setCodeRecup(string $codeRecup): self
    {
        $this->codeRecup = $codeRecup;

        return $this;
    }

    /**
     * @return Collection<int, Localisation>
     */
    public function getLocalisations(): Collection
    {
        return $this->localisations;
    }

    public function addLocalisation(Localisation $localisation): self
    {
        if (!$this->localisations->contains($localisation)) {
            $this->localisations[] = $localisation;
            $localisation->setUser($this);
        }

        return $this;
    }

    public function removeLocalisation(Localisation $localisation): self
    {
        if ($this->localisations->removeElement($localisation)) {
            // set the owning side to null (unless already changed)
            if ($localisation->getUser() === $this) {
                $localisation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HistoriquePaiement>
     */
    public function getHistoriquePaiements(): Collection
    {
        return $this->historiquePaiements;
    }

    public function addHistoriquePaiement(HistoriquePaiement $historiquePaiement): self
    {
        if (!$this->historiquePaiements->contains($historiquePaiement)) {
            $this->historiquePaiements[] = $historiquePaiement;
            $historiquePaiement->setUser($this);
        }

        return $this;
    }

    public function removeHistoriquePaiement(HistoriquePaiement $historiquePaiement): self
    {
        if ($this->historiquePaiements->removeElement($historiquePaiement)) {
            // set the owning side to null (unless already changed)
            if ($historiquePaiement->getUser() === $this) {
                $historiquePaiement->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LikeProduit>
     */
    public function isLike_produitProduits(): Collection
    {
        return $this->LikeProduits;
    }

    public function addLikeProduit(LikeProduit $LikeProduit): self
    {
        if (!$this->LikeProduits->contains($LikeProduit)) {
            $this->LikeProduits->add($LikeProduit);
            $LikeProduit->setClient($this);
        }

        return $this;
    }

    public function removeLikeProduit(LikeProduit $LikeProduit): self
    {
        if ($this->LikeProduits->removeElement($LikeProduit)) {
            // set the owning side to null (unless already changed)
            if ($LikeProduit->getClient() === $this) {
                $LikeProduit->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NegociationProduit>
     */
    public function getNegociationProduits(): Collection
    {
        return $this->negociationProduits;
    }

    public function addNegociationProduit(NegociationProduit $negociationProduit): self
    {
        if (!$this->negociationProduits->contains($negociationProduit)) {
            $this->negociationProduits->add($negociationProduit);
            $negociationProduit->setInitiateur($this);
        }

        return $this;
    }

    public function removeNegociationProduit(NegociationProduit $negociationProduit): self
    {
        if ($this->negociationProduits->removeElement($negociationProduit)) {
            // set the owning side to null (unless already changed)
            if ($negociationProduit->getInitiateur() === $this) {
                $negociationProduit->setInitiateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AbonnementBoutique>
     */
    public function getAbonnementBoutiques(): Collection
    {
        return $this->abonnementBoutiques;
    }

    public function addAbonnementBoutique(AbonnementBoutique $abonnementBoutique): self
    {
        if (!$this->abonnementBoutiques->contains($abonnementBoutique)) {
            $this->abonnementBoutiques->add($abonnementBoutique);
            $abonnementBoutique->setClient($this);
        }

        return $this;
    }

    public function removeAbonnementBoutique(AbonnementBoutique $abonnementBoutique): self
    {
        if ($this->abonnementBoutiques->removeElement($abonnementBoutique)) {
            // set the owning side to null (unless already changed)
            if ($abonnementBoutique->getClient() === $this) {
                $abonnementBoutique->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Communication>
     */
    public function getCommunications(): Collection
    {
        return $this->communications;
    }

    public function addCommunication(Communication $communication): self
    {
        if (!$this->communications->contains($communication)) {
            $this->communications->add($communication);
            $communication->setClient($this);
        }

        return $this;
    }

    public function removeCommunication(Communication $communication): self
    {
        if ($this->communications->removeElement($communication)) {
            // set the owning side to null (unless already changed)
            if ($communication->getClient() === $this) {
                $communication->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MessageNegociation>
     */
    public function getMessageNegociations(): Collection
    {
        return $this->messageNegociations;
    }

    public function addMessageNegociation(MessageNegociation $messageNegociation): self
    {
        if (!$this->messageNegociations->contains($messageNegociation)) {
            $this->messageNegociations->add($messageNegociation);
            $messageNegociation->setInitiateur($this);
        }

        return $this;
    }

    public function removeMessageNegociation(MessageNegociation $messageNegociation): self
    {
        if ($this->messageNegociations->removeElement($messageNegociation)) {
            // set the owning side to null (unless already changed)
            if ($messageNegociation->getInitiateur() === $this) {
                $messageNegociation->setInitiateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MessageCommunication>
     */
    public function getMessageCommunications(): Collection
    {
        return $this->messageCommunications;
    }

    public function addMessageCommunication(MessageCommunication $messageCommunication): self
    {
        if (!$this->messageCommunications->contains($messageCommunication)) {
            $this->messageCommunications->add($messageCommunication);
            $messageCommunication->setInitiateur($this);
        }

        return $this;
    }

    public function removeMessageCommunication(MessageCommunication $messageCommunication): self
    {
        if ($this->messageCommunications->removeElement($messageCommunication)) {
            // set the owning side to null (unless already changed)
            if ($messageCommunication->getInitiateur() === $this) {
                $messageCommunication->setInitiateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Parrainage>
     */
    public function getParrainages(): Collection
    {
        return $this->parrainages;
    }

    public function addParrainage(Parrainage $parrainage): static
    {
        if (!$this->parrainages->contains($parrainage)) {
            $this->parrainages->add($parrainage);
            $parrainage->setParrain($this);
        }

        return $this;
    }

    public function removeParrainage(Parrainage $parrainage): static
    {
        if ($this->parrainages->removeElement($parrainage)) {
            // set the owning side to null (unless already changed)
            if ($parrainage->getParrain() === $this) {
                $parrainage->setParrain(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserObject>
     */
    public function getUserObjects(): Collection
    {
        return $this->userObjects;
    }

    public function addUserObject(UserObject $userObject): static
    {
        if (!$this->userObjects->contains($userObject)) {
            $this->userObjects->add($userObject);
            $userObject->setUserPlateform($this);
        }

        return $this;
    }

    public function removeUserObject(UserObject $userObject): static
    {
        if ($this->userObjects->removeElement($userObject)) {
            // set the owning side to null (unless already changed)
            if ($userObject->getUserPlateform() === $this) {
                $userObject->setUserPlateform(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShortLike>
     */
    public function getShortLikes(): Collection
    {
        return $this->shortLikes;
    }

    public function addShortLike(ShortLike $shortLike): static
    {
        if (!$this->shortLikes->contains($shortLike)) {
            $this->shortLikes->add($shortLike);
            $shortLike->setClient($this);
        }

        return $this;
    }

    public function removeShortLike(ShortLike $shortLike): static
    {
        if ($this->shortLikes->removeElement($shortLike)) {
            // set the owning side to null (unless already changed)
            if ($shortLike->getClient() === $this) {
                $shortLike->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShortComment>
     */
    public function getShortComments(): Collection
    {
        return $this->shortComments;
    }

    public function addShortComment(ShortComment $shortComment): static
    {
        if (!$this->shortComments->contains($shortComment)) {
            $this->shortComments->add($shortComment);
            $shortComment->setClient($this);
        }

        return $this;
    }

    public function removeShortComment(ShortComment $shortComment): static
    {
        if ($this->shortComments->removeElement($shortComment)) {
            // set the owning side to null (unless already changed)
            if ($shortComment->getClient() === $this) {
                $shortComment->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShortCommentLike>
     */
    public function getShortCommentLikes(): Collection
    {
        return $this->shortCommentLikes;
    }

    public function addShortCommentLike(ShortCommentLike $shortCommentLike): static
    {
        if (!$this->shortCommentLikes->contains($shortCommentLike)) {
            $this->shortCommentLikes->add($shortCommentLike);
            $shortCommentLike->setClient($this);
        }

        return $this;
    }

    public function removeShortCommentLike(ShortCommentLike $shortCommentLike): static
    {
        if ($this->shortCommentLikes->removeElement($shortCommentLike)) {
            // set the owning side to null (unless already changed)
            if ($shortCommentLike->getClient() === $this) {
                $shortCommentLike->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setInitiateur($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getInitiateur() === $this) {
                $notification->setInitiateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotificationRecepteur(): Collection
    {
        return $this->notification_recepteur;
    }

    public function addNotificationRecepteur(Notification $notificationRecepteur): static
    {
        if (!$this->notification_recepteur->contains($notificationRecepteur)) {
            $this->notification_recepteur->add($notificationRecepteur);
            $notificationRecepteur->setRecepteur($this);
        }

        return $this;
    }

    public function removeNotificationRecepteur(Notification $notificationRecepteur): static
    {
        if ($this->notification_recepteur->removeElement($notificationRecepteur)) {
            // set the owning side to null (unless already changed)
            if ($notificationRecepteur->getRecepteur() === $this) {
                $notificationRecepteur->setRecepteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserReadShort>
     */
    public function getUserReadShorts(): Collection
    {
        return $this->userReadShorts;
    }

    public function addUserReadShorts(UserReadShort $userReadShorts): static
    {
        if (!$this->userReadShorts->contains($userReadShorts)) {
            $this->userReadShorts->add($userReadShorts);
            $userReadShorts->setClient($this);
        }

        return $this;
    }

    public function removeUserReadShorts(UserReadShort $userReadShorts): static
    {
        if ($this->userReadShorts->removeElement($userReadShorts)) {
            // set the owning side to null (unless already changed)
            if ($userReadShorts->getClient() === $this) {
                $userReadShorts->setClient(null);
            }
        }

        return $this;
    }

    public function getUserTag(): ?string
    {
        return $this->user_tag;
    }

    public function setUserTag(?string $user_tag): static
    {
        $this->user_tag = $user_tag;

        return $this;
    }
}