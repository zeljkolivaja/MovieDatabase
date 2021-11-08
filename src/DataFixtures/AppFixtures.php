<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $movie = new Movie();
        $movie->setTitle("Test Movie Fixtures");
        $movie->setSlug("test-movie-" . rand(0, 1000));
        $movie->setReleaseYear(new DateTime("now"));
        $manager->persist($movie);
        $manager->flush();
    }
}
