<?php
/**
 * Initialize the dependency loader.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

if ( defined( 'ABSPATH' ) ) {
    add_action( 'init', array( \XWP\Dependency\Loader::class, 'instance' ), 500, 0 );
}
