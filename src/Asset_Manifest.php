<?php
/**
 * Asset_Manifest class file.
 *
 * @package Asset Loader
 */

namespace Oblak\WP;

/**
 * Class Manifest
 */
class Asset_Manifest {

    /**
     * Manifest assets
     *
     * @var string[]
     */
    public $assets;

    /**
     * Root url for assets
     *
     * @var string
     */
    public $dist_uri;

    /**
     * Root path for assets
     *
     * @var string
     */
    public $dist_path;

    /**
     * Class constructor
     *
     * @param string $manifest_path Local filesystem path to JSON-encoded manifest.
     * @param string $dist_uri  Remote URI to assets root.
     * @param string $dist_path Local filesystem path to assets root.
     */
    public function __construct( $manifest_path, $dist_uri, $dist_path ) {
        $this->assets    = file_exists( $manifest_path )
            ? json_decode( file_get_contents( $manifest_path ), true ) //phpcs:ignore
            : array();
        $this->dist_uri  = $dist_uri;
        $this->dist_path = $dist_path;
    }

    /**
     * Get the cache-busted asset
     *
     * If the manifest does not have an entry for $asset, then return $asset
     *
     * @param  string $asset The original name of the file before cache-busting.
     * @return string
     */
    private function get( $asset ) {
        return isset( $this->assets[ $asset ] ) ? $this->assets[ $asset ] : $asset;
    }

    /**
     * Get the cache-busted URI
     *
     * If the manifest does not have an entry for $asset, then return URI for $asset
     *
     * @param  string $asset The original name of the file before cache-busting, relative to $dist_uri.
     * @return string
     */
    public function get_uri( $asset ) {
        return "{$this->dist_uri}/{$this->get($asset)}";
    }


    /**
     * Get the cache-busted path
     *
     * If the manifest does not have an entry for $asset, then return URI for $asset
     *
     * @param  string $asset The original name of the file before cache-busting, relative to $dist_path.
     * @return string
     */
    public function get_path( $asset ) {
        return "{$this->dist_path}/{$this->get($asset)}";
    }
}
