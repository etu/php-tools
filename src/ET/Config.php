<?php
namespace ET;

class Config
{
    private $config;

    public function __construct($configFile, $visitDomain)
    {
        // Config not found at all
        if (!file_exists($configFile)) {
            throw new ConfigException('Configruation file not found: '.$configFile);
        }

        // Parse INI file
        $config = parse_ini_file($configFile, true);

        // Failed to parse, empty or no default domain specified
        if (!$config || !is_array($config['@'])) {
            throw new ConfigException('Configruation file failed to parse: '.$configFile);
        }

        // Set default-values and remove it from the list
        $this->config = (object) $config['@'];
        unset($config['@']);

        //Exact matching of domain name will have priority and ignore all fuzzy matching
        if (isset($config[$visitDomain])) {
            foreach ($config[$visitDomain] as $key => $value) {
                if (is_array($value)) {
                    $this->config->$key = (object) array_merge($this->config->$key, $value);
                } else {
                    $this->config->$key = $value;
                }
            }
        } else {
            // Fuzzy match domain name with wildcards and run to the end of the array
            foreach ($config as $domain => $contents) {
                if (fnmatch($domain, $visitDomain)) {
                    foreach ($contents as $key => $value) {
                        if (is_array($value)) {
                            $this->config->$key = (object) array_merge($this->config->$key, $value);
                        } else {
                            $this->config->$key = $value;
                        }
                    }
                }
            }
        }

        foreach ($this->config as $key => $value) {
            if (is_array($value)) {
                $this->config->$key = (object) $value;
            }
        }
    }

    public function dumpConfig()
    {
        return $this->config;
    }
}
