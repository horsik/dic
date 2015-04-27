<?php

namespace Kampaw\Dic\Config;

abstract class Configurable
{
    /**
     * @var array $config
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->merge($config);
    }

    /**
     * @param array $config
     */
    protected function merge(array $config)
    {
        $this->config = $config;

        foreach ($config as $key => $value) {
            $mutator = 'set' . ucfirst($key);

            if ($key === 'config') {
                // @todo(kampaw) reserved word, ffs please make tests for this class
                continue;
            }

            if (method_exists($this, $mutator)) {
                $this->{$mutator}($value);
            }
            elseif (array_key_exists($key, get_object_vars($this))) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getKey($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }
    }
}