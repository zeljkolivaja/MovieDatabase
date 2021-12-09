<?php

namespace App\Factory;

use App\Entity\Person;
use App\Repository\PersonRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Person>
 *
 * @method static Person|Proxy createOne(array $attributes = [])
 * @method static Person[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Person|Proxy find(object|array|mixed $criteria)
 * @method static Person|Proxy findOrCreate(array $attributes)
 * @method static Person|Proxy first(string $sortedField = 'id')
 * @method static Person|Proxy last(string $sortedField = 'id')
 * @method static Person|Proxy random(array $attributes = [])
 * @method static Person|Proxy randomOrCreate(array $attributes = [])
 * @method static Person[]|Proxy[] all()
 * @method static Person[]|Proxy[] findBy(array $attributes)
 * @method static Person[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Person[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PersonRepository|RepositoryProxy repository()
 * @method Person|Proxy create(array|callable $attributes = [])
 */
final class PersonFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'birthDate' => self::faker()->dateTimeBetween('-100 years', '-1 minute'),
            'countryOfBirth' => self::faker()->countryCode(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Person $person) {})
        ;
    }

    protected static function getClass(): string
    {
        return Person::class;
    }
}
