<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;





#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: "impossible de créer un compte avec cet email")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotBlank(
        message : "le prénom est obligatoire"
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: "le prenom doit contenir au maximum {{limit}} caractère",
    )]
    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;





    #[Assert\NotBlank(
        message : "le nom est obligatoire"
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: "le nom doit contenir au maximum {{limit}} caractère",
    )]
    #[ORM\Column(type: 'string', length: 255)]
    private $lastName;







    #[Assert\NotBlank(
        message : "l'email est obligatoire"
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: "l'email doit contenir au maximum {{limit}} caractère",
    )]
    #[Assert\Email(
        message: "l'email '{{ value }}' n'est pas valide",
    )]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];


    #[Assert\NotBlank(
        message : "le mot de passe et sa confirmation est obligatoire"
    )]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "le mot de passe doit contenir au minimum {{ limit }} caractère",
        maxMessage: "le mot de passe doit contenir au maximum {{ limit }} caractère",
    )]
    #[Assert\Regex(
        pattern: '#^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ỳ])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ỳ0-9]).{8,4096}$#',
        match: true,
        message: 'Votre mot de passe doit contenir au moins un chiffre, une lettre minuscule, une lettre majuscule et un caractère spécial.',
    )]
    #[Assert\NotCompromisedPassword(
    message : "ce mot de passe est facilement piratable, veuillez en choisir un autre"
    )]
    #[ORM\Column(type: 'string',length: 255)]
    private $password;


    #[ORM\Column(type: 'boolean', options:array('default' => false))]
    private $isVerified;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $tokenForEmailVerification;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $deadlineForEmailVerification;

    /**
     * @var \DateTime
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $verifiedAt;

    /**
     * @var \DateTime
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Post::class, orphanRemoval: true)]
    private $posts;

    // -----------------
    // -----------------
    // -----------------

    public function __construct()
    {
        $this-> isVerified = false;
        $this->roles[]     = "ROLE_USER";
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getTokenForEmailVerification(): ?string
    {
        return $this->tokenForEmailVerification;
    }

    public function setTokenForEmailVerification(?string $tokenForEmailVerification): self
    {
        $this->tokenForEmailVerification = $tokenForEmailVerification;

        return $this;
    }

    public function getDeadlineForEmailVerification(): ?\DateTimeImmutable
    {
        return $this->deadlineForEmailVerification;
    }

    public function setDeadlineForEmailVerification(?\DateTimeImmutable $deadlineForEmailVerification): self
    {
        $this->deadlineForEmailVerification = $deadlineForEmailVerification;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }



    public function getVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(?\DateTimeImmutable $verifiedAt): self
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }


}
