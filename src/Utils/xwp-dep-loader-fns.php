<?php
/**
 * Utility functions for the dependency loader.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

/**
 * Add a dependency bundle.
 *
 * @param string $id     Bundle ID.
 * @param array  $config Bundle configuration.
 */
function xwp_add_dependency_bundle( string $id, array $config ): void {
    add_filter(
        'xwp_dependency_bundles',
        static function ( array $bundles ) use ( $id, $config ): array {
			$bundles[ $id ] = $config;
			return $bundles;
		},
        10,
        1,
    );
}
