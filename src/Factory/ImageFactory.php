<?php

namespace App\Factory;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Image>
 *
 * @method static Image|Proxy createOne(array $attributes = [])
 * @method static Image[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Image|Proxy find(object|array|mixed $criteria)
 * @method static Image|Proxy findOrCreate(array $attributes)
 * @method static Image|Proxy first(string $sortedField = 'id')
 * @method static Image|Proxy last(string $sortedField = 'id')
 * @method static Image|Proxy random(array $attributes = [])
 * @method static Image|Proxy randomOrCreate(array $attributes = [])
 * @method static Image[]|Proxy[] all()
 * @method static Image[]|Proxy[] findBy(array $attributes)
 * @method static Image[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Image[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ImageRepository|RepositoryProxy repository()
 * @method Image|Proxy create(array|callable $attributes = [])
 */
final class ImageFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }


    use ImageScanTrait;

    protected function getDefaults(): array
    {
        //using ImageScanTrait to reuse logic and to scan directory for images only once;
        $exampleImg = $this->scanDirImages();

        $exampleImg = (array_filter($exampleImg, function ($var) {
            return (stripos($var, 'img') !== false);
        }));

        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'fileName' => $exampleImg[array_rand($exampleImg, 1)],
            'movie' => MovieFactory::random(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Image $image) {})
        ;
    }

    protected static function getClass(): string
    {
        return Image::class;
    }
}
