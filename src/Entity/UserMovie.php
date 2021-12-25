<?php

namespace App\Entity;

use App\Repository\UserMovieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UserMovieRepository::class)
 */
class UserMovie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userMovies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Movie::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $movie;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $favorite = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $watchLater = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $rated = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $review = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * 
     */
    private $reviewTitle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getFavorite(): ?bool
    {
        return $this->favorite;
    }

    public function setFavorite(?bool $favorite): self
    {
        $this->favorite = $favorite;

        return $this;
    }

    public function getWatchLater(): ?bool
    {
        return $this->watchLater;
    }

    public function setWatchLater(?bool $watchLater): self
    {
        $this->watchLater = $watchLater;

        return $this;
    }

    public function getRated(): ?bool
    {
        return $this->rated;
    }

    public function setRated(?bool $rated): self
    {
        $this->rated = $rated;

        return $this;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReviewTitle(): ?string
    {
        return $this->reviewTitle;
    }

    public function setReviewTitle(?string $reviewTitle): self
    {
        $this->reviewTitle = $reviewTitle;

        return $this;
    }
}
