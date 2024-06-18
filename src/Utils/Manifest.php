<?php //phpcs:disable Universal.Operators.DisallowShortTernary.Found
/**
 * Manifest class file.
 *
 * @package eXtended WordPress
 */
namespace XWP\Dependency\Utils;

use Automattic\Jetpack\Constants;

/**
 * Helper class to load a manifest file.
 */
class Manifest {
    /**
     * Load a manifest file
     *
     * @param  string $dir  Directory where the manifest file is located.
     * @param  string $file Name of the manifest file.
     * @param  string $id   The ID of the manifest.
     * @param  string $ver  The version of the manifest.
     * @return array
     */
    public static function load( string $dir, string $file, string $id, string $ver ): array {
        if ( ! static::cache_enabled( $ver ) ) {
            return static::read_manifest_file( $dir, $file );
        }

        $data = static::get_manifest_cache( $id, $ver ) ?? static::read_manifest_file( $dir, $file );

        if ( $data ) {
			\set_transient( static::get_cache_key( $id, $ver ), $data, \HOUR_IN_SECONDS );
        }

        return $data;
    }

    /**
     * Get the cached manifest
     *
     * @param  string $id  The ID of the manifest.
     * @param  string $ver The version of the manifest.
     * @return array|null
     */
    public static function get_manifest_cache( string $id, string $ver ): ?array {
        return \get_transient( static::get_cache_key( $id, $ver ) ) ?: null;
    }

    /**
     * Get the cache key for a manifest
     *
     * @param  string $id  The ID of the manifest.
     * @param  string $ver The version of the manifest.
     * @return string
     */
    public static function get_cache_key( string $id, string $ver ): string {
        return "xwp_{$id}_assets_{$ver}";
    }

    /**
     * Check if cache is enabled
     *
     * @param  string $ver Version of the manifest.
     * @return bool
     */
    public static function cache_enabled( string $ver ): bool {
        return ! \str_starts_with( $ver, '0.0.0' ) && ! Constants::is_true( 'XWP_LOADER_DEBUG' );
    }

    /**
     * Try to read a manifest file
     *
     * @param  string $dir  Directory where the manifest file is located.
     * @param  string $file Name of the manifest file.
     * @return array
     *
     * @throws \Throwable If the manifest file cannot be read.
     */
    public static function read_manifest_file( string $dir, string $file ): array {
        $file = \trailingslashit( $dir ) . \pathinfo( $file, PATHINFO_FILENAME );

        try {
            return match ( true ) {
                \file_exists( "{$file}.php" )  => static::read_manifest_php( $file ),
                \file_exists( "{$file}.json" ) => static::read_manifest_json( $file ),
                default => array(),
            };
        } catch ( \Throwable ) {
            return array();
        }
    }

    /**
     * Read a PHP manifest file
     *
     * @param  string $file Path to the manifest file.
     * @return array
     */
    protected static function read_manifest_php( string $file ): array {
        return require "{$file}.php";
    }

    /**
     * Read a JSON manifest file
     *
     * @param  string $file Path to the manifest file.
     * @return array
     */
    protected static function read_manifest_json( string $file ): array {
        return \json_decode(
            \wp_load_filesystem()->get_contents( "{$file}.json" ),
            true,
            512,
            \JSON_THROW_ON_ERROR,
        );
    }
}
