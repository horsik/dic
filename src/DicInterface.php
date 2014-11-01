<?php
namespace Kampaw\Dic;


/**
 * Class Dic
 *
 * @package Kampaw\Dic
 */
interface DicInterface
{
    /**
     * @param $class
     * @param $interface
     * @param bool $shared
     * @param null $instance
     * @return bool
     */
    public function register($class, $interface, $shared = true, $instance = null);

    /**
     * @param string $interface
     * @param string $as
     */
    public function registerAlias($interface, $as);

    /**
     * @param string $alias
     */
    public function unregisterAlias($alias);

    /**
     * @param string $interface
     * @return array
     */
    public function listClasses($interface);

    /**
     * @param string $interface
     * @return string|null
     */
    public function getCandidate($interface);

    /**
     * @param $interface
     * @return object
     */
    public function resolveInterface($interface);

    /**
     * @param $class
     * @return object
     */
    public function resolveClass($class);

    /**
     * @param object $object
     * @return object
     */
    public function injectDependencies($object);

    /**
     * @param array $config
     */
    public function setConfig($config);
}