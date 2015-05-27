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
class ContainerMutatorInjectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \Kampaw\Dic\Definition\DefinitionException
     * @expectedExceptionCode 0
     */
    public function Inject_ConcreteMutatorNoMatchingDefinitionRegisteredDiscoveryFalse_ThrowsException()
    {
        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array();

        $container = new Container($config);
        $object = new \stdClass();

        $container->inject($object);
    }

    public function mutatorsSpecificAutowireProvider()
    {
        return array(
            array(AutowireMode::AUTODETECT),
            array(AutowireMode::MUTATORS),
        );
    }

    /**
     * @test
     * @dataProvider mutatorsSpecificAutowireProvider
     */
    public function Inject_ConcreteMutatorMatchingDefinitionRegisteredDiscoveryFalseAutowireMutators_InjectsDependency($autowire)
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\ConcreteParameter';

        $mutator['type'] = 'stdClass';
        $mutator['name'] = 'setConcrete';

        $definition1['concrete'] = $asset;
        $definition1['discovery'] = false;
        $definition1['autowire'] = $autowire;
        $definition1['mutators'][] = $mutator;

        $definition2['concrete'] = 'stdClass';

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array($definition1, $definition2);

        $container = new Container($config);
        $object = new $asset;

        $container->inject($object);

        $this->assertInstanceOf('stdClass', $object->getConcrete());
    }

    /**
     * @test
     */
    public function Inject_ConcreteMutatorNoMatchingDefinitionsRegisteredDiscoveryTrue_InjectsDependency()
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\ConcreteParameter';

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = true;
        $config['definitions'] = array();

        $container = new Container($config);
        $object = new $asset;

        $container->inject($object);

        $this->assertInstanceOf('stdClass', $object->getConcrete());
    }

    /**
     * @test
     * @dataProvider mutatorsSpecificAutowireProvider
     */
    public function Inject_ConcreteMutatorNoMatchingMutatorDefinitionRegisteredDiscoveryFalseAutowireMutators_InjectsNothing($autowire)
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\ConcreteParameter';

        $mutator['type'] = 'stdClass';
        $mutator['name'] = 'setConcrete';

        $definition['concrete'] = $asset;
        $definition['discovery'] = false;
        $definition['autowire'] = $autowire;
        $definition['mutators'][] = $mutator;

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'][] = $definition;

        $container = new Container($config);
        $object = new $asset;

        $container->inject($object);

        $this->assertNull($object->getConcrete());
    }

    /**
     * @test
     * @dataProvider mutatorsSpecificAutowireProvider
     * @expectedException \Kampaw\Dic\Exception\DependencyException
     * @expectedExceptionCode 10
     */
    public function Inject_ConcreteMutatorMatchingDefinitionRegisteredDefinitionExcludedDiscoveryFalseAutowireMutators_ThrowsException($autowire)
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\ConcreteParameter';

        $mutator['type'] = 'stdClass';
        $mutator['name'] = 'setConcrete';

        $definition1['concrete'] = $asset;
        $definition1['discovery'] = false;
        $definition1['autowire'] = $autowire;
        $definition1['mutators'][] = $mutator;

        $definition2['concrete'] = 'stdClass';
        $definition2['candidate'] = false;

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array($definition1, $definition2);

        $container = new Container($config);
        $object = new $asset;

        $container->inject($object);

        $this->assertInstanceOf('stdClass', $object->getConcrete());
    }

    public function nonMutatorsSpecificAutowireProvider()
    {
        return array(
            array(AutowireMode::DISABLED),
            array(AutowireMode::CONSTRUCTOR),
        );
    }

    /**
     * @test
     * @dataProvider nonMutatorsSpecificAutowireProvider
     */
    public function Inject_ConcreteMutatorNoMatchingMutatorDefinitionRegisteredDiscoveryFalseAutowireNotMutators_InjectsNothing($autowire)
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\ConcreteParameter';

        $mutator['type'] = 'stdClass';
        $mutator['name'] = 'setConcrete';

        $definition1['concrete'] = $asset;
        $definition1['discovery'] = false;
        $definition1['autowire'] = $autowire;
        $definition1['mutators'][] = $mutator;

        $definition2['concrete'] = 'stdClass';

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array($definition1, $definition2);

        $container = new Container($config);
        $object = new $asset;

        $container->inject($object);

        $this->assertNull($object->getConcrete());
    }

    /**
     * @test
     * @dataProvider mutatorsSpecificAutowireProvider
     */
    public function Get_ConcreteMutatorMatchingDefinitionRegisteredDiscoveryFalseAutowireMutators_InjectsDependency($autowire)
    {
        $asset = 'Kampaw\Dic\Assets\MutatorInjection\ConcreteParameter';

        $mutator['type'] = 'stdClass';
        $mutator['name'] = 'setConcrete';

        $definition1['concrete'] = $asset;
        $definition1['discovery'] = false;
        $definition1['autowire'] = $autowire;
        $definition1['mutators'][] = $mutator;

        $definition2['concrete'] = 'stdClass';

        $config['assembler'] = new SmartAssembler();
        $config['discovery'] = false;
        $config['definitions'] = array($definition1, $definition2);

        $container = new Container($config);

        $object = $container->get($asset);

        $this->assertInstanceOf('stdClass', $object->getConcrete());
    }
}