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

    public function __construct($configFile, $visitDomain = '@')
    {
        $this->config = new \stdClass;

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

        // Set default-values
        $this->addValues($config['@']);

        // If an exact match exists, overwrite all configs to make only exact one exist
        if (isset($config[$visitDomain])) {
            $config = [
                $visitDomain => $config[$visitDomain]
            ];
        }

        // Fuzzy match domain name and apply configs
        foreach ($config as $domain => $contents) {
            if (fnmatch($domain, $visitDomain)) {
                $this->addValues($contents);
            }
        }

        // Convert all sub-arrays of config to objects
        $this->config = json_decode(json_encode($this->config));
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
        $keys = explode('.', $key);

        // If we're going multidimenional...
        if (count($keys) > 1) {
            $key = $keys[0];

            $value = $this->createRecursiveArray($keys, $value)[$key];
        }

        // And if we're going to merge arrays
        if (is_array($value) && isset($this->config->$key)) {
            $value = array_merge($this->config->$key, $value);
        }

        $this->config->$key = $value;
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

    /**
     * Creates a recursive array based on $keys and it's $value
     *
     * For example:
     * $keys = [ 'one', 'two' ];
     * $value = 'value';
     *
     * Result:
     * [ 'one' => [ 'two' => 'value' ] ]
     */
    private function createRecursiveArray($keys, $value)
    {
        if (count($keys) === 1) {
            $array = [
                array_shift($keys) => $value
            ];
        }

        if (count($keys) >= 1) {
            $array = [
                array_shift($keys) => $this->createRecursiveArray($keys, $value)
            ];
        }

        return $array;
    }
}
