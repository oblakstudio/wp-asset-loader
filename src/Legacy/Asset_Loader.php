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
 *
 * @mixin \XWP_Asset_Loader
 * @deprecated 5.0.0 Use \XWP_Asset_Loader instead.
 */
final class Asset_Loader {
    use Singleton;

    /**
     * Gets the singleton instance
     *
     * @return self
     */
    public static function get_instance(): self {
        return self::instance();
    }

    /**
     * Magic method to call methods on the asset loader
     *
     * @param  string $name Method name.
     * @param  array  $args Method arguments.
     * @return mixed
     */
    public function __call( string $name, array $args = array() ): mixed {
        return \XWP_Asset_Loader::instance()->$name( ...$args );
    }

    /**
     * Registers a namespace to load assets for
     *
     * @param  string $namespace  Namespace to register.
     * @param  array  $args Asset data.
     * @return void
     */
    public function register_namespace( $namespace, $args ) {
        \XWP_Asset_Loader::load_bundle(
            array(
				'assets'   => $this->remap_assets( $args['assets'] ),
				'base_dir' => $args['base_dir'] ?? $args['dist_path'] ?? '',
				'base_uri' => $args['base_uri'] ?? $args['dist_uri'] ?? '',
				'id'       => $namespace,
				'manifest' => 'assets.php',
				'priority' => $args['priority'] ?? 50,
				'version'  => $args['version'] ?? '0.0.0-dev',
            ),
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
}
