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
    public ?array $assets;

    /**
     * Root url for assets
     *
     * @var string
     */
    public string $dist_uri;

    /**
     * Root path for assets
     *
     * @var string
     */
    public string $dist_path;

    /**
     * Class constructor
     *
     * @param string $manifest_path Local filesystem path to JSON-encoded manifest.
     * @param string $dist_uri  Remote URI to assets root.
     * @param string $dist_path Local filesystem path to assets root.
     */
    public function __construct( $manifest_path, $dist_uri, $dist_path ) {
        $this->assets    = $this->load_manifest( $manifest_path );
        $this->dist_uri  = $dist_uri;
        $this->dist_path = $dist_path;
    }

    /**
     * Loads the manifest, and creates a PHP version if necessary
     *
     * @param  string $manifest_path Local filesystem path to JSON-encoded manifest.
     * @return array|null                Array of assets, or null if manifest does not exist.
     */
    protected function load_manifest( string $manifest_path ): ?array {
        $manifest_php = preg_replace( '/\.json$/', '.php', $manifest_path );

        if ( file_exists( $manifest_php ) ) {
            return require $manifest_php;
        }
        return file_exists( $manifest_path )
            ? $this->create_manifest_php( $manifest_path, $manifest_php )
            : null;
    }

    /**
     * Creates a PHP version of the manifest
     *
     * @param  string $manifest_path Local filesystem path to JSON-encoded manifest.
     * @param  string $manifest_php  Local filesystem path to PHP-encoded manifest.
     * @return array|null            Array of assets, or null if manifest does not exist.
     */
    protected function create_manifest_php( string $manifest_path, string $manifest_php ): ?array {
        require_once ABSPATH . '/wp-admin/includes/file.php';
        WP_Filesystem();

        global $wp_filesystem;

        $manifest = json_decode( $wp_filesystem->get_contents( $manifest_path ), true );

        if ( ! $manifest ) {
            return null;
        }

        $wp_filesystem->put_contents(
            $manifest_php,
            "<?php\nreturn " . var_export( $manifest, true ) . ";\n" //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
        );

        return $manifest;
    }

    /**
     * Get the cache-busted asset
     *
     * If the manifest does not have an entry for $asset, then return $asset
     *
     * @param  string $asset The original name of the file before cache-busting.
     * @return string
     */
    private function get( string $asset ): string {
        return $this->assets[ $asset ] ?? $asset;
    }

    /**
     * Get the cache-busted URI
     *
     * If the manifest does not have an entry for $asset, then return URI for $asset
     *
     * @param  string $asset The original name of the file before cache-busting, relative to $dist_uri.
     * @return string
     */
    public function get_uri( string $asset ) {
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
    public function get_path( string $asset ) {
        return "{$this->dist_path}/{$this->get($asset)}";
    }
}
