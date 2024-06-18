<?php //phpcs:disable SlevomatCodingStandard.Classes.SuperfluousTraitNaming
/**
 * Loader_Trait class file.
 *
 * @package Asset Loader
 */

namespace Oblak\WP;

use XWP\Dependency\Retriever;

/**
 * Getters for asset path and URI.
 */
trait Loader_Trait {
    use Retriever;

    /**
     * Initializes the asset loader
     *
     * @param array       $args      Array of assets to load.
     * @param string|null $namespace Namespace for the assets. Defaults to null. Optional.
     */
    protected function init_asset_loader( array $args, ?string $namespace = null ): void {
        $this->bundle_id ??= $namespace ?? $args['namespace'] ?? \wp_generate_uuid4();
        \add_action(
            'init',
            fn() => Asset_Loader::instance()->register_namespace( $this->bundle_id, $args )
        );
    }
}
