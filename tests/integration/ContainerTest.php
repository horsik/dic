<?php

namespace Kampaw\Dic;

use Kampaw\Dic\Assembler\SmartAssembler;
use Kampaw\Dic\Definition\AutowireMode;

/**
 * @coversDefaultClass \Kampaw\Dic\Container
 * @covers ::__construct
 * @covers ::<!public>
 * @uses \Kampaw\Dic\DefinitionRepository
 * @uses \Kampaw\Dic\Definition\ArrayDefinition
 * @uses \Kampaw\Dic\Definition\AbstractDefinition
 * @uses \Kampaw\Dic\Definition\Parameter
 * @uses \Kampaw\Dic\Assembler\SmartAssembler
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function Get_ConcreteTypeWithoutParametersMatchingDefinitionRegisteredDiscoveryFalse_ReturnsInstance()
    {
        $definition['concrete'] = '\stdClass';

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'][] = $definition;

        $container = new Container($config);

        $this->assertInstanceOf('\stdClass', $container->get('\stdClass'));
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     * @expectedExceptionCode 0
     */
    public function Get_ConcreteTypeWithoutParametersNoMatchingDefinitionRegisteredDiscoveryFalse_ThrowsException()
    {
        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array();

        $container = new Container($config);
        $container->get('\stdClass');
    }

    /**
     * @test
     */
    public function Get_ConcreteTypeWithoutParametersNoMatchingDefinitionRegisteredDiscoveryTrue_ReturnsInstance()
    {
        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = true;
        $config['definitions'] = array();

        $container = new Container($config);

        $this->assertInstanceOf('\stdClass', $container->get('\stdClass'));
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     * @expectedExceptionCode 10
     */
    public function Get_ConcreteTypeWithPrivateConstructorNoMatchingDefinitionRegisteredDiscoveryTrue_ThrowsException()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\PrivateConstructor';

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = true;
        $config['definitions'] = array();

        $container = new Container($config);
        $container->get($asset);
    }

    /**
     * @test
     */
    public function Get_ConcreteTypeWithScalarParameterWithDefaultValueMatchingDefinitionRegisteredDiscoveryFalse_ReturnsInstance()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ScalarParameterWithDefaultValue';

        $parameter['value'] = 'default';

        $definition['concrete'] = $asset;
        $definition['parameters'][] = $parameter;

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'][] = $definition;

        $container = new Container($config);

        $this->assertInstanceOf($asset, $container->get($asset));
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     * @expectedExceptionCode 20
     */
    public function Get_ConcreteTypeWithScalarParameterWithoutDefaultValueMatchingMalformedDefinitionRegisteredDiscoveryFalse_ThrowsException()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ScalarParameter';

        $definition['concrete'] = $asset;
        $definition['parameters'][] = array();

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'][] = $definition;

        $container = new Container($config);
        $container->get($asset);
    }

    /**
     * @test
     */
    public function Get_ConcreteTypeWithExplicitConcreteParameterMatchingDefinitionsRegistered_ReturnsInstance()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';

        $parameter['ref'] = 'dependency';

        $definition1['concrete'] = $asset;
        $definition1['parameters'][] = $parameter;

        $definition2['concrete'] = '\stdClass';
        $definition2['name'] = 'dependency';

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array($definition1, $definition2);

        $container = new Container($config);

        $this->assertInstanceOf($asset, $container->get($asset));
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     * @expectedExceptionCode 30
     */
    public function Get_ConcreteTypeWithExplicitConcreteParameterNoMatchingDependencyDefinitionRegistered_ThrowsException()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';

        $parameter['ref'] = 'dependency';

        $definition['concrete'] = $asset;
        $definition['parameters'][] = $parameter;

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'][] = $definition;

        $container = new Container($config);
        $container->get($asset);
    }

    public function nonConstructorSpecificAutowireProvider()
    {
        return array(
            array(AutowireMode::DISABLED),
            array(AutowireMode::MUTATORS),
        );
    }

    /**
     * @test
     * @dataProvider nonConstructorSpecificAutowireProvider
     * @expectedException \Kampaw\Dic\Exception\DependencyException
     * @expectedExceptionCode 0
     */
    public function Get_ConcreteTypeConcreteParameterMatchingDefinitionsRegisteredParameterNotResolvedByAutowire_ThrowsException($autowire)
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';

        $parameter['type'] = '\stdClass';

        $definition1['concrete'] = $asset;
        $definition1['autowire'] = $autowire;
        $definition1['parameters'][] = $parameter;

        $definition2['concrete'] = '\stdClass';

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array($definition1, $definition2);

        $container = new Container($config);
        $container->get($asset);
    }

    public function constructorSpecificAutowireProvider()
    {
        return array(
            array(AutowireMode::AUTODETECT),
            array(AutowireMode::CONSTRUCTOR),
        );
    }

    /**
     * @test
     * @dataProvider constructorSpecificAutowireProvider
     */
    public function Get_ConcreteTypeConcreteParameterMatchingDefinitionsRegisteredParameterResolvedByAutowireDiscoveryFalse_ReturnsInstance($autowire)
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';

        $parameter['type'] = '\stdClass';

        $definition1['concrete'] = $asset;
        $definition1['autowire'] = $autowire;
        $definition1['parameters'][] = $parameter;

        $definition2['concrete'] = '\stdClass';

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array($definition1, $definition2);

        $container = new Container($config);

        $this->assertInstanceOf($asset, $container->get($asset));
    }

    /**
     * @test
     * @dataProvider constructorSpecificAutowireProvider
     * @expectedException \Kampaw\Dic\Exception\DependencyException
     * @expectedExceptionCode 0
     */
    public function Get_ConcreteTypeConcreteParameterNoMatchingParameterDefinitionRegisteredResolvedByAutowireDiscoveryFalse_ThrowsException($autowire)
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';

        $parameter['type'] = '\stdClass';

        $definition['concrete'] = $asset;
        $definition['autowire'] = $autowire;
        $definition['parameters'][] = $parameter;

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'][] = $definition;

        $container = new Container($config);
        $container->get($asset);
    }

    /**
     * @test
     * @dataProvider constructorSpecificAutowireProvider
     */
    public function Get_ConcreteTypeConcreteParameterNoMatchingParameterDefinitionRegisteredResolvedByAutowireDiscoveryTrue_ReturnsInstance($autowire)
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';

        $parameter['type'] = '\stdClass';

        $definition['concrete'] = $asset;
        $definition['autowire'] = $autowire;
        $definition['parameters'][] = $parameter;

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = true;
        $config['definitions'][] = $definition;

        $container = new Container($config);

        $this->assertInstanceOf($asset, $container->get($asset));
    }

    /**
     * @test
     */
    public function Get_ConcreteTypeWithConcreteParameterWithDefaultValueNoMatchingParameterDefinitionRegisteredDiscoveryFalse_ReturnsInstance()
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameterWithDefaultValue';

        $parameter['type'] = '\stdClass';
        $parameter['value'] = null;

        $definition['concrete'] = $asset;
        $definition['parameters'][] = $parameter;

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'][] = $definition;

        $container = new Container($config);

        $this->assertInstanceOf($asset, $container->get($asset));
    }

    /**
     * @test
     * @dataProvider constructorSpecificAutowireProvider
     * @expectedException \Kampaw\Dic\Exception\DependencyException
     * @expectedExceptionCode 10
     */
    public function Get_ConcreteTypeConcreteParameterMatchingDefinitionsRegisteredParameterResolvedByAutowireDefinitionCandidateFalse_ThrowsException($autowire)
    {
        $asset = '\Kampaw\Dic\Assets\ConstructorInjection\ConcreteParameter';

        $parameter['type'] = '\stdClass';

        $definition1['concrete'] = $asset;
        $definition1['autowire'] = $autowire;
        $definition1['parameters'][] = $parameter;

        $definition2['concrete'] = '\stdClass';
        $definition2['candidate'] = false;

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array($definition1, $definition2);

        $container = new Container($config);
        $container->get($asset);
    }
}