<?php

namespace Oblak\Asset;

class Loader implements LoaderInterface {
    /**
     * Hook we're using to load assets
     *
     * @var string|null
     */
    private static $hook = null;

    /**
     * Asset context
     *
     * @var null|string
     */
    private static $context = null;

    /**
     * Loader instance
     *
     * @var null|Loader
     */
    private static $instance = null;

    /**
     * Array of registered namespaces
     *
     * @var array
     */
    private $namespaces;

    public function __construct() {
        self::$hook    = (!is_admin()) ? 'wp_enqueue_scripts' : 'admin_enqueue_scripts';
        self::$context = (!is_admin()) ? 'front' : 'admin';

        $this->namespaces = [];

        add_action(self::$hook, [$this, 'run'], -1);
    }

    /**
     * Gets the singleton instance
     * @return Loader Singleton instance
     */
    public static function getInstance() {
        return (self::$instance === null)
            ? self::$instance = new Loader()
            : self::$instance;
    }

    /**
     * Registers a namespace to load assets for
     * @param  string $namespace
     * @param  array $data
     * @return void
     */
    public function registerNamespace($namespace, $data) {
        $this->namespaces[$namespace] = [
            'assets'   => $data['assets'],
            'version'  => $data['version']  ?? '1.0.0',
            'priority' => $data['priority'] ?? 50,
            'manifest' => new Manifest(
                $data['dist_path'].'/assets.json',
                $data['dist_uri'],
                $data['dist_path']
            ),
        ];
    }

    public function run() {
        foreach ($this->namespaces as $namespace => $data) {
            add_action(self::$hook, function () use ($namespace, $data) {
                $this->loadStyles(
                    $namespace,
                    $data['manifest'],
                    $data['assets'][self::$context]['styles'],
                    $data['version']
                );

                $this->loadScripts(
                    $namespace,
                    $data['manifest'],
                    $data['assets'][self::$context]['scripts'],
                    $data['version']
                );
            }, $data['priority']);
        }
    }

    public function loadStyles($namespace, $manifest, $assets, $version) {
        $load_styles = apply_filters("{$namespace}/load_styles", true);

        if (!$load_styles) {
            return;
        }

        foreach ($assets as $style) {
            $basename = basename($style);
            $handler  = "{$namespace}/{$basename}";

            if (!apply_filters("{$namespace}/enqueue/{$basename}", true)) {
                continue;
            }

            wp_register_style($handler, $manifest->getUri($style), [], $version);
            wp_enqueue_style($handler);
        }
    }

    public function loadScripts($namespace, $manifest, $assets, $version) {
        $load_scripts = apply_filters("{$namespace}/load_scripts", true);

        if (!$load_scripts) {
            return;
        }

        foreach ($assets as $script) {
            $basename = basename($script);
            $handler  = "{$namespace}/{$basename}";

            if (!apply_filters("{$namespace}/enqueue/{$basename}", true)) {
                continue;
            }

            wp_register_script($handler, $manifest->getUri($script), [], $version, true);
            do_action("{$namespace}/localize/$basename");
            wp_enqueue_script($handler);
        }
    }

    public function getUri($namespace, $asset) {
        return $this->namespaces[$namespace]['manifest']->getUri($asset);
    }

    public function getPath($namespace, $asset) {
        return $this->namespaces[$namespace]['manifest']->getPath($asset);
    }
}
