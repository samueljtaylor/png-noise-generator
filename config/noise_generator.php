<?php

return [
    /*
     * Density of the noise to be generated (0 - 100)
     */
    'density' => 75,

    /*
     * Size of the image to be generated
     * WxH or W,H (150x150 or 150,150)
     */
    'size' => '150x150',

    /*
     * Color of the noise to generate
     * Hex color code
     */
    'noiseColor' => '#000',

    /*
     * Opacity of the noise (0 - 100)
     */
    'noiseOpacity' => 100,

    /*
     * Generate a transparent background
     */
    'transparentBackground' => true,

    /*
     * Background color if transparentBackground is false
     */
    'backgroundColor' => '#fff',

    /*
     * Path to save image in
     * null value will place image saved in images folder at the root of this directory
     */
    'path' => null,

    /*
     * File name to save as
     */
    'fileName' => 'recent',
];
