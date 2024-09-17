<?php
/**
 * Loader class file.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

namespace XWP\Dependency;

/**
 * Dependency loader class.
 *
 * @deprecated 5.0.0 Use \XWP_Asset_Loader instead.
 */
class Loader {
    /**
     * Returns the singleton instance of the loader.
     *
     * @return \XWP_Asset_Loader
     */
    public static function instance(): \XWP_Asset_Loader {
        return \XWP_Asset_Loader::instance();
    }
}
