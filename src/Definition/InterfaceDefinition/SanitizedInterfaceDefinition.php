<?php

namespace Kampaw\Dic\Definition\InterfaceDefinition;

use Kampaw\Dic\Definition\ClassDefinition\AbstractClassDefinition;
use Kampaw\Dic\Definition\ClassDefinition\SanitizedClassDefinition;
use Kampaw\Dic\Exception\BadMethodCallException;
use Kampaw\Dic\Exception\DomainException;
use Kampaw\Dic\Exception\InvalidArgumentException;

class SanitizedInterfaceDefinition extends AbstractInterfaceDefinition
{
    /**
     * @param string $interface
     */
    protected function setInterface($interface)
    {
        if (!is_string($interface)) {
            throw new InvalidArgumentException('Interface must be a string');
        }

        if (!interface_exists($interface)) {
            throw new DomainException("Interface $interface not exists");
        }

        $this->interface = $interface;
    }

    /**
     * @param array $candidates
     */
    protected function setCandidates(array $candidates)
    {
        if (empty($candidates)) {
            throw new BadMethodCallException('At least one candidate needs to be supplied');
        }

        parent::setCandidates($candidates);
    }

    /**
     * @param $candidate
     */
    protected function addCandidate($candidate)
    {
        $this->candidates[] = $this->sanitizeCandidate($candidate);
    }

    /**
     * @param AbstractClassDefinition $candidate
     * @return SanitizedClassDefinition
     */
    protected function sanitizeCandidate(AbstractClassDefinition $candidate)
    {
        $class = $candidate->getClass();

        if (!is_a($class, $this->interface, true)) {
            throw new InvalidArgumentException("$class not implements {$this->interface}");
        }

        return new SanitizedClassDefinition($class, $candidate->getParameters(), $candidate->getMutators());
    }
}