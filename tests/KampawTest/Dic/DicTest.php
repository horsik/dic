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
    public function CreateInstance_StdClass_ReturnsStdClassInstance()
    {
        $result = $this->dic->resolveClass('stdClass');

        $this->assertInstanceOf('stdClass', $result);
    }

    /**
     * @test
     * @expectedException \ReflectionException
     */
    public function CreateInstance_InvalidClass_ThrowsException()
    {
        $this->dic->resolveClass('BogusClass');
    }

    /**
     * @test
     */
    public function CreateInstance_InjectContainer_ReturnsObjectWithDicInjected()
    {
        $this->dic->register('Kampaw\Dic\Dic', 'Kampaw\Dic\DicInterface', false, $this->dic);
        $result = $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\A');

        $this->assertSame($this->dic, $result->dic);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCall
     */
    public function CreateInstance_MissingDependency_ThrowsException()
    {
        $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\B');
    }

    /**
     * @test
     */
    public function CreateInstance_ArgumentHasDefaultValue_DefaultValuePassed()
    {
        $result = $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\C');

        $this->assertEquals('defaultValue', $result->extra);
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCall
     */
    public function CreateInstance_ArgumentIsClassWithoutInterface_ThrowsException()
    {
        $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\D');
    }

    /**
     * @test
     * @expectedException \Kampaw\Dic\Exception\BadMethodCall
     */
    public function CreateInstance_ArgumentIsNotOptionalScalar_ThrowsException()
    {
        $this->dic->resolveClass('KampawTest\Dic\TestAsset\ConstructorInjection\E');
    }

    /**
     * @test
     */
    public function CreateInstance_DependencyRequireDependency_EverythingSatified()
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
    public function CreateInstance_CircularSelfDepencency_ThrowsException()
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
    public function CreateInstance_CircularSelfDepencencySecondLevel_ThrowsException()
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
    public function CreateInstance_CircularDependency_RecoversIfCaught()
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
}