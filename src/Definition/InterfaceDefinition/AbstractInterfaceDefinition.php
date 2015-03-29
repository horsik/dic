<?php

namespace Kampaw\Dic\Definition\InterfaceDefinition;

use Kampaw\Dic\Definition\ClassDefinition\AbstractClassDefinition;
use Kampaw\Dic\Definition\DefinitionInterface;
use Kampaw\Dic\Definition\Parameter\AbstractParameter;
use Kampaw\Dic\Exception\DomainException;
use Kampaw\Dic\Exception\InvalidArgumentException;
use Kampaw\Dic\Exception\OutOfBoundsException;

abstract class AbstractInterfaceDefinition implements DefinitionInterface
{
    /**
     * @var string $interface
     */
    protected $interface;

    /**
     * @var AbstractClassDefinition[] $candidates
     */
    protected $candidates = array();

    /**
     * @var string $candidate
     */
    protected $candidate = 0;

    /**
     * @param string $interface
     * @param AbstractClassDefinition[] $candidates
     */
    public function __construct($interface, array $candidates)
    {
        $this->setInterface($interface);
        $this->setCandidates($candidates);
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->getCandidate()->getClass();
    }

    /**
     * @return AbstractParameter[]
     */
    public function getParameters()
    {
        return $this->getCandidate()->getParameters();
    }

    /**
     * @return mixed
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * @param string $interface
     */
    protected abstract function setInterface($interface);

    /**
     * @return AbstractClassDefinition[]
     */
    public function getCandidates()
    {
        return $this->candidates;
    }

    /**
     * @param AbstractClassDefinition[] $candidates
     */
    protected function setCandidates(array $candidates)
    {
        foreach ($candidates as $candidate) {
            $this->addCandidate($candidate);
        }
    }

    /**
     * @param $candidate
     */
    protected abstract function addCandidate($candidate);

    /**
     * @return AbstractClassDefinition
     */
    public function getCandidate()
    {
        $candidates = $this->getCandidates();

        return $candidates[$this->candidate];
    }

    /**
     * @param int $index
     */
    public function setCandidateByIndex($index)
    {
        if (!is_int($index)) {
            throw new InvalidArgumentException('Index must be an integer');
        }

        if ($index >= 0 and $index < count($this->getCandidates())) {
            $this->candidate = $index;
        } else {
            throw new OutOfBoundsException('Invalid index supplied');
        }
    }

    /**
     * @param AbstractClassDefinition $definition
     */
    public function setCandidateByDefinition(AbstractClassDefinition $definition)
    {
        $index = array_search($definition, $this->getCandidates());

        if (is_int($index)) {
            $this->candidate = $index;
        } else {
            throw new DomainException('Requested candidate not exists');
        }
    }
}