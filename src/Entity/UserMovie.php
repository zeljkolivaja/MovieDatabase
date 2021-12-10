<?php

namespace App\Entity;

use App\Repository\UserMovieRepository;
use Doctrine\ORM\Mapping as ORM;

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
}
