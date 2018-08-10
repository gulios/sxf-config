<?php
declare(strict_types=1);

namespace SXF\Config\Command;

use SXF\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConfigCacheClearCommand
 * @package SXF\Config\Command
 */
class ConfigCacheClearCommand extends Command
{
    /**
     * @var string
     */
    private $configPath;

    /**
     * @var string
     */
    private $envFile;

    /**
     * @var string
     */
    private $cacheFile;

    /**
     * ConfigCacheClearCommand constructor.
     * @param string $configPath
     * @param string $envFile
     * @param string $cacheFile
     */
    public function __construct(string $configPath, string $envFile, string $cacheFile)
    {
        $this->configPath = $configPath;
        $this->envFile = $envFile;
        $this->cacheFile = $cacheFile;

        parent::__construct($this->configPath, $this->envFile, $this->cacheFile);
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName("config:clear")
            ->setDescription("Remove the configuration cache file");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool|int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = new Config();
        $config->setConfigFilesPath($this->configPath);
        $config->setEnvFile($this->envFile);
        $config->setCacheConfigFile($this->cacheFile);
        $config->clearCache();

        $output->writeln('<info>Configuration cache cleared! ' . $this->cacheFile . '</info>');
        return true;
    }
}
