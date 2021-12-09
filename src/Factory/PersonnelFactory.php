<?php

namespace App\Factory;

use App\Entity\Personnel;
use App\Repository\PersonnelRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Personnel>
 *
 * @method static Personnel|Proxy createOne(array $attributes = [])
 * @method static Personnel[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Personnel|Proxy find(object|array|mixed $criteria)
 * @method static Personnel|Proxy findOrCreate(array $attributes)
 * @method static Personnel|Proxy first(string $sortedField = 'id')
 * @method static Personnel|Proxy last(string $sortedField = 'id')
 * @method static Personnel|Proxy random(array $attributes = [])
 * @method static Personnel|Proxy randomOrCreate(array $attributes = [])
 * @method static Personnel[]|Proxy[] all()
 * @method static Personnel[]|Proxy[] findBy(array $attributes)
 * @method static Personnel[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Personnel[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PersonnelRepository|RepositoryProxy repository()
 * @method Personnel|Proxy create(array|callable $attributes = [])
 */
final class PersonnelFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $roles = ['Actor', 'Director', 'Producer', 'Executive Producer', 'Production Manager', 'Screenwriter'];

        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'movie' => MovieFactory::new(),
            'person' => PersonFactory::new(),
            'role' => $roles[array_rand($roles, 1)],
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Personnel $personnel) {})
        ;
    }

    protected static function getClass(): string
    {
        return Personnel::class;
    }
}
