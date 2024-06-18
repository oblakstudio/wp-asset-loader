<?php
/**
 * Loader class file.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

namespace XWP\Dependency;

use XWP\Helper\Traits\Singleton;

/**
 * Dependency loader class.
 */
class Loader {
    use Singleton;

    /**
     * Current execution context
     *
     * @var string
     */
    protected string $context;

    /**
     * Hook we're using to load assets
     *
     * @var 'wp_enqueue_scripts'|'admin_enqueue_scripts'
     */
    protected string $target;

    /**
     * Array of registered bundles
     *
     * @var array<string, Bundle>
     */
    protected array $bundles;

    /**
     * Class constructor
     */
    protected function __construct() {
        $this->target  = ! \is_admin() ? 'wp_enqueue_scripts' : 'admin_enqueue_scripts';
        $this->context = ! \is_admin() ? 'front' : 'admin';

        \add_action( 'init', array( $this, 'load_bundles' ), 1000, 0 );
        \add_action( $this->target, array( $this, 'run' ), -1 );
    }

    /**
     * Loads the bundles
     */
    public function load_bundles() {
        $bundles = \apply_filters( 'xwp_dependency_bundles', array(), $this->context );
        $bundles = \array_map( array( $this, 'load_bundle' ), $bundles );
        $bundles = \array_filter( $bundles );

        $this->bundles = $bundles;
    }

    /**
     * Loads a bundle
     *
     * @param  array|Bundle $config Bundle configuration.
     * @return Bundle|null
     */
    protected function load_bundle( array|Bundle $config ): ?Bundle {
        if ( $config instanceof Bundle ) {
            return $config;
        }

        if ( ! $config['assets'] ) {
            return null;
        }

        $config = \wp_parse_args(
            $config,
            array(
                'manifest' => false,
                'priority' => 50,
                'version'  => '0.0.0-dev',
            ),
        );

        return new Bundle( ...$config );
    }

    /**
     * Runs the loader on the target hook.
     */
    public function run() {
        foreach ( $this->bundles as $bundle ) {
            $this->enqueue( $bundle, $bundle->get_context_deps( $this->context ) );
        }
    }

    /**
     * Enqueues a bundle assets.
     *
     * @param Bundle $bundle Bundle to enqueue.
     * @param array  $deps   Dependencies to enqueue.
     */
    public function enqueue( Bundle $bundle, array $deps ) {
        foreach ( $deps as $dep ) {
            \add_action(
                $this->target,
                static fn() => $bundle[ $dep ]->process( 'auto' ),
                $bundle->priority(),
                0,
            );
        }
    }

    /**
     * Get a bundle by ID
     *
     * @param  string $id Bundle ID.
     * @return Bundle|null
     */
    public function get_bundle( string $id ): ?Bundle {
        return $this->bundles[ $id ] ?? null;
    }
}
