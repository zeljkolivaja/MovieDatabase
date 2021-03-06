<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\MovieRepository;



/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
 */
class Movie
{
    use TimestampableEntity;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 40,
     * minMessage = "Your title must be at least {{ limit }} characters long",
     * maxMessage = "Your title cannot be longer than {{ limit }} characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $releaseYear;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rating = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $storyline;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $runtime;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $PG;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalVotes = 0;


    /**
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="Movie", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="Movie", cascade={"remove"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $images;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="movies")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Personnel::class, mappedBy="movie", cascade={"remove"})
     */
    private $personnels;


    /**
     * @ORM\OneToMany(targetEntity=UserMovie::class, mappedBy="movie", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $userMovies;

    /**
     * @ORM\Column(type="string", nullable=true, length=255)
     */
    private $poster;


    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->personnels = new ArrayCollection();
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

    public function getReleaseYear(): ?\DateTimeInterface
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(?\DateTimeInterface $releaseYear): self
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getStoryline(): ?string
    {
        return $this->storyline;
    }

    public function setStoryline(?string $storyline): self
    {
        $this->storyline = $storyline;

        return $this;
    }

    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    public function setRuntime(?int $runtime): self
    {
        $this->runtime = $runtime;

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

    public function getPG(): ?string
    {
        return $this->PG;
    }

    public function setPG(?string $PG): self
    {
        $this->PG = $PG;

        return $this;
    }

    public function getTotalVotes(): ?int
    {
        return $this->totalVotes;
    }

    public function setTotalVotes(?int $totalVotes): self
    {
        $this->totalVotes = $totalVotes;

        return $this;
    }


    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setMovie($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getMovie() === $this) {
                $video->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setMovie($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getMovie() === $this) {
                $image->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection|Personnel[]
     */
    public function getPersonnels(): Collection
    {
        return $this->personnels;
    }

    public function addPersonnel(Personnel $personnel): self
    {
        if (!$this->personnels->contains($personnel)) {
            $this->personnels[] = $personnel;
            $personnel->setMovie($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getMovie() === $this) {
                $personnel->setMovie(null);
            }
        }

        return $this;
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function getPosterSafe()
    {
        return $this->poster ?: 'poster1.jpg';
    }

    public function setPoster(string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }
}
