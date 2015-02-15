<?php namespace App\Services;

use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;

class CoverCreator
{

    private $source = null;

    private $name = null;

    public function __construct($source, $name)
    {
        $this->source = $source;
        $this->name = $name;
    }

    public function create()
    {
        if ($this->source != null) {
            // créer différentes tailles d'images
            $coversPath = Config::get('koobe.paths.covers');
            $cover = Image::make($this->source)->encode('jpg', 75);
            $cover->save($coversPath . DIRECTORY_SEPARATOR . $this->name . '.jpg');
        }
    }
}