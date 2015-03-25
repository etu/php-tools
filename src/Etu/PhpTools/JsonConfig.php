<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2015
 */
namespace Etu\PhpTools;

class JsonConfig implements Config
{
    private $config;
    private $tmpConfig;
    private $modules = null;

    public function __construct($configFile, $env = '@')
    {
        $this->config = new \stdClass;

        if (!file_exists($configFile)) {
            throw new ConfigException('Configruation file not found: '.$configFile);
        }

        $config = json_decode(file_get_contents($configFile));

        if ($config === null) {
            $msg = 'Configruation file failed to parse: '.$configFile.', with error: '.json_last_error_msg();

            throw new ConfigException($msg);
        }

        if (!isset($config->{'@'})) {
            throw new ConfigException('Missing "@" section in parsed config: '.$configFile);
        }

        // If modules is defined, copy to own variable
        if (isset($config->__modules__)) {
            $this->modules = $config->__modules__;
        }

        // Att default values to config
        $this->addValue('tmpConfig', $config->{'@'}, $this);

        if (isset($config->$env)) {
            $config = [
                $env => $config->$env
            ];
        }

        foreach ($config as $key => $value) {
            if (fnmatch($key, $env)) {
                $this->addValue('tmpConfig', $value, $this);
            }
        }

        $this->loadConfigModules();

        $this->addValue('config', $this->tmpConfig, $this);

        unset($this->tmpConfig);
    }

    /**
     * Get values from the configruation.
     */
    public function __get($key)
    {
        return $this->config->$key;
    }

    /**
     * Add a single value to the configruation, might be an array.
     *
     * @param $key string Key to put $value into
     * @param $value (string|object) Value to put in
     * @param $config Reference to object to put the above into
     */
    public function addValue($key, $value, &$config)
    {
        switch (true) {
            case (is_object($value)):
                if (!isset($config->$key)) {
                    $config->$key = new \stdClass;
                }

                foreach ($value as $key2 => $value2) {
                    $this->addValue($key2, $value2, $config->$key);
                }
                break;

            default:
                $config->$key = $value;
        }
    }

    /**
     * Load Modules
     */
    private function loadConfigModules()
    {
        if (isset($this->tmpConfig->__loaded_modules__)) {
            foreach ($this->tmpConfig->__loaded_modules__ as $module) {
                $this->addValue('config', $this->modules->$module, $this);
            }
        }
    }
}
