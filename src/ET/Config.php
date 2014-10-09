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

    public function __construct($configFile, $env = '@')
    {
        $this->config = new \stdClass;

        if (!file_exists($configFile)) {
            throw new ConfigException('Configruation file not found: '.$configFile);
        }

        $config = parse_ini_file($configFile, true);

        if (!$config || !is_array($config['@'])) {
            throw new ConfigException('Configruation file failed to parse: '.$configFile);
        }

        $this->addValues($config['@']);

        if (isset($config[$env])) {
            $config = [
                $env => $config[$env]
            ];
        }

        foreach ($config as $key => $value) {
            if (fnmatch($key, $env)) {
                $this->addValues($value);
            }
        }

        $this->config = $this->recursiveArrayToObject($this->config);
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

        if (count($keys) > 1) {
            $key = $keys[0];

            $value = $this->createRecursiveArray($keys, $value)[$key];
        }

        if (is_array($value) && isset($this->config->$key)) {
            $value = array_replace_recursive($this->config->$key, $value);
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

    /**
     * Converts a multidimensional array to objects
     */
    private function recursiveArrayToObject($array)
    {
        return json_decode(json_encode($array));
    }
}
