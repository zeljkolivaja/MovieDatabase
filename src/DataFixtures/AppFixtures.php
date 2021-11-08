<?php

namespace App\DataFixtures;

use App\Factory\MovieFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        MovieFactory::createMany(10);
        MovieFactory::new()
            ->notReleased()
            ::createMany(5);
    }
}
