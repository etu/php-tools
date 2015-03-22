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

        // Att default values to config
        $this->addValue('config', $config->{'@'}, $this);

        if (isset($config->$env)) {
            $config = [
                $env => $config->$env
            ];
        }

        foreach ($config as $key => $value) {
            if (fnmatch($key, $env)) {
                $this->addValue('config', $value, $this);
            }
        }
    }

    public function __get($key)
    {
        return $this->config->$key;
    }

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
}
