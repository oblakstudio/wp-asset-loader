<?php
/**
 * Asset_Loader class file.
 *
 * @package Asset Loader
 */

namespace Oblak\WP;

/**
 * Handles the complete asset loading process.
 */
class Asset_Loader {
    /**
     * Hook we're using to load assets
     *
     * @var string|null
     */
    private static $hook = null;

    /**
     * Asset context
     *
     * @var null|string
     */
    private static $context = null;

    /**
     * Loader instance
     *
     * @var null|Loader
     */
    private static $instance = null;

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
        self::$hook    = ( ! is_admin() ) ? 'wp_enqueue_scripts' : 'admin_enqueue_scripts';
        self::$context = ( ! is_admin() ) ? 'front' : 'admin';

        $this->namespaces = array();

        add_action( self::$hook, array( $this, 'run' ), -1 );
    }

    /**
     * Gets the singleton instance
     *
     * @return Asset_Loader
     */
    public static function get_instance() {
        return self::$instance ??= new Asset_Loader(); //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
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
            'assets'   => $asset_data['assets'],
            'version'  => $asset_data['version'] ?? '1.0.0',
            'priority' => $asset_data['priority'] ?? 50,
            'manifest' => new Asset_Manifest(
                $asset_data['manifest'] ?? $asset_data['dist_path'] . '/assets.json',
                $asset_data['dist_uri'],
                $asset_data['dist_path']
            ),
        );
    }

    /**
     * Runs the asset loader for all the registered namespaces
     */
    public function run() {
        foreach ( $this->namespaces as $namespace => $data ) {
            foreach ( array( 'styles', 'scripts' ) as $asset_type ) {
                ! empty( $data['assets'][ self::$context ][ $asset_type ] ) &&
                add_action(
                    self::$hook,
                    fn() => $this->enqueue_assets( $asset_type, $namespace, $data ),
                    $data['priority']
                );
            }
        }
    }

    /**
     * Enqueues the assets for a namespace
     *
     * @param  string $type      Asset type. Can be 'styles' or 'scripts'.
     * @param  string $namespace Namespace to enqueue assets for.
     * @param  array  $data      Namespace data.
     */
    public function enqueue_assets( string $type, string $namespace, array $data ) {
        $singular_type = rtrim( $type, 's' );
        $register      = "wp_register_{$singular_type}";
        $enqueue       = "wp_enqueue_{$singular_type}";

        /**
         * Should we load styles for this namespace?
         *
         * @param bool $load_styles Whether to load styles.
         *
         * @since 2.0.0
         */
        if ( ! apply_filters( "{$namespace}_load_{$type}", true ) ) {
            return;
        }

        foreach ( $data['assets'][ self::$context ][ $type ] ?? array() as $asset ) {
            $basename = strtr(
                basename( $asset ),
                array(
					'.css' => '',
					'.js'  => '',
                )
            );
            $handler  = "{$namespace}-{$basename}";

            /**
             * Short-cuts the loading of a specific style.
             *
             * @param bool   $load_stype Whether to load the style.
             * @param string $basename   Style basename.
             *
             * @since 2.0.0
             */
            if ( ! apply_filters( "{$namespace}_load_{$singular_type}", true, $basename ) ) {
                continue;
            }

            $register( $handler, $data['manifest']->get_uri( $asset ), array(), $data['version'] );
            $enqueue( $handler );
        }
    }

    /**
     * Get cache-busted asset URI
     *
     * @param  string $namespace Namespace to get asset URI for.
     * @param  string $asset     Asset to get URI for.
     * @return string
     */
    public function get_uri( string $namespace, string $asset ): string {
        return $this->namespaces[ $namespace ]['manifest']->get_uri( $asset );
    }

    /**
     * Get cache-busted asset path
     *
     * @param  string $namespace Namespace to get asset path for.
     * @param  string $asset     Asset to get path for.
     * @return string
     */
    public function get_path( string $namespace, string $asset ): string {
        return $this->namespaces[ $namespace ]['manifest']->get_path( $asset );
    }
}
