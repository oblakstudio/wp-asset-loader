<?php
/**
 * Image class file.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

namespace XWP\Dependency\Resources;

/**
 * Image resource.
 */
class Image extends File {
    /**
     * Get the base64 encoded data of the image.
     *
     * @return string
     */
    public function base64_data(): string {
        //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
        return \base64_encode( $this->data() );
    }

    /**
     * Get the base64 encoded URI of the image.
     *
     * @return string
     */
    public function base64_uri(): string {
        return "data:image/{$this->ext()};base64,{$this->base64_data()}";
    }
}
