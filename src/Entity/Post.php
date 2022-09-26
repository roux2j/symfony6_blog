<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Category;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping\ManyToMany;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;


#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;




    #[Assert\NotBlank(
        message : "Le titre est obligatoire"
    )]
    #[Assert\Length(
        max: 255,
        maxMessage: "le titre doit contenir au maximum {{ limit }} caractère",
    )]
    #[ORM\Column(type: 'string', length: 255)]
    /**
     *  Le titre de l'article
     *
     * @var string
     */
    private string $title;



    #[Gedmo\Slug(fields: ['title'])]
    #[ORM\Column(type: 'string', length: 255)]
    /**
     * Le slug de l'article généré sur la base du titre grâce au bundle "Stof" de Doctrine
     *
     * @var string
     */
    private string $slug;


    #[Assert\NotBlank(
        message : "veuillez choisir une catégorie"
    )]
    #[Assert\Type(
        type: Category::class,
        message: 'Veuillez sélectionner une catégorie valide.',
    )]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    /**
     * Cette variable est un objet de type Category
     *
     * @var Category
     */
    private Category $category;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    /**
     * cette propriété représente le chemin de l'image qui sera inséré en base
     * 
     * ça n'est jamais l'image qui est stocké, mais son chemin
     *
     * @var string
     */
    private $image;
    
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     */
    #[Assert\File(
        maxSize: '1024k',
        mimeTypes: ['image/png', 'image/jpeg', 'image/bmp', 'image/webp', 'image/svg+xml'],
        mimeTypesMessage: 'Seuls ces formats d\'image sont vonlides : png, jpeg, bmp, webp, svg.',
    )]
    #[Vich\UploadableField(mapping: 'post_image', fileNameProperty: 'image')]
    /**
     * Cette propriété représente l'input de l'image
     *
     * @var File|null
     */
    private ?File $imageFile = null;

    #[Assert\NotBlank(
        message : "Le contenu est obligatoire"
    )]
    #[Assert\Length(
        max: 500000,
        maxMessage: "veuillez entrer au maximum {{ limit }} caractères.",
    )]
    #[ORM\Column(type: 'text')]
    private $content;



    #[ORM\Column(type: 'boolean', options: array('default' => false))]
    private $isPublished;



    /**
     * @var \DateTime
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $createdAt;


    /**
     * @var \DateTime
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $publishedAt;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
    private $tags;

    //-------------------------------------------------
    //--------------------------------------------------
    //--------------------------------------------------

    public function __construct()
    {
        return $this->isPublished = false;
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }



    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }



    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): ?Collection
    {
        return $this->tags;
    }

    public function addTag(?Tag $tag): self
    {
        // if (!$this->tags->contains($tag)) {
        //     $this->tags[] = $tag;
        // }

        if($tag != null) 
        {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
