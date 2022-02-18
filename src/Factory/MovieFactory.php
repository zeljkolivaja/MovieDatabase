<?php

namespace App\Factory;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use DateTime;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Movie>
 *
 * @method static Movie|Proxy createOne(array $attributes = [])
 * @method static Movie[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Movie|Proxy find(object|array|mixed $criteria)
 * @method static Movie|Proxy findOrCreate(array $attributes)
 * @method static Movie|Proxy first(string $sortedField = 'id')
 * @method static Movie|Proxy last(string $sortedField = 'id')
 * @method static Movie|Proxy random(array $attributes = [])
 * @method static Movie|Proxy randomOrCreate(array $attributes = [])
 * @method static Movie[]|Proxy[] all()
 * @method static Movie[]|Proxy[] findBy(array $attributes)
 * @method static Movie[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Movie[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static MovieRepository|RepositoryProxy repository()
 * @method Movie|Proxy create(array|callable $attributes = [])
 */
final class MovieFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }


    public function notReleased(): self
    {
        return $this->addState(['releaseYear' => null]);
    }

    public function notRated(): self
    {
        return $this->addState(['rating' => null, 'totalVotes' => null]);
    }

    protected function getDefaults(): array
    {
        $PG = ['G', 'PG', 'PG-13', 'R', 'NC-17'];

        $exampleImg = scandir(__DIR__ . '/../' . '../public/images/');
        $exampleImg = (array_filter($exampleImg, function ($var) {
            return (stripos($var, 'poster') !== false);
        }));


        return [
            'title' => self::faker()->realText(20),
            'releaseYear' => self::faker()->dateTimeBetween('-100 years', '-1 minute'),
            'storyLine' => self::faker()->paragraphs('1', true),
            'runtime' => rand(1, 240),
            'PG' => $PG[array_rand($PG, 1)],
            'rating' => rand(70, 100),
            'totalVotes' => rand(20, 100),
            'poster' => $exampleImg[array_rand($exampleImg, 1)],
        ];
    }




    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Movie $movie) {})
        ;
    }

    protected static function getClass(): string
    {
        return Movie::class;
    }
}
