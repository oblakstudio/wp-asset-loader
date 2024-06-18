<?php
/**
 * Retriever trait file.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

namespace XWP\Dependency;

/**
 * Retriever trait.
 */
trait Retriever {
    /**
     * Bundle ID.
     *
     * @var string
     */
    private string $bundle_id;

    /**
     * Load the bundle configuration.
     *
     * @param  string $path Path to the bundle configuration file.
     * @return void
     */
    protected function load_bundle_config( string $path ): void {
        $config          = include $path;
        $this->bundle_id = $config['id'];

        \xwp_add_dependency_bundle( $this->bundle_id, $config );
    }

    /**
     * Get the bundle instance.
     *
     * @return Bundle Bundle instance.
     */
    private function bundle(): Bundle {
        return Loader::instance()->get_bundle( $this->bundle_id );
    }

    /**
     * Get the cache-busted file path.
     *
     * @param  string $asset Asset path.
     * @return string
     */
    public function asset_path( string $asset ): string {
        return $this->bundle()[ $asset ]->path();
    }

    /**
     * Get the cache-busted file URI.
     *
     * @param  string $asset Asset URI.
     * @return string
     */
    public function asset_uri( string $asset ): string {
        return $this->bundle()[ $asset ]->uri();
    }

    /**
     * Get the base64 encoded file URI.
     *
     * @param  string $asset Asset path.
     * @return string
     */
    public function asset_base64_uri( string $asset ): string {
        return $this->bundle()[ $asset ]->base64_uri();
    }

    /**
     * Get the cache-busted file data.
     *
     * @param  string $asset Asset path.
     * @return string
     */
    public function asset_data( string $asset ): string {
        return $this->bundle()[ $asset ]->data();
    }

    /**
     * Get the base64 encoded file data.
     *
     * @param  string $asset Asset path.
     * @return string
     */
    public function asset_base64_data( string $asset ): string {
        return $this->bundle()[ $asset ]->base64_data();
    }
}
