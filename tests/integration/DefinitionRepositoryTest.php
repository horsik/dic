<?php

namespace Kampaw\Dic;

use Kampaw\Dic\Definition\ArrayDefinition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\DefinitionRepository
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Definition\ArrayDefinition
 * @uses \Kampaw\Dic\Definition\AbstractDefinition
 */
class DefinitionRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefinitionRepository $container
     */
    private $container;

    public function setUp()
    {
        $this->container = new DefinitionRepository();
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::hasType
     */
    public function HasType_ConcreteTypeRequestedMatchingConcreteDefinitionRegistered_ReturnsTrue()
    {
        $config['concrete'] = '\stdClass';

        $definition = new ArrayDefinition($config);

        $this->container->insert($definition);

        $this->assertTrue($this->container->hasType('\stdClass'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::HasType
     */
    public function HasType_ConcreteTypeRequestedNoMatchingDefinitionsRegistered_ReturnsFalse()
    {
        $this->assertFalse($this->container->hasType('\stdClass'));
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
     * @covers ::hasName
     */
    public function HasName_NameRequestedMatchingNamedDefinitionRegistered_ReturnsTrue()
    {
        $config['concrete'] = '\ArrayObject';
        $config['name'] = 'requested_name';

        $definition = new ArrayDefinition($config);
        $this->container->insert($definition);

        $this->assertTrue($this->container->hasName('requested_name'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::hasName
     */
    public function HasName_NameRequestedNoMatchingDefinitionsRegistered_ReturnsFalse()
    {
        $this->assertFalse($this->container->hasName('requested_name'));
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
}