<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\ImageFactory;
use App\Factory\MovieFactory;
use App\Factory\PersonFactory;
use App\Factory\PersonnelFactory;
use App\Factory\UserFactory;
use App\Factory\UserMovieFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\VideoFactory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {


        //admin user
        UserFactory::createOne([
            'email' => 'admin@test.com',
            'roles' => ['ROLE_ADMIN'],
        ]);

        //regular user
        UserFactory::createOne([
            'email' => 'user@test.com',
        ]);


        //create 9 categories(movie genres)
        CategoryFactory::createMany(9);

        //create 100 movies, connect each movie to random amount of categories(min 1 category , max 5)
        //select five images for each movie
        MovieFactory::createMany(50, function () {
            return [
                'categories' => CategoryFactory::randomRange(1, 3),
                'images' => ImageFactory::new()->many(5),
            ];
        });


        //create 50 users
        UserFactory::createMany(50);

        //create 400 join tables between random users and movies
        UserMovieFactory::createMany(400, function () {
            return [
                "user" => UserFactory::random(),
                "movie" => MovieFactory::random(),
            ];
        });

        //create 50 persons (movie personnel, actors, directors etc)
        PersonFactory::createMany(50);

        //create 100 manyToMany relations between movies and persons, chose random movie and person objects from already created ones
        PersonnelFactory::createMany(100, function () {
            return [
                "movie" => MovieFactory::random(),
                "person" => PersonFactory::random(),
            ];
        });


        //create 5 movies that are not released (releaseDate set to null for now,
        //TODO maybe add new field "released" with boolean, since maybe we will allow movies to have release date in the future)
        //assign them to random category and create two images for each
        MovieFactory::new()
            ->notReleased()
            ->many(5)
            ->create(function () {
                return [
                    'categories' => CategoryFactory::randomRange(1, 5),
                    'images' => ImageFactory::new()->many(2)
                ];
            });


        //create 10 movies that have no rating
        MovieFactory::new()
            ->notRated()
            ->many(10)
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
