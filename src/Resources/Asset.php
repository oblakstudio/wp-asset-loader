<?php
/**
 * Asset class file.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

namespace XWP\Dependency\Resources;

use XWP\Dependency\Bundle;

/**
 * Base enqueueable asset resource.
 */
abstract class Asset extends File {
    /**
     * Asset constructor.
     *
     * @param Bundle     $bundle The bundle object.
     * @param string     $src The source of the asset.
     * @param string     $ctx The context of the asset.
     * @param string     $mode The mode of the asset.
     * @param array      $deps The dependencies of the asset.
     * @param array|null $args The arguments of the asset.
     */
    public function __construct(
        Bundle &$bundle,
        string $src,
        protected string $ctx,
        protected string $mode,
        protected array $deps,
        protected ?array $args = null,
    ) {
        parent::__construct( $bundle, $src );
    }

    /**
     * Get the file name
     *
     * @return string
     */
    public function name(): string {
        return $this->name;
    }

    /**
     * Get the context of the asset.
     *
     * @return string The context of the asset.
     */
    public function ctx(): string {
        return $this->ctx;
    }

    /**
     * Get the version of the asset.
     *
     * @return string The version of the asset.
     */
    public function version(): string {
        return $this->bundle->version();
    }

    /**
     * Get the dependencies of the asset.
     *
     * @return array The dependencies of the asset.
     */
    public function deps(): array {
        return $this->deps;
    }

    /**
     * Get the handle of the asset.
     *
     * @return string The handle of the asset.
     */
    public function handle(): string {
        return $this->bundle->id() . '-' . $this->name();
    }

    /**
     * Get the arguments of the asset.
     *
     * @return array The arguments of the asset.
     */
    public function args(): array {
        return $this->args ?? $this->default_args();
    }

    /**
     * Process the asset.
     *
     * @param  string $mode The mode of the asset.
     * @return bool   True if the asset is processed successfully, false otherwise.
     */
    public function process( string $mode = 'auto' ): bool {
        /**
         * Should we register this asset type?
         *
         * @param bool $load_styles Whether to load styles.
         *
         * @since 2.0.0
         */
        if ( ! \apply_filters( "{$this->bundle->id()}_load_{$this->type()}s", true ) ) {
            return false;
        }

        return $this->register() && $this->enqueue( $mode );
    }

    /**
     * Register the asset.
     *
     * @return bool True if the asset is registered successfully, false otherwise.
     */
    public function register(): bool {
        /**
         * Short-cuts the loading of a specific style.
         *
         * @param bool   $load_stype Whether to load the style.
         * @param string $basename   Style basename.
         *
         * @since 2.0.0
         */
        if ( ! \apply_filters( "{$this->bundle->id()}_load_{$this->type()}", true, $this->name() ) ) {
            return false;
        }

        return $this->callback( 'register', $this->cb_args() );
    }

    /**
     * Enqueue the asset.
     *
     * @param string $mode The mode of the asset.
     * @return bool True if the asset is enqueued successfully, false otherwise.
     */
    public function enqueue( string $mode = 'auto' ): bool {
        if ( $mode !== $this->mode ) {
            return false;
        }

        return $this->callback( 'enqueue', array( 'handle' => $this->handle() ) );
    }

    /**
     * Call the callback function for the given action and arguments.
     *
     * @param string $action The action to perform.
     * @param array  $args The arguments for the callback function.
     * @return bool True if the callback function is called successfully, false otherwise.
     */
    protected function callback( string $action, array $args ): bool {
        $callback = "wp_{$action}_{$this->type()}";

        return $callback( ...$args ) ?? true;
    }

    /**
     * Get the callback arguments for registering or enqueuing the asset.
     *
     * @return array The callback arguments.
     */
    protected function cb_args(): array {
        return \array_merge(
            array(
                'deps'   => $this->deps(),
                'handle' => $this->handle(),
                'src'    => $this->uri(),
                'ver'    => $this->version(),
            ),
            $this->args(),
        );
    }

    /**
     * Get the type of asset.
     *
     * @return string The type of asset. Possible values are 'script' or 'style'.
     */
    abstract protected function type(): string;

    /**
     * Get the default enqueue/register arguments for the asset.
     *
     * @return array The default arguments for enqueue/register.
     */
    abstract protected function default_args(): array;
}
