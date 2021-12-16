<?php

namespace App\Factory;

use App\Entity\UserMovie;
use App\Repository\UserMovieRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<UserMovie>
 *
 * @method static UserMovie|Proxy createOne(array $attributes = [])
 * @method static UserMovie[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static UserMovie|Proxy find(object|array|mixed $criteria)
 * @method static UserMovie|Proxy findOrCreate(array $attributes)
 * @method static UserMovie|Proxy first(string $sortedField = 'id')
 * @method static UserMovie|Proxy last(string $sortedField = 'id')
 * @method static UserMovie|Proxy random(array $attributes = [])
 * @method static UserMovie|Proxy randomOrCreate(array $attributes = [])
 * @method static UserMovie[]|Proxy[] all()
 * @method static UserMovie[]|Proxy[] findBy(array $attributes)
 * @method static UserMovie[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static UserMovie[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static UserMovieRepository|RepositoryProxy repository()
 * @method UserMovie|Proxy create(array|callable $attributes = [])
 */
final class UserMovieFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [

            'favorite' => $this->faker()->boolean(30),
            'watchLater' => $this->faker()->boolean(20),
            'review' =>  $this->faker()->paragraph(10),
            'rated' => $this->faker()->boolean(10),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(UserMovie $userMovie) {})
        ;
    }

    protected static function getClass(): string
    {
        return UserMovie::class;
    }
}
