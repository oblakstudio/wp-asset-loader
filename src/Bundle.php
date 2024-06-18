<?php
/**
 * Bundle class file.
 *
 * @package eXtended WordPress
 * @subpackage Dependency Loader
 */

namespace XWP\Dependency;

use XWP\Dependency\Resources\Asset;
use XWP\Dependency\Resources\File;
use XWP\Dependency\Resources\Font;
use XWP\Dependency\Resources\Image;
use XWP\Dependency\Resources\Script;
use XWP\Dependency\Resources\Style;
use XWP\Dependency\Utils\Manifest;
use XWP\Helper\Traits\Array_Access;

/**
 * Bundle class.
 *
 * @template TKey of string
 * @template TValue of Asset|Image|Font
 * @implements \ArrayAccess<TKey, TValue>
 * @implements \Iterator<TKey, TValue>
 * */
class Bundle implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable {
    use Array_Access;

    /**
     * Asset ids grouped by context
     *
     * @var array<string, array<string>>
     */
    protected array $grouped = array();

    /**
     * Class constructor
     *
     * @param string  $id       Bundle ID.
     * @param ?string $version  Bundle version.
     * @param int     $priority Bundle priority.
     * @param string  $base_dir Base directory for assets.
     * @param string  $base_uri Base URI for assets.
     * @param array   $assets   Array of assets to load.
     * @param string  $manifest Manifest file.
     */
    public function __construct(
        protected string $id,
        protected string $version,
        protected int $priority,
        protected string $base_dir,
        protected string $base_uri,
        array $assets,
        string $manifest,
    ) {
        $this->load_assets( $assets, Manifest::load( $base_dir, $manifest, $id, $version ) );
    }

    /**
     * Loads the assets
     *
     * @param  array $assets Array of assets to load.
     * @param  array $manifest Asset manifest.
     */
    protected function load_assets( array $assets, array $manifest ) {
        $this->arr_data      = \array_merge(
            ...\array_map(
                fn( $ast, $ctx ) => $this->load_asset_group( $ast, $ctx, $manifest ),
                $assets,
                \array_keys( $assets ),
            ),
        );
        $this->arr_data_keys = \array_keys( $this->arr_data );

        $manifest = \array_diff_key( $manifest, $this->arr_data );

        foreach ( $manifest as $id => $src ) {
            $this->arr_data[ $id ] = $this->load_file( $src );
            $this->arr_data_keys[] = $id;
        }
    }

    /**
     * Parses an asset file
     *
     * @param  array  $deps Asset group to parse.
     * @param  string $ctx  Context for the asset.
     * @param  array  $map  Asset map.
     * @return array
     */
    protected function load_asset_group( array $deps, string $ctx, array $map ): array {
        $parsed = array();
        foreach ( $deps as $dep ) {
            if ( ! \is_array( $dep ) ) {
                $dep = array( 'src' => $dep );
            }
            $id  = $dep['src'];
            $src = $map[ $id ] ?? $id;
            $ext = \pathinfo( $src, \PATHINFO_EXTENSION );

            $dep = \wp_parse_args(
                \array_merge( $dep, \compact( 'src' ) ),
                array(
                    'ctx'  => $ctx,
					'deps' => array(),
					'mode' => 'auto',
				),
            );

            $parsed[ $id ] = $this->load_asset( $dep );

            $this->grouped[ $ctx ][] = $id;
        }

        return $parsed;
    }

    /**
     * Loads an asset
     *
     * @param  array $asset Asset data.
     * @return Asset
     */
    protected function load_asset( array $asset ): Asset {
        $ext = \pathinfo( $asset['src'], \PATHINFO_EXTENSION );

        $cname = match ( $ext ) {
            'js'    => Script::class,
            'css'   => Style::class,
            default => Script::class,
        };

        return new $cname( $this, ...$asset );
    }

    /**
     * Loads a file
     *
     * @param  string $src File source.
     * @return File
     */
    protected function load_file( string $src ): File {
        $ext = \pathinfo( $src, \PATHINFO_EXTENSION );

        $cname = match ( $ext ) {
            'jpg', 'png', 'gif',
            'ico', 'svg', 'jpeg',
            'webp', 'avif', 'apng' => Image::class,
            'ttf', 'woff', 'woff2' => Font::class,
            default => Image::class,
        };

        return new $cname( $this, $src );
    }

    /**
     * Get the bundle ID
     *
     * @return string
     */
    public function id(): string {
        return $this->id;
    }

    /**
     * Get the base directory
     *
     * @return string
     */
    public function base_dir(): string {
        return $this->base_dir;
    }

    /**
     * Get the base URI
     *
     * @return string
     */
    public function base_uri(): string {
        return $this->base_uri;
    }

    /**
     * Get the bundle version
     *
     * @return string
     */
    public function version(): string {
        return $this->version;
    }

    /**
     * Get the bundle priority
     *
     * @return int
     */
    public function priority(): int {
        return $this->priority;
    }

    /**
     * Get the context dependencies
     *
     * @param  string $ctx Context to get dependencies for.
     * @return array
     */
    public function get_context_deps( string $ctx ): array {
        return $this->grouped[ $ctx ] ?? array();
    }
}
