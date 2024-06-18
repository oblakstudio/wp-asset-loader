<?php
/**
 * Style class file.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

namespace XWP\Dependency\Resources;

/**
 * Style resource.
 */
class Style extends Asset {
    /**
     * Get the asset type.
     *
     * @return string
     */
    protected function type(): string {
        return 'style';
    }

    /**
     * Get the media type of the style.
     *
     * @return string The media type of the style.
     */
    protected function default_args(): array {
        return array(
			'media' => 'all',
		);
    }
}
