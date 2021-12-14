<?php

namespace Oblak\Asset;

interface LoaderInterface {
    /**
     * Loads styles
     *
     * @param  string $namespace
     * @param  Manifest $manifest
     * @param  array $assets
     * @param  string $version
     * @return mixed
     */
    public function loadStyles($namespace, $manifest, $assets, $version);

    /**
     * Loads scripts
     *
     * @param  string $namespace
     * @param  Manifest $manifest
     * @param  array $assets
     * @param  string $version
     * @return mixed
     */
    public function loadScripts($namespace, $manifest, $assets, $version);

    /**
     * Get the cache-busted URI
     *
     * If the manifest does not have an entry for $asset, then return URI for $asset
     *
     * @param  string $asset The original name of the file before cache-busting
     * @return string
     */
    public function getUri($namespace, $asset);

    /**
     * Get the cache-busted path
     *
     * If the manifest does not have an entry for $asset, then return URI for $asset
     *
     * @param  string $asset The original name of the file before cache-busting
     * @return string
     */
    public function getPath($namespace, $asset);
}
