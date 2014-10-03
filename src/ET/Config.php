<?php
/**
 * @license ISC License <http://opensource.org/licenses/ISC>
 * @author Elis Axelsson <http://elis.nu/>
 * @since 2014
 */
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
        $this->config = (object) [];
        $this->addValues($config['@']);
        unset($config['@']);

        //Exact matching of domain name will have priority and ignore all fuzzy matching
        if (isset($config[$visitDomain])) {
            $this->addValues($config[$visitDomain]);
        } else {
            // Fuzzy match domain name with wildcards and run to the end of the array
            foreach ($config as $domain => $contents) {
                if (fnmatch($domain, $visitDomain)) {
                    $this->addValues($contents);
                }
            }
        }

        // Convert all sub-arrays to objects
        foreach ($this->config as $key => $value) {
            if (is_array($value)) {
                $this->config->$key = (object) $value;
            }
        }
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
     */
    private function addValue($key, $value)
    {
        if (is_array($value) && isset($this->config->$key)) {
            $this->config->$key = array_merge($this->config->$key, $value);
        } else {
            $this->config->$key = $value;
        }
    }

    /**
     * Loop through an array and add contents to the configruation.
     */
    private function addValues($data)
    {
        foreach ($data as $key => $value) {
            $this->addValue($key, $value);
        }
    }
}
