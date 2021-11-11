<?php

namespace App\DataFixtures;

use App\Factory\ImageFactory;
use App\Factory\MovieFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\VideoFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        MovieFactory::createMany(10);
        MovieFactory::new()
            ->notReleased()
            ::createMany(5);

        ImageFactory::createMany(50);
        VideoFactory::createMany(20);

        $manager->flush();
    }
}
