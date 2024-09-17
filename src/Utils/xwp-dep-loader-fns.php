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
    $config['id'] = $id;

    XWP_Asset_Loader::load_bundle( $config );
}
