<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
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
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="Movie")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="Movie")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $images;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->images = new ArrayCollection();
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

    public function getRating(): float
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
}
