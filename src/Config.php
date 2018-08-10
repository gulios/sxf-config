<?php
declare(strict_types=1);

namespace SXF\Config;

use Illuminate\Config\Repository;
use Symfony\Component\Finder\Finder;
use Dotenv\Loader;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ApplicationConfig
 * @package SXF\Configs
 */
class Config extends Repository
{
    /**
     * @var string
     */
    public $configPathDepthLevel = '< 6';

    /**
     * @var
     */
    protected $configPath;

    /**
     * @var string
     */
    protected $envFile;

    /**
     * @var string
     */
    private $cacheFile;

    /**
     * @var string
     */
    private $configFilesPath;

    /**
     * @param string $configFilesPath
     */
    public function setConfigFilesPath(string $configFilesPath) : void
    {
        $this->configFilesPath = $configFilesPath;
    }

    /**
     * @return string
     */
    public function getConfigFilesPath()
    {
        return $this->configFilesPath;
    }

    public function setEnvFile(string $envFile) : void
    {
        $this->envFile = $envFile;
    }

    /**
     * @return string
     */
    public function getEnvFile()
    {
        return $this->envFile;
    }

    public function setCacheConfigFile(string $cacheConfigFile) : void
    {
        $this->cacheFile = $cacheConfigFile;
    }

    /**
     * @return string
     */
    public function getCacheConfigFile()
    {
        return $this->cacheFile;
    }

    /**
     * @return array
     */
    private function returnConfigFromCache()
    {
        /** @noinspection PhpIncludeInspection */
        return include $this->cacheFile;
    }

    /**
     * @return array
     */
    private function loadEnv()
    {
        $loader = new Loader($this->envFile, true);
        $loader->load();

        return array_merge($_ENV, $_SERVER);
    }

    /**
     * @return array
     */
    public function getAll() : array
    {
        $fileSystem = new Filesystem();

        if ($fileSystem->exists($this->cacheFile)) {

            return $this->returnConfigFromCache();

        } else {

            $this->loadEnv();
            $this->loadConfigFiles();
            return $this->all();
        }
    }

    /**
     * @param array|string $key
     * @param null $default
     * @return array|mixed
     */
    public function get($key, $default = null)
    {
        $all = $this->getAll();

        if (is_null($key)) {
            return $all;
        }

        if (isset($all[$key])) {
            return $all[$key];
        }

        foreach (explode('.', $key) as $segment)
        {
            if ( !is_array($all) || !array_key_exists($segment, $all))
            {
                return $all;
            }

            $all = $all[$segment];
        }

        return $all;
    }

    /**
     *
     */
    private function loadConfigFiles()
    {
        foreach ($this->getConfigFiles() as $fileKey => $path) {
            /** @noinspection PhpIncludeInspection */
            $this->set($fileKey, require $path);
        }
    }

    /**
     * @return array
     */
    private function getConfigFiles()
    {
        if (!is_dir($this->configFilesPath)) {
            return [];
        }

        $files = [];
        $phpFiles = Finder::create()->files()->name('*.php')->in($this->configFilesPath)->depth($this->configPathDepthLevel);

        foreach ($phpFiles as $file) {
            /** @noinspection PhpUndefinedMethodInspection */
            $files[basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }

    /**
     *
     */
    public function createCache() : void
    {
        $fileSystem = new Filesystem();
        $fileSystem->dumpFile($this->cacheFile, '<?php return ' . var_export($this->getAll(), true) . ';' . PHP_EOL);
    }
}
