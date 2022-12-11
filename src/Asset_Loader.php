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
    private $namespaces;

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
     * @return Loader
     */
    public static function get_instance() {
        return self::$instance ?? self::$instance = new Asset_Loader(); //phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
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
            add_action(
                self::$hook,
                function () use ( $namespace, $data ) {
                    $this->load_styles(
                        $namespace,
                        $data['manifest'],
                        $data['assets'][ self::$context ]['styles'],
                        $data['version']
                    );

                    $this->load_scripts(
                        $namespace,
                        $data['manifest'],
                        $data['assets'][ self::$context ]['scripts'],
                        $data['version']
                    );
                },
                $data['priority']
            );
        }
    }

    /**
     * Load styles for a namespace
     *
     * @param  string         $namespace Namespace to load styles for.
     * @param  Asset_Manifest $manifest  Asset manifest.
     * @param  string[]       $assets    Array of assets.
     * @param  string         $version   Version to use for assets.
     */
    public function load_styles( $namespace, $manifest, $assets, $version ) {

        /**
         * Should we load styles for this namespace?
         *
         * @param bool $load_styles Whether to load styles.
         *
         * @since 2.0.0
         */
        if ( ! apply_filters( "{$namespace}_load_styles", true ) ) {
            return;
        }

        foreach ( $assets as $style ) {

            $basename = str_replace( '.css', '', basename( $style ) );
            $handler  = "{$namespace}-{$basename}";

            /**
             * Short-cuts the loading of a specific style.
             *
             * @param bool   $load_stype Whether to load the style.
             * @param string $basename   Style basename.
             *
             * @since 2.0.0
             */
            if ( ! apply_filters( "{$namespace}_load_style", true, $basename ) ) {
                continue;
            }

            wp_register_style( $handler, $manifest->get_uri( $style ), array(), $version );
            wp_enqueue_style( $handler );
        }
    }

    /**
     * Load scripts for a namespace
     *
     * @param  string         $namespace Namespace to load styles for.
     * @param  Asset_Manifest $manifest  Asset manifest.
     * @param  string[]       $assets    Array of assets.
     * @param  string         $version   Version to use for assets.
     */
    public function load_scripts( $namespace, $manifest, $assets, $version ) {

        /**
         * Should we load scripts for this namespace?
         *
         * @param bool $load_scripts Whether to load styles.
         *
         * @since 2.0.0
         */
        if ( ! apply_filters( "{$namespace}_load_scripts", true ) ) {
            return;
        }

        foreach ( $assets as $script ) {

            $basename = str_replace( '.js', '', basename( $script ) );
            $handler  = "{$namespace}-{$basename}";

            /**
             * Short-cuts the loading of a specific script.
             *
             * @param bool   $load_stype Whether to load the script.
             * @param string $basename   Script basename.
             *
             * @since 2.0.0
             */
            if ( ! apply_filters( "{$namespace}_load_script", true, $basename ) ) {
                continue;
            }

            wp_register_script( $handler, $manifest->get_uri( $script ), array(), $version, true );

            /**
             * Localize the script
             *
             * @param string $basename Script basename.
             *
             * @since 2.0.0
             */
            do_action( "{$namespace}_localize_script", $basename );

            wp_enqueue_script( $handler );
        }
    }

    /**
     * Get cache-busted asset URI
     *
     * @param  string $namespace Namespace to get asset URI for.
     * @param  string $asset     Asset to get URI for.
     * @return string
     */
    public function get_uri( $namespace, $asset ) {
        return $this->namespaces[ $namespace ]['manifest']->get_uri( $asset );
    }

    /**
     * Get cache-busted asset path
     *
     * @param  string $namespace Namespace to get asset path for.
     * @param  string $asset     Asset to get path for.
     * @return string
     */
    public function get_path( $namespace, $asset ) {
        return $this->namespaces[ $namespace ]['manifest']->get_path( $asset );
    }
}
