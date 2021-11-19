<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Factory\CategoryFactory;
use App\Factory\ImageFactory;
use App\Factory\MovieFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\VideoFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        //create 10 categories(movie genres)
        CategoryFactory::createMany(10);

        //create 100 movies, connect each movie to random amount of categories(min 1 category , max 5)
        //create two images(just random strings for now) for each movie
        MovieFactory::createMany(100, function () {
            return [
                'categories' => CategoryFactory::randomRange(1, 5),
                'images' => ImageFactory::new()->many(2)
            ];
        });


        //create 20 movies that are not released (releaseDate set to null for now,
        //TODO maybe add new field "released" with boolean, since maybe we will allow movies to have release date in the future)
        //assign them to random category and create two images for each
        MovieFactory::new()
            ->notReleased()
            ->many(20)
            ->create(function () {
                return [
                    'categories' => CategoryFactory::randomRange(1, 5),
                    'images' => ImageFactory::new()->many(2)
                ];
            });

        //create 20 videos(just random strings for now), assign them to random movies
        VideoFactory::createMany(20);


        $manager->flush();
    }
}
