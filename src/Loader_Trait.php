<?php
/**
 * Loader_Trait class file.
 *
 * @package Asset Loader
 */

namespace Oblak\WP;

/**
 * Getters for asset path and URI.
 */
trait Loader_Trait {

    /**
     * Namespace for the assets
     *
     * @var string
     */
    protected $namespace;

    /**
     * Initializes the asset loader
     *
     * @param array $assets Array of assets to load.
     */
    protected function init_asset_loader( $assets ) {
        add_action(
            'init',
            function() use ( $assets ) {
                Asset_Loader::get_instance()->register_namespace( $this->namespace, $assets );
            }
        );
    }

    /**
     * Get the cache buster asset path
     *
     * @param  string $asset Asset path.
     * @return string        Asset path with cache buster.
     */
    public function asset_path( $asset ) {
        return Asset_Loader::get_instance()->get_path( $this->namespace, $asset );
    }

    /**
     * Get the cache buster asset uri
     *
     * @param  string $asset Asset uri.
     * @return string        Asset uri with cache buster.
     */
    public function asset_uri( $asset ) {
        return Asset_Loader::get_instance()->get_uri( $this->namespace, $asset );
    }
}
