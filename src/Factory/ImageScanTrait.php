<?php


namespace App\Factory;


trait ImageScanTrait
{
    private $images = NULL;

    private function scanDirImages($imgType = null)
    {
        if ($this->images === NULL) {
            $this->images =  scandir(__DIR__ . '/../' . '../public/images/');

            if ($imgType === "poster") {
                $this->images = (array_filter($this->images, function ($var) {
                    return (stripos($var, 'poster') !== false);
                }));
            }
        }

        return $this->images;
    }
}
