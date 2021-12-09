<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonRepository::class)
 */
class Person
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $lastName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $birthDate;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $countryOfBirth;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateOfDeath;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $gender;

    /**
     * @ORM\OneToMany(targetEntity=Personnel::class, mappedBy="person")
     */
    private $relatedMovies;

    public function __construct()
    {
        $this->relatedMovies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getCountryOfBirth(): ?string
    {
        return $this->countryOfBirth;
    }

    public function setCountryOfBirth(string $countryOfBirth): self
    {
        $this->countryOfBirth = $countryOfBirth;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getDateOfDeath(): ?\DateTimeInterface
    {
        return $this->dateOfDeath;
    }

    public function setDateOfDeath(?\DateTimeInterface $dateOfDeath): self
    {
        $this->dateOfDeath = $dateOfDeath;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return Collection|Personnel[]
     */
    public function getRelatedMovies(): Collection
    {
        return $this->relatedMovies;
    }

    public function addRelatedMovie(Personnel $relatedMovie): self
    {
        if (!$this->relatedMovies->contains($relatedMovie)) {
            $this->relatedMovies[] = $relatedMovie;
            $relatedMovie->setPerson($this);
        }

        return $this;
    }

    public function removeRelatedMovie(Personnel $relatedMovie): self
    {
        if ($this->relatedMovies->removeElement($relatedMovie)) {
            // set the owning side to null (unless already changed)
            if ($relatedMovie->getPerson() === $this) {
                $relatedMovie->setPerson(null);
            }
        }

        return $this;
    }
}
