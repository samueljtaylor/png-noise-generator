<?php


namespace SamuelJTaylor\NoiseGenerator;


class NoiseGenerator
{
    /**
     * The image resource
     *
     * @var \GdImage
     */
    protected $image;

    /**
     * File name and path
     *
     * @var string
     */
    protected $file;

    /**
     * @var bool
     */
    protected $saved = false;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * NoiseGenerator constructor.
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->loadConfig();

        foreach($options as $key => $value) {
            $this->setConfig($key, $value);
        }

        $path = $this->config('path') ?? __DIR__.'/../images/';
        $this->file = $path . $this->config('fileName', 'recent');
    }

    /**
     * Load config from config file
     */
    public function loadConfig()
    {
        try {
            $this->config = config('noise_generator');
        } catch(\Exception $exception) {
            $this->config = include_once __DIR__.'/../config/noise_generator.php';
        }
    }

    /**
     * Get a config value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function config($key, $default = null)
    {
        return array_key_exists($key, $this->config) ? $this->config[$key] : $default;
    }

    /**
     * Get width or height from size string
     *
     * @param int $xy X or Y value (0 for X, 1 for Y)
     */
    public function getSizeValue($xy)
    {
        if($xy !== 0 || $xy !== 1) {
            return false;
        }

        preg_match_all('/(\d).(\d)/', $this->config['size'], $matches);
        return $matches[0][$xy];
    }

    /**
     * Get the width
     *
     * @return mixed
     */
    public function width()
    {
        return $this->getSizeValue(0);
    }


    /**
     * Get the height
     *
     * @return mixed
     */
    public function height()
    {
        return $this->getSizeValue(1);
    }

    /**
     * Get the image resource
     *
     * @return false|\GdImage|resource
     */
    public function image()
    {
        if(!isset($this->image)) {
            $this->image = imagecreatetruecolor($this->width(), $this->height());
        }
        return $this->image;
    }

    /**
     * Get a color from RGB
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     * @param int $alpha
     * @return false|int
     */
    public function rgbColor($red, $green, $blue, $alpha = 0)
    {
        return imagecolorallocatealpha($this->image(), $red, $green, $blue, $alpha);
    }

    /**
     * Get a color from hex code
     *
     * @param string $hex
     * @param int $alpha
     * @return false|int
     */
    public function hexColor($hex, $alpha = 0)
    {
        $hex = str_replace('#', '', $hex);
        $length = strlen($hex);

        if($length !== 6 && $length !== 3) {
            return false;
        }

        $red = hexdec($length === 3 ? $hex[0].$hex[0] : $hex[0].$hex[1]);
        $green = hexdec($length === 3 ? $hex[1].$hex[1] : $hex[2].$hex[3]);
        $blue = hexdec($length === 3 ? $hex[2].$hex[2] : $hex[4].$hex[5]);

        return $this->rgbColor($red, $green, $blue, $alpha);
    }

    /**
     * Get transparent color
     *
     * @return false|int
     */
    public function transparent()
    {
        return $this->rgbColor(0,0,0,127);
    }

    /**
     * Fill image
     *
     * @return $this
     */
    public function fillImage()
    {
        $background = $this->config('transparentBackground', true)
                    ? $this->transparent()
                    : $this->hexColor($this->config('backgroundColor', 'fff'));

        imagefill($this->image(), 0, 0, $background);
        return $this;
    }

    /**
     * Get the noise color
     *
     * @return false|int
     */
    public function noiseColor()
    {
        $alpha = 127 - ($this->config('noiseOpacity', 100) / 100 * 127);
        return $this->hexColor($this->config('noiseColor', '000'), (int) $alpha);
    }

    /**
     * Generate noise on the filled image
     *
     * Go through each pixel and assign it with noise or not based
     * on mt_rand and density value in config.
     *
     * @return $this
     */
    public function generate()
    {
        for($i = 0; $i < $this->width(); $i++) {
            for($j = 0; $j < $this->height(); $j++) {
                if (mt_rand(0, 101-$this->config('density', 100)) == 1) {
                    imagesetpixel($this->image(), $i, $j, $this->noiseColor());
                }
            }
        }
        return $this;
    }

    /**
     * Make the noisy image
     *
     * @return NoiseGenerator
     */
    public function make()
    {
        return $this->fillImage()->generate();
    }

    /**
     * Save the image
     *
     * @return string
     */
    public function save()
    {
        imagesavealpha($this->image(), $this->config('transparentBackground', true));
        ImagePNG($this->image(), $this->file, 9);
        $this->saved = true;
        return $this->file;
    }

    /**
     * Set a config value
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setConfig($key, $value)
    {
        $this->config[$key] = $value;
        return $this;
    }

    /**
     * Generate and save an image
     *
     * @param array $options
     * @return string
     */
    public static function generatePng($options = [])
    {
        return (new static($options))->make()->save();
    }
}
