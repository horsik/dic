<?php

namespace Kampaw\Dic\Definition;

interface DefinitionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getConcrete();

    /**
     * @return string
     */
    public function getAbstract();

    /**
     * @return Parameter[]
     */
    public function getParameters();

    /**
     * @return Mutator[]
     */
    public function getMutators();

    /**
     * @return string
     */
    public function getLifetime();

    /**
     * @return string
     */
    public function getAutowire();

    /**
     * @return boolean
     */
    public function isCandidate();
}