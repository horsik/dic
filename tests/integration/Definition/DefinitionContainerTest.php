<?php

namespace Kampaw\Dic\Definition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\DefinitionContainer
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Definition\ArrayDefinition
 * @uses \Kampaw\Dic\Definition\AbstractDefinition
 */
class DefinitionContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefinitionContainer $container
     */
    private $container;

    public function setUp()
    {
        $this->container = new DefinitionContainer();
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByType
     */
    public function GetByType_ConcreteTypeRequestedMatchingConcreteDefinitionRegistered_ReturnsDefinition()
    {
        $config['concrete'] = '\stdClass';

        $definition = new ArrayDefinition($config);

        $this->container->insert($definition);

        $this->assertSame($definition, $this->container->getByType('\stdClass'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByType
     */
    public function GetByType_ConcreteTypeRequestedNoMatchingDefinitionsRegistered_ReturnsNull()
    {
        $this->assertNull($this->container->getByType('\stdClass'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByType
     */
    public function GetByType_ConcreteTypeRequestedMultipleMatchingDefinitionsRegistered_ReturnsFirstRegistered()
    {
        $config['concrete'] = '\stdClass';

        $definition1 = new ArrayDefinition($config);
        $definition2 = new ArrayDefinition($config);

        $this->container->insert($definition1);
        $this->container->insert($definition2);

        $this->assertSame($definition1, $this->container->getByType('\stdClass'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByType
     */
    public function GetByType_DifferentConcreteTypesRequestedMatchingDefinitionsRegistered_ReturnsDefinitions()
    {
        $config1['concrete'] = '\ArrayObject';

        $definition1 = new ArrayDefinition($config1);

        $config2['concrete'] = '\ArrayIterator';

        $definition2 = new ArrayDefinition($config2);

        $this->container->insert($definition1);
        $this->container->insert($definition2);

        $this->assertSame($definition1, $this->container->getByType('\ArrayObject'));
        $this->assertSame($definition2, $this->container->getByType('\ArrayIterator'));

    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByType
     */
    public function GetByType_AbstractTypeRequestedMatchingDefinitionRegistered_ReturnsDefinition()
    {
        $config['concrete'] = '\ArrayObject';
        $config['abstract'] = '\ArrayAccess';

        $definition = new ArrayDefinition($config);
        $this->container->insert($definition);

        $this->assertSame($definition, $this->container->getByType('\ArrayAccess'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByType
     */
    public function GetByType_AbstractTypeRequestedMultipleMatchingDefinitionsRegistered_ReturnsFirstRegistered()
    {
        $config1['concrete'] = '\ArrayObject';
        $config1['abstract'] = '\ArrayAccess';

        $definition1 = new ArrayDefinition($config1);

        $config2['concrete'] = '\ArrayIterator';
        $config2['abstract'] = '\ArrayAccess';

        $definition2 = new ArrayDefinition($config2);

        $this->container->insert($definition1);
        $this->container->insert($definition2);

        $this->assertSame($definition1, $this->container->getByType('\ArrayAccess'));
        $this->assertSame($definition1, $this->container->getByType('\ArrayObject'));
        $this->assertSame($definition2, $this->container->getByType('\ArrayIterator'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByName
     */
    public function GetByName_NameRequestedMatchingNamedDefinitionRegistered_ReturnsDefinition()
    {
        $config['concrete'] = '\ArrayObject';
        $config['name'] = 'requested_name';

        $definition = new ArrayDefinition($config);
        $this->container->insert($definition);

        $this->assertSame($definition, $this->container->getByName('requested_name'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByName
     */
    public function GetByName_NameRequestedNoMatchingDefinitionsRegistered_ReturnsNull()
    {
        $this->assertNull($this->container->getByName('requested_name'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByName
     */
    public function GetByName_DifferentNamesRequestedMatchingDefinitionsRegistered_ReturnsDefinitions()
    {
        $config1['concrete'] = '\ArrayObject';
        $config1['name'] = 'first_name';

        $definition1 = new ArrayDefinition($config1);

        $config2['concrete'] = '\ArrayIterator';
        $config2['name'] = 'second_name';

        $definition2 = new ArrayDefinition($config2);

        $this->container->insert($definition1);
        $this->container->insert($definition2);

        $this->assertSame($definition1, $this->container->getByName('first_name'));
        $this->assertSame($definition2, $this->container->getByName('second_name'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByName
     */
    public function GetByName_NameRequestedMultipleMatchingDefinitionsRegistered_ReturnsFirstRegistered()
    {
        $config['concrete'] = '\ArrayObject';
        $config['name'] = 'requested_name';

        $definition1 = new ArrayDefinition($config);
        $definition2 = new ArrayDefinition($config);

        $this->container->insert($definition1);
        $this->container->insert($definition2);

        $this->assertSame($definition1, $this->container->getByName('requested_name'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByType
     */
    public function GetByType_EmptyTypeRequestedMatchingAbstractDefinitionRegistered_ReturnsNull()
    {
        $config['concrete'] = '\ArrayObject';
        $config['abstract'] = '';

        $definition = new ArrayDefinition($config);
        $this->container->insert($definition);

        $this->assertNull($this->container->getByType(''));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByName
     */
    public function GetByName_EmptyTypeRequestedMatchingNamedDefinitionRegistered_ReturnsNull()
    {
        $config['concrete'] = '\ArrayObject';
        $config['name'] = '';

        $definition = new ArrayDefinition($config);
        $this->container->insert($definition);

        $this->assertNull($this->container->getByName(''));
    }
}