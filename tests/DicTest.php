<?php

namespace KampawTest\Dic;

use Kampaw\Dic\Dic;
use Kampaw\Dic\Exception\CircularDependencyException;
use PHPUnit_Framework_TestCase as TestCase;

class DicTest extends TestCase
{
    /**
     * @var Dic $dic
     */
    private $dic;

    public function setUp()
    {
        $this->dic = new Dic();
    }

    /**
     * @test
     */
    public function Constructor_WithoutArguments_HasCorrectInterface()
    {
        $this->assertInstanceOf('Kampaw\Dic\DicInterface', $this->dic);
    }

    /**
     * @test
     */
    public function Constructor_PassConfig_DicConfigured()
    {
        $config = array(
            'factories' => array(
                'KampawTest\Dic\TestAsset\Config\AInterface' => array(
                    'KampawTest\Dic\TestAsset\Config\A'
                ),
            ),
            'singletons' => array(
                'Kampaw\Dic\DicInterface' => array(
                    'Kampaw\Dic\Dic',
                ),
            ),
            'aliases' => array(
                'Kampaw\Dic\DicInterface' => 'Dic'
            ),
        );

        $dic = new Dic($config);
        $result = $dic->getConfig();

        $this->assertEquals($config, $result);
    }

    /**
     * @test
     */
    public function Register_Self_ReturnsNull()
    {
        $result = $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');

        $this->assertNull($result);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function Register_NullArguments_ThrowsException()
    {
        $this->dic->register(null, null);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function Register_SelfNonExistentInterface_ThrowsException()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'BogusInterface');
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function Register_SelfWithWrongInterface_ThrowsException()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'ArrayAccess');
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function Register_SelfTwice_ThrowsException()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function RegisterAlias_NonExistentInterface_ThrowsException()
    {
        $this->dic->registerAlias('BogusInterface', 'Bogus');
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function RegisterAlias_UnregistredInterface_ThrowsException()
    {
        $this->dic->registerAlias('Kampaw\Dic\Dic', 'Dic');
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function RegisterAlias_OverwriteAlias_ThrowsException()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $this->dic->register(
            'KampawTest\Dic\TestAsset\Aliases\A',
            'KampawTest\Dic\TestAsset\Aliases\AInterface'
        );
        $this->dic->registerAlias('Kampaw\Dic\DicInterface', 'Dic');
        $this->dic->registerAlias('KampawTest\Dic\TestAsset\Aliases\AInterface', 'Dic');
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function RegisterAlias_Twice_ThrowsException()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $this->dic->registerAlias('Kampaw\Dic\DicInterface', 'Dic');
        $this->dic->registerAlias('Kampaw\Dic\DicInterface', 'Dic');
    }

    public function invalidAliasProvider()
    {
        return array(
            array(0xBAD),
            array(array()),
            array(new \stdClass()),
        );
    }

    /**
     * @test
     * @dataProvider invalidAliasProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function RegisterAlias_InvalidAlias_ThrowsException($alias)
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $this->dic->registerAlias('Kampaw\Dic\DicInterface', $alias);
    }

    /**
     * @test
     */
    public function UnregisterAlias_RegistredAlias_RemovesAlias()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $this->dic->registerAlias('Kampaw\Dic\DicInterface', 'Dic');
        $this->dic->unregisterAlias('Dic');

        $result = $this->dic->listClasses('Dic');

        $this->assertEmpty($result);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function UnregisterAlias_NotRegistredAlias_ThrowsException()
    {
        $this->dic->unregisterAlias('Kampaw\Dic\Dic');
    }

    /**
     * @test
     */
    public function ListClasses_PassOwnInterfaceWhenOneRegistred_ReturnsOwnClass()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $result = $this->dic->listClasses('Kampaw\Dic\DicInterface');

        $this->assertContains('Kampaw\Dic\Dic', $result);
    }

    /**
     * @test
     */
    public function ListClasses_PassOwnInterfaceWhenMoreRegistred_ReturnsOwnClass()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $this->dic->register('DateTime', 'DateTimeInterface');
        $result = $this->dic->listClasses('Kampaw\Dic\DicInterface');

