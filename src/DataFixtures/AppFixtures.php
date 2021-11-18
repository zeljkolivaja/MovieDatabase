<?php

namespace App\DataFixtures;

use App\Entity\Category;
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

        $movie = MovieFactory::createOne();

        $category1  = new Category();
        $category1->setName('Horror');


        $category2  = new Category();
        $category2->setName('Action');

        $movie->addCategory($category1);
        $movie->addCategory($category2);

        $manager->persist($category1);
        $manager->persist($category2);

        $manager->flush();
    }
}
