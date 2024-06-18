<?php //phpcs:disable Squiz.Commenting.FunctionComment.Missing
/**
 * Script class file.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

namespace XWP\Dependency\Resources;

/**
 * Script enqueueable resource.
 */
class Script extends Asset {
    protected function type(): string {
        return 'script';
    }

    protected function default_args(): array {
        return array(
			'args' => array(
				'in_footer' => true,
			),
		);
    }

    public function register(): bool {
        if ( ! parent::register() ) {
            return false;
        }

        \do_action( "{$this->bundle->id()}_localize_{$this->type()}", $this->name(), $this );

        if ( ! \has_filter( "localize_params_{$this->handle()}" ) ) {
            return true;
        }

        $args = array(
            'handle'      => $this->handle(),
            'l10n'        => array(),
            'object_name' => $this->name(),
        );

        $args = \apply_filters( "localize_params_{$this->handle()}", $args, $this );

        \wp_localize_script( ...$args );

        return true;
    }
}