        $this->assertContains('Kampaw\Dic\Dic', $result);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCall
     */
    public function ResolveInterface_UnregistredInterface_ReturnsNull()
    {
        $result = $this->dic->resolveInterface('UnregistredInterface');
    }

    /**
     * @test
     */
    public function ResolveInterface_RegistredInterface_ReturnsInstance()
    {
        $this->dic->register('DateTime', 'DateTimeInterface');
        $result = $this->dic->resolveInterface('DateTimeInterface');

        $this->assertInstanceOf('DateTimeInterface', $result);

    }

    /**
     * @test
     */
    public function ResolveInterface_OwnInterface_ReturnsSelf()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface', false, $this->dic);
        $result = $this->dic->resolveInterface('Kampaw\Dic\DicInterface');

        $this->assertSame($this->dic, $result);
    }

    /**
     * @test
     */
    public function ResolveInterface_ClassAsSingleton_ReturnsSameInstance()
    {
        $this->dic->register('DateTime', 'DateTimeInterface', false);

        $first = $this->dic->resolveInterface('DateTimeInterface');
        $second = $this->dic->resolveInterface('DateTimeInterface');

        $this->assertSame($first, $second);
    }

    /**
     * @test
     */
    public function ResolveInterface_CircularDependency_RecoversIfCaught()
    {
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\H',
            'KampawTest\Dic\TestAsset\ConstructorInjection\HInterface'
        );
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\X',
            'KampawTest\Dic\TestAsset\ConstructorInjection\XInterface'
        );
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\Y',
            'KampawTest\Dic\TestAsset\ConstructorInjection\YInterface'
        );
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\F',
            'KampawTest\Dic\TestAsset\ConstructorInjection\FInterface'
        );

        try {
            $this->dic->resolveInterface('KampawTest\Dic\TestAsset\ConstructorInjection\HInterface');
        } catch (CircularDependencyException $e) {
            $result = $this->dic->resolveInterface('KampawTest\Dic\TestAsset\ConstructorInjection\FInterface');
        }

        $this->assertInstanceOf('KampawTest\Dic\TestAsset\ConstructorInjection\F', $result);
    }

    /**
     * @test
     */
    public function GetCandidate_OwnInterface_ReturnsOwnClass()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $result = $this->dic->getCandidate('Kampaw\Dic\DicInterface');

        $this->assertEquals('Kampaw\Dic\Dic', $result);
    }

    /**
     * @test
     */
    public function GetCandidate_UnregistredInterface_ReturnsNull()
    {
        $result = $this->dic->getCandidate('UnregistredInterface');

        $this->assertNull($result);
    }

    /**
     * @test
     */
    public function ResolveClass_StdClass_ReturnsStdClassInstance()
    {
        $result = $this->dic->resolveClass('stdClass');

        $this->assertInstanceOf('stdClass', $result);
    }

    /**
     * @test
     * @expectedException \ReflectionException
     */
    public function ResolveClass_InvalidClass_ThrowsException()
    {
        $this->dic->resolveClass('BogusClass');
    }

    /**
     * @test
     */
    public function ResolveClass_InjectContainer_ReturnsObjectWithDicInjected()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface', false, $this->dic);
        $result = $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\A');

        $this->assertSame($this->dic, $result->dic);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCall
     */
    public function ResolveClass_MissingDependency_ThrowsException()
    {
        $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\B');
    }

    /**
     * @test
     */
    public function ResolveClass_ArgumentHasDefaultValue_DefaultValuePassed()
    {
        $result = $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\C');

        $this->assertEquals('defaultValue', $result->extra);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCall
     */
    public function ResolveClass_ArgumentIsClassWithoutInterface_ThrowsException()
    {
        $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\D');
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCall
     */
    public function ResolveClass_ArgumentIsNotOptionalScalar_ThrowsException()
    {
        $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\E');
    }

    /**
     * @test
     */
    public function ResolveClass_DependencyRequireDependency_EverythingSatified()
    {
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\X',
            'KampawTest\Dic\TestAsset\ConstructorInjection\XInterface'
        );
        $result = $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\F');

        $this->assertInstanceOf('KampawTest\Dic\TestAsset\ConstructorInjection\X', $result->x);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\CircularDependencyException
     */
    public function ResolveClass_CircularSelfDepencency_ThrowsException()
    {
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\G',
            'KampawTest\Dic\TestAsset\ConstructorInjection\GInterface'
        );
        $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\G');
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\CircularDependencyException
     */
    public function ResolveClass_CircularSelfDepencencySecondLevel_ThrowsException()
    {
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\H',
            'KampawTest\Dic\TestAsset\ConstructorInjection\HInterface'
        );
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\X',
            'KampawTest\Dic\TestAsset\ConstructorInjection\XInterface'
        );
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\Y',
            'KampawTest\Dic\TestAsset\ConstructorInjection\YInterface'
        );
        $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\H');
    }

    /**
     * @test
     */
    public function ResolveClass_CircularDependency_RecoversIfCaught()
    {
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\H',
            'KampawTest\Dic\TestAsset\ConstructorInjection\HInterface'
        );
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\X',
            'KampawTest\Dic\TestAsset\ConstructorInjection\XInterface'
        );
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\Y',
            'KampawTest\Dic\TestAsset\ConstructorInjection\YInterface'
        );
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\F',
            'KampawTest\Dic\TestAsset\ConstructorInjection\FInterface'
        );

        try {
            $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\H');
        } catch (CircularDependencyException $e) {
            $result = $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\F');
        }

        $this->assertInstanceOf('KampawTest\Dic\TestAsset\ConstructorInjection\F', $result);
    }

    /**
     * @test
     */
    public function ResolveClass_DicAwareInterface_DicIsInjected()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface', false, $this->dic);
        $result = $this->dic->ResolveClass('KampawTest\Dic\TestAsset\PropertyInjection\B');

        $this->assertSame($this->dic, $result->getDic());
    }

    /**
     * @test
     */
    public function ResolveClass_DicAwareInterfaceWhenDicNotRegistred_DoesntThrowException()
    {
        $this->dic->ResolveClass('KampawTest\Dic\TestAsset\PropertyInjection\B');
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function ResolveClass_TwoInterfaces_ReturnsInjected()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface', false, $this->dic);
        $this->dic->register(
            'KampawTest\Dic\TestAsset\PropertyInjection\X',
            'KampawTest\Dic\TestAsset\PropertyInjection\XInterface'
        );

        $result = $this->dic->ResolveClass('KampawTest\Dic\TestAsset\PropertyInjection\C');

        $this->assertSame($this->dic, $result->getDic());
        $this->assertInstanceOf('KampawTest\Dic\TestAsset\PropertyInjection\X', $result->getX());
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCall
     */
    public function ResolveClass_SetterHasScalarParameters_ThrowsException()
    {
        $this->dic->register(
            'KampawTest\Dic\TestAsset\PropertyInjection\Y',
            'KampawTest\Dic\TestAsset\PropertyInjection\YInterface'
        );
        $this->dic->ResolveClass('KampawTest\Dic\TestAsset\PropertyInjection\D');
    }

    /**
     * @test
     */
    public function ResolveClass_SetterWithoutParameters_IsCalled()
    {
        $result = $this->dic->ResolveClass('KampawTest\Dic\TestAsset\PropertyInjection\E');

        $this->assertNotNull($result->nothing);
    }

    /**
     * @test
     */
    public function ResolveClass_UppercaseSetterName_IsCalled()
    {
        $result = $this->dic->ResolveClass('KampawTest\Dic\TestAsset\PropertyInjection\F');

        $this->assertNotNull($result->nothing);
    }

    /**
     * @test
     */
    public function Resolve_RegistredInterface_ReturnsInstance()
    {
        $this->dic->register('DateTime', 'DateTimeInterface');
        $result = $this->dic->resolve('DateTimeInterface');

        $this->assertInstanceOf('DateTimeInterface', $result);
    }

    /**
     * @test
     */
    public function Resolve_Class_ReturnsInstance()
    {
        $result = $this->dic->resolve('DateTime');

        $this->assertInstanceOf('DateTimeInterface', $result);
    }

    /**
     * @test
     */
    public function Resolve_DicAlias_ReturnsDicInstance()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface', false, $this->dic);
        $this->dic->registerAlias('Kampaw\Dic\DicInterface', 'Dic');

        $result = $this->dic->resolve('Dic');

        $this->assertSame($this->dic, $result);
    }

    /**
     * @test
     */
    public function InjectDependencies_ClassHasNoInterfaces_ReturnsUnchanged()
    {
        $mock = $this->getMock('KampawTest\Dic\TestAsset\PropertyInjection\A');
        $mock->expects($this->never())->method($this->anything())->withAnyParameters();

        $this->dic->injectDependencies($mock);
    }

    /**
     * @test
     */
    public function InjectDependencies_DicAwareInterface_DicIsInjected()
    {
        $mock = $this->getMock('KampawTest\Dic\TestAsset\PropertyInjection\B');
        $mock->expects($this->once())->method('setDic')->with($this->identicalTo($this->dic));

        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface', false, $this->dic);
        $this->dic->injectDependencies($mock);
    }

    /**
     * @test
     */
    public function InjectDependencies_DicAwareInterfaceWhenDicNotRegistred_DoesntThrowException()
    {
        $mock = $this->getMock('KampawTest\Dic\TestAsset\PropertyInjection\B');

        $this->dic->injectDependencies($mock);
        $this->addToAssertionCount(1);
    }

    /**
     * @test
     */
    public function InjectDependencies_TwoInterfaces_ReturnsInjected()
    {
        $mock = $this->getMock('KampawTest\Dic\TestAsset\PropertyInjection\C');
        $mock->expects($this->once())->method('setDic')->with($this->identicalTo($this->dic));
        $mock->expects($this->once())->method('setX')
             ->with($this->isInstanceOf('KampawTest\Dic\TestAsset\PropertyInjection\X'));

        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface', false, $this->dic);
        $this->dic->register(
            'KampawTest\Dic\TestAsset\PropertyInjection\X',
            'KampawTest\Dic\TestAsset\PropertyInjection\XInterface'
        );

        $this->dic->injectDependencies($mock);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCall
     */
    public function InjectDependencies_SetterHasScalarParameters_ThrowsException()
    {
        $mock = $this->getMock('KampawTest\Dic\TestAsset\PropertyInjection\D');

        $this->dic->register(
            'KampawTest\Dic\TestAsset\PropertyInjection\Y',
            'KampawTest\Dic\TestAsset\PropertyInjection\YInterface'
        );
        $this->dic->injectDependencies($mock);
    }

    /**
     * @test
     */
    public function InjectDependencies_SetterWithoutParameters_IsCalled()
    {
        $mock = $this->getMock('KampawTest\Dic\TestAsset\PropertyInjection\E');
        $mock->expects($this->once())->method('setNothing');

        $this->dic->injectDependencies($mock);
    }

    /**
     * @test
     */
    public function InjectDependencies_UppercaseSetterName_IsCalled()
    {
        $mock = $this->getMock('KampawTest\Dic\TestAsset\PropertyInjection\F');
        $mock->expects($this->once())->method('SETNOTHING');

        $this->dic->injectDependencies($mock);

    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function InjectDependencies_InvalidArgument_ThrowsException()
    {
        $this->dic->injectDependencies(10);
    }

    /**
     * @test
     */
    public function SetConfig_EmptyFactories_ClearsInterfaces()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $this->dic->setConfig(array('factories' => array()));

        $result = array_filter($this->dic->getConfig());

        $this->assertEmpty($result);
    }

    /**
     * @test
     */
    public function SetConfig_PassObject_NoException()
    {
        $config = new \stdClass();
        $config->factories = array();
        $config->singletons = array();
        $config->aliases = array();

        $this->dic->setConfig($config);

        $result = $this->dic->getConfig();

        $this->assertEquals((array) $config, $result);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterFactoryArray_Success()
    {
        $config = array(
            'factories' => array(
                'KampawTest\Dic\TestAsset\Config\AInterface' => array(
                    'KampawTest\Dic\TestAsset\Config\A',
                ),
            ),
            'singletons' => array(),
            'aliases' => array(),
        );
        $this->dic->setConfig($config);

        $result = $this->dic->getConfig();

        $this->assertEquals($config, $result);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterFactoryString_Success()
    {
        $config = array(
            'factories' => array(
                'KampawTest\Dic\TestAsset\Config\AInterface' => 'KampawTest\Dic\TestAsset\Config\A',
            ),
        );
        $this->dic->setConfig($config);

        $result = $this->dic->getCandidate('KampawTest\Dic\TestAsset\Config\AInterface');

        $this->assertEquals('KampawTest\Dic\TestAsset\Config\A', $result);
    }

    public function invalidFactoryProvider()
    {
        return array(
            array(array(
                'BogusInterface' => 'BogusClass'
            )),
            array(array(
                'ArrayAccess' => 'BogusClass'
            )),
            array(array(
                'BogusInterface' => 'Kampaw\Dic\Dic'
            )),
            array(array(
                'ArrayAccess' => 'Kampaw\Dic\Dic'
            )),
        );
    }

    /**
     * @test
     * @dataProvider invalidFactoryProvider
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function SetConfig_RegisterInvalidFactory_ThrowsException($factories)
    {
        $config = array(
            'factories' => $factories,
        );
        $this->dic->setConfig($config);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterFactoryMultipleClasses_Success()
    {
        $classes = array(
            'KampawTest\Dic\TestAsset\Config\A',
            'KampawTest\Dic\TestAsset\Config\B',
        );
        $config = array(
            'factories' => array(
                'KampawTest\Dic\TestAsset\Config\AInterface' => $classes,
            ),
        );
        $this->dic->setConfig($config);

        $result = $this->dic->listClasses('KampawTest\Dic\TestAsset\Config\AInterface');

        $this->assertEquals($classes, $result);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterFactoryMultipleInterfaces_Success()
    {
        $config = array(
            'factories' => array(
                'KampawTest\Dic\TestAsset\Config\AInterface' => array(
                    'KampawTest\Dic\TestAsset\Config\A',
                ),
                'KampawTest\Dic\TestAsset\Config\CInterface' => array(
                    'KampawTest\Dic\TestAsset\Config\C',
                ),
            ),
        );
        $this->dic->setConfig($config);

        $result = $this->dic->getConfig();

        $this->assertEquals($config['factories'], $result['factories']);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterSingletonArray_Success()
    {
        $config = array(
            'singletons' => array(
                'KampawTest\Dic\TestAsset\Config\AInterface' => array(
                    'KampawTest\Dic\TestAsset\Config\A'
                ),
            ),
        );
        $this->dic->setConfig($config);

        $result = $this->dic->getConfig();

        $this->assertEquals($config['singletons'], $result['singletons']);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterSingletonString_Success()
    {
        $config = array(
            'singletons' => array(
                'KampawTest\Dic\TestAsset\Config\AInterface' => 'KampawTest\Dic\TestAsset\Config\A',
            ),
        );
        $this->dic->setConfig($config);

        $result = $this->dic->getConfig();
        $result = $result['singletons']['KampawTest\Dic\TestAsset\Config\AInterface'];

        $this->assertContains('KampawTest\Dic\TestAsset\Config\A', $result);
    }

    /**
     * @test
     */
    public function SetConfig_EmptySingletons_ClearsSingletons()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface', false);
        $this->dic->setConfig(array('singletons' => array()));

        $result = $this->dic->getConfig();

        $this->assertEmpty($result['singletons']);
    }

    /**
     * @test
     * @dataProvider invalidFactoryProvider
     * @expectedException \Kampaw\Dic\Exception\UnexpectedValueException
     */
    public function SetConfig_RegisterInvalidSingleton_ThrowsException($singletons)
    {
        $config = array(
            'singletons' => $singletons,
        );
        $this->dic->setConfig($config);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterSingletonMultipleClasses_Success()
    {
        $classes = array(
            'KampawTest\Dic\TestAsset\Config\A',
            'KampawTest\Dic\TestAsset\Config\B',
        );
        $config = array(
            'singletons' => array(
                'KampawTest\Dic\TestAsset\Config\AInterface' => $classes,
            ),
        );
        $this->dic->setConfig($config);

        $result = $this->dic->getConfig();
        $result = $result['singletons']['KampawTest\Dic\TestAsset\Config\AInterface'];

        $this->assertEquals($classes, $result);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterSingletonMultipleInterfaces_Success()
    {
        $config = array(
            'singletons' => array(
                'KampawTest\Dic\TestAsset\Config\AInterface' => array(
                    'KampawTest\Dic\TestAsset\Config\A',
                ),
                'KampawTest\Dic\TestAsset\Config\CInterface' => array(
                    'KampawTest\Dic\TestAsset\Config\C',
                ),
            ),
        );
        $this->dic->setConfig($config);

        $result = $this->dic->getConfig();

        $this->assertEquals($config['singletons'], $result['singletons']);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterAliasEmptyArray_ClearsAliases()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $this->dic->registerAlias('Kampaw\Dic\DicInterface', 'Dic');
        $this->dic->setConfig(array('aliases' => array()));

        $result = $this->dic->getConfig();

        $this->assertEmpty($result['aliases']);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterOneAlias_Success()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $config = array(
            'aliases' => array(
                'Kampaw\Dic\DicInterface' => 'Dic',
            )
        );
        $this->dic->setConfig($config);

        $result = $this->dic->getConfig();

        $this->assertEquals($config['aliases'], $result['aliases']);
    }

    /**
     * @test
     */
    public function SetConfig_RegisterMultipleAliases_Success()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');
        $this->dic->register(
            'KampawTest\Dic\TestAsset\Aliases\A',
            'KampawTest\Dic\TestAsset\Aliases\AInterface'
        );

        $config = array(
            'aliases' => array(
                'Kampaw\Dic\DicInterface' => 'Dic',
                'KampawTest\Dic\TestAsset\Aliases\AInterface' => 'A',
            )
        );
        $this->dic->setConfig($config);

        $result = $this->dic->getConfig();

        $this->assertEquals($config['aliases'], $result['aliases']);
    }

    /**
     * @test
     * @dataProvider invalidAliasProvider
     * @expectedException \Kampaw\Dic\Exception\InvalidArgumentException
     */
    public function SetConfig_RegisterInvalidAlias_ThrowsException($alias)
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface');

        $config = array(
            'aliases' => array(
                'Kampaw\Dic\DicInterface' => $alias,
            )
        );
        $this->dic->setConfig($config);
    }

    /**
     * @test
     */
    public function WithArguments_IntegerArgument_PropertySet()
    {
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\I',
            'KampawTest\Dic\TestAsset\ConstructorInjection\IInterface'
        );

        $result = $this->dic->withArguments(array(10))->resolve('KampawTest\Dic\TestAsset\ConstructorInjection\IInterface');

        $this->assertEquals($result->test, 10);
    }

    /**
     * @test
     */
    public function WithArguments_SecondResolveWithoutArguments_SecondObjectNotHydrated()
    {
        $this->dic->register(
            'KampawTest\Dic\TestAsset\ConstructorInjection\I',
            'KampawTest\Dic\TestAsset\ConstructorInjection\IInterface'
        );

        $first = $this->dic->withArguments(array(10))->resolve('KampawTest\Dic\TestAsset\ConstructorInjection\IInterface');
        $second = $this->dic->resolve('KampawTest\Dic\TestAsset\ConstructorInjection\IInterface');

        $this->assertEquals($first->test, 10);
        $this->assertNull($second->test);
    }
}