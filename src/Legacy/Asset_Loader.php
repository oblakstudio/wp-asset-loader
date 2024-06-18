<?php
/**
 * Asset_Loader class file.
 *
 * @package Asset Loader
 */

namespace Oblak\WP;

use XWP\Helper\Traits\Singleton;

/**
 * Handles the complete asset loading process.
 */
final class Asset_Loader {
    use Singleton;

    /**
     * Array of registered namespaces
     *
     * @var array
     */
    private array $namespaces = array();

    /**
     * Class constructor
     *
     * Intializes the global loader hook and context, and registers the loader
     */
    public function __construct() {
        \add_filter( 'xwp_dependency_bundles', array( $this, 'load_legacy_bundles' ), 10, 1 );
    }

    /**
     * Gets the singleton instance
     *
     * @return Asset_Loader
     */
    public static function get_instance(): self {
        return self::instance();
    }

    /**
     * Registers a namespace to load assets for
     *
     * @param  string $namespace  Namespace to register.
     * @param  array  $asset_data Asset data.
     * @return void
     */
    public function register_namespace( $namespace, $asset_data ) {
        $this->namespaces[ $namespace ] = array(
            'assets'   => $this->remap_assets( $asset_data['assets'] ),
            'base_dir' => $asset_data['dist_path'],
            'base_uri' => $asset_data['dist_uri'],
            'id'       => $namespace,
            'manifest' => 'assets.php',
            'priority' => $asset_data['priority'] ?? 50,
            'version'  => $asset_data['version'] ?? '0.0.0-dev',
        );
    }

    /**
     * Remaps the assets array to the new format
     *
     * @param  array $assets Assets to remap.
     * @return array
     */
    protected function remap_assets( array $assets ): array {
        return \array_map( static fn( $a ) => \array_merge( ...\array_values( $a ) ), $assets );
    }

    /**
     * Loads the legacy bundles
     *
     * @param  array $bundles Bundles to load.
     * @return array
     */
    public function load_legacy_bundles( array $bundles ): array {
        if ( ! \doing_filter( 'xwp_dependency_bundles' ) ) {
            return $bundles;
        }

        $legacy_bundles = $this->namespaces;
        unset( $this->namespaces );

        return \array_merge( $bundles, $legacy_bundles );
    }
}
