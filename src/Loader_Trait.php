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
    protected ?string $namespace = null;

    /**
     * Initializes the asset loader
     *
     * @param array       $args      Array of assets to load.
     * @param string|null $namespace Namespace for the assets. Defaults to null. Optional.
     */
    protected function init_asset_loader( array $args, ?string $namespace = null ): void {
        $this->namespace ??= $namespace ?? $args['namespace'] ?? wp_generate_uuid4();
        add_action( 'init', fn() => Asset_Loader::get_instance()->register_namespace( $this->namespace, $args ) );
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
