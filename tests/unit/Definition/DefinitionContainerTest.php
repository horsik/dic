<?php

namespace Kampaw\Dic\Definition;

/**
 * @coversDefaultClass \Kampaw\Dic\Definition\DefinitionContainer
 * @covers ::<!public>
 * @uses \Kampaw\Dic\Definition\Definition
 * @uses \Kampaw\Dic\Config\Configurable
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
     * @covers ::count
     */
    public function Count_EmptyContainer_ReturnsZero()
    {
        $this->assertSame(0, $this->container->count());
    }

    /**
     * @test
     * @covers ::insert
     */
    public function Insert_ValidDefinition_ReturnsTrue()
    {
        $config = array(
            'concrete' => '\stdClass'
        );

        $definition = new Definition($config);
        $this->assertTrue($this->container->insert($definition));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::count
     */
    public function Count_OneDefinitionRegistered_ReturnsOne()
    {
        $config = array(
            'concrete' => '\stdClass'
        );

        $definition = new Definition($config);
        $this->container->insert($definition);

        $this->assertSame(1, $this->container->count());
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::count
     */
    public function Count_TwoDefinitionsSameConcrete_ReturnsTwo()
    {
        $config = array(
            'concrete' => '\stdClass'
        );

        $definition1 = new Definition($config);
        $definition2 = new Definition($config);
        $this->container->insert($definition1);
        $this->container->insert($definition2);

        $this->assertSame(2, $this->container->count());
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByType
     */
    public function GetByType_RegistredConcreteType_ReturnsDefinition()
    {
        $config = array(
            'concrete' => '\stdClass'
        );

        $definition = new Definition($config);
        $this->container->insert($definition);

        $this->assertSame($definition, $this->container->getByType('\stdClass'));
    }

    /**
     * @test
     * @covers ::getByType
     */
    public function GetByType_UnregisteredType_ReturnsNull()
    {
        $this->assertNull($this->container->getByType('\stdClass'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getByType
     */
    public function GetByType_RegistredAbstractType_ReturnsDefinition()
    {
        $config = array(
            'concrete' => '\ArrayObject',
            'abstract' => '\ArrayAccess'
        );

        $definition = new Definition($config);
        $this->container->insert($definition);

        $this->assertSame($definition, $this->container->getByType('\ArrayAccess'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::getById
     */
    public function GetById_RegisteredId_ReturnsDefinition()
    {
        $config = array(
            'concrete' => '\ArrayObject',
            'id' => 'registered'
        );

        $definition = new Definition($config);
        $this->container->insert($definition);

        $this->assertSame($definition, $this->container->getById('registered'));
    }

    /**
     * @test
     * @covers ::getById
     */
    public function GetById_UnregistredId_ReturnsNull()
    {
        $this->assertNull($this->container->getById('unregistered'));
    }

    /**
     * @test
     * @covers ::insert
     */
    public function Insert_TwoDefinitionsIdCollision_ReturnsFalse()
    {
        $config = array(
            'concrete' => '\ArrayObject',
            'id' => 'registered'
        );

        $definition1 = new Definition($config);
        $definition2 = new Definition($config);

        $this->assertTrue($this->container->insert($definition1));
        $this->assertFalse($this->container->insert($definition2));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::count
     * @covers ::clear
     */
    public function Clear_ContainsConcreteDefinition_CountZero()
    {
        $config = array(
            'concrete' => '\ArrayObject',
        );

        $definition = new Definition($config);
        $this->container->insert($definition);

        $this->assertSame(1, $this->container->count());

        $this->container->clear();

        $this->assertSame(0, $this->container->count());
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::clear
     * @covers ::getByType
     */
    public function Clear_ContainsAbstractDefinition_CanNoLongerResolve()
    {
        $config = array(
            'concrete' => '\ArrayObject',
            'abstract' => '\ArrayAccess',
        );

        $definition = new Definition($config);
        $this->container->insert($definition);
        $this->container->clear();

        $this->assertNull($this->container->getByType('\ArrayAccess'));
    }

    /**
     * @test
     * @covers ::insert
     * @covers ::clear
     * @covers ::getById
     */
    public function Clear_ContainsIdDefinition_CanNoLongerResolve()
    {
        $config = array(
            'concrete' => '\ArrayObject',
            'id' => 'registered'
        );

        $definition = new Definition($config);
        $this->container->insert($definition);
        $this->container->clear();

        $this->assertNull($this->container->getById('registered'));
    }
}