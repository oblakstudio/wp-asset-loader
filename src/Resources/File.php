<?php
/**
 * File class file.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

namespace XWP\Dependency\Resources;

use XWP\Dependency\Bundle;

/**
 * Base file resource.
 */
class File {
    /**
     * File extension
     *
     * @var string
     */
    protected string $ext;

    /**
     * File name
     *
     * @var string
     */
    protected string $name;

    /**
     * Constructor
     *
     * @param  Bundle $bundle Bundle instance.
     * @param  string $src    File source.
     */
    public function __construct(
        protected Bundle &$bundle,
        protected string $src,
    ) {
        $this->ext    = \pathinfo( $this->src, \PATHINFO_EXTENSION );
        $this->name ??= \pathinfo( $this->src, \PATHINFO_FILENAME );
    }

    /**
     * Get the file extension
     *
     * @return string
     */
    public function ext(): string {
        return $this->ext;
    }

    /**
     * Get the file path
     *
     * @return string
     */
    public function path(): string {
        return $this->bundle->base_dir() . '/' . $this->src;
    }

    /**
     * Get the file URI
     *
     * @return string
     */
    public function uri(): string {
        return $this->bundle->base_uri() . '/' . $this->src;
    }

    /**
     * Get the file contents
     *
     * @return string
     */
    public function data(): string {
        return \wp_load_filesystem()->get_contents( $this->path() );
    }
}
