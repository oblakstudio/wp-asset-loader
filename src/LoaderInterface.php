<?php
namespace Oblak\Asset;

interface LoaderInterface
{

    public function loadStyles(string $namespace, Manifest $manifest, array $assets, string $version);

    public function loadScripts(string $namespace, Manifest $manifest, array $assets, string $version);
    
    /**
     * Get the cache-busted URI
     *
     * If the manifest does not have an entry for $asset, then return URI for $asset
     *
     * @param string $asset The original name of the file before cache-busting
     * @return string
     */
    public function getUri(string $namespace, string $asset) : string;

    /**
     * Get the cache-busted path
     * 
     * If the manifest does not have an entry for $asset, then return URI for $asset
     *
     * @param  string $asset The original name of the file before cache-busting
     * @return string
     */
    public function getPath(string $namespace, string $asset) : string;

}