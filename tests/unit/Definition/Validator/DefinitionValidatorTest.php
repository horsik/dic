<?php

namespace Kampaw\Dic\Definition\Validator;

/**
 * @coversDefaultClass Kampaw\Dic\Definition\Validator\DefinitionValidator
 * @covers ::__construct
 * @covers ::<!public>
 */
class DefinitionValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefinitionValidator $validator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new DefinitionValidator();
    }

    function nonArrayTypeProvider()
    {
        return array(
            array(false),
            array(0xBAD),
            array(0.1),
            array('invalid'),
            array(new \stdClass()),
            array(tmpfile()),
            array(function() {}),
            array(NAN),
        );
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider nonArrayTypeProvider
     */
    public function IsValid_InvalidDefinitionType_ReturnsFalse($definition)
    {
        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     * @dataProvider nonArrayTypeProvider
     */
    public function GetErrors_InvalidDefinitionType_ReturnsError($definition)
    {
        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::ARGUMENT_NOT_AN_ARRAY, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_ConcreteKeyMissing_ReturnsFalse()
    {
        $definition = array();

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     */
    public function GetErrors_ConcreteKeyMissing_ReturnsError()
    {
        $definition = array();

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::CONCRETE_KEY_MISSING, $errors);
    }

    function nonStringTypeProvider()
    {
        return array(
            array(false),
            array(0xBAD),
            array(0.1),
            array(array()),
            array(new \stdClass()),
            array(tmpfile()),
            array(function() {}),
            array(NAN),
        );
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider nonStringTypeProvider
     */
    public function IsValid_ConcreteInvalidType_ReturnsFalse($concrete)
    {
        $definition['concrete'] = $concrete;

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     * @dataProvider nonStringTypeProvider
     */
    public function GetErrors_ConcreteInvalidType_ReturnsError($concrete)
    {
        $definition['concrete'] = $concrete;

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::CONCRETE_INVALID_TYPE, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_ConcreteClassNotExists_ReturnsFalse()
    {
        $definition['concrete'] = '\nonExistent';

        $this->assertFalse($this->validator->isValid($definition));

    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     */
    public function GetErrors_ConcreteClassNotExists_ReturnsError()
    {
        $definition['concrete'] = '\nonExistent';

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::CONCRETE_CLASS_NOT_FOUND, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider nonStringTypeProvider
     */
    public function IsValid_AbstractInvalidType_ReturnsFalse($abstract)
    {
        $definition['concrete'] = '\stdClass';
        $definition['abstract'] = $abstract;

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_AbstractValidValue_ReturnsTrue()
    {
        $definition['concrete'] = '\ArrayObject';
        $definition['abstract'] = '\ArrayAccess';

        $this->assertTrue($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     * @dataProvider nonStringTypeProvider
     */
    public function GetErrors_AbstractInvalidType_ReturnsError($abstract)
    {
        $definition['concrete'] = '\stdClass';
        $definition['abstract'] = $abstract;

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::ABSTRACT_INVALID_TYPE, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_AbstractInterfaceNotExists_ReturnsFalse()
    {
        $definition['concrete'] = '\stdClass';
        $definition['abstract'] = '\nonExistent';

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     */
    public function GetErrors_AbstractInterfaceNotExists_ReturnsError()
    {
        $definition['concrete'] = '\stdClass';
        $definition['abstract'] = '\nonExistent';

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::ABSTRACT_INTERFACE_NOT_FOUND, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_AbstractEmptyString_ReturnsFalse()
    {
        $definition['concrete'] = '\stdClass';
        $definition['abstract'] = '';

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_AbstractBaseclassMismatch_ReturnsFalse()
    {
        $definition['concrete'] = '\stdClass';
        $definition['abstract'] = '\ArrayAccess';

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     */
    public function GetErrors_AbstractBaseclassMismatch_ReturnsError()
    {
        $definition['concrete'] = '\stdClass';
        $definition['abstract'] = '\ArrayAccess';

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::ABSTRACT_BASECLASS_MISMATCH, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider nonStringTypeProvider
     */
    public function IsValid_NameInvalidType_ReturnsFalse($name)
    {
        $definition['concrete'] = '\stdClass';
        $definition['name'] = $name;

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     * @dataProvider nonStringTypeProvider
     */
    public function GetErrors_NameInvalidType_ReturnsError($name)
    {
        $definition['concrete'] = '\stdClass';
        $definition['name'] = $name;

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::NAME_INVALID_TYPE, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_NameValidValue_ReturnsTrue()
    {
        $definition['concrete'] = '\stdClass';
        $definition['name'] = 'validName';

        $this->assertTrue($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     */
    public function GetErrors_CalledTwice_ReturnsSameResult()
    {
        $definition = array();

        $this->assertFalse($this->validator->isValid($definition));
        $first = $this->validator->getDefinitionErrors();

        $this->assertFalse($this->validator->isValid($definition));
        $second = $this->validator->getDefinitionErrors();

        $this->assertSame($first, $second);
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider nonStringTypeProvider
     */
    public function IsValid_LifetimeInvalidType_ReturnsFalse($lifetime)
    {
        $definition['concrete'] = '\stdClass';
        $definition['lifetime'] = $lifetime;

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     * @dataProvider nonStringTypeProvider
     */
    public function GetErrors_LifetimeInvalidType_ReturnsError($lifetime)
    {
        $definition['concrete'] = '\stdClass';
        $definition['lifetime'] = $lifetime;

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::LIFETIME_INVALID_TYPE, $errors);
    }

    public function validLifetimeValueProvider()
    {
        return array(
            array('transient'),
            array('singleton'),
        );
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider validLifetimeValueProvider
     */
    public function IsValid_LifetimeValidValue_ReturnsTrue($lifetime)
    {
        $definition['concrete'] = '\stdClass';
        $definition['lifetime'] = $lifetime;

        $this->assertTrue($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_LifetimeInvalidValue_ReturnsFalse()
    {
        $definition['concrete'] = '\stdClass';
        $definition['lifetime'] = 'invalid';

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     */
    public function GetErrors_LifetimeInvalidValue_ReturnsError()
    {
        $definition['concrete'] = '\stdClass';
        $definition['lifetime'] = 'invalid';

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::LIFETIME_INVALID_VALUE, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider nonStringTypeProvider
     */
    public function IsValid_AutowireInvalidType_ReturnsFalse($autowire)
    {
        $definition['concrete'] = '\stdClass';
        $definition['autowire'] = $autowire;

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     * @dataProvider nonStringTypeProvider
     */
    public function GetErrors_AutowireInvalidType_ReturnsError($autowire)
    {
        $definition['concrete'] = '\stdClass';
        $definition['autowire'] = $autowire;

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::AUTOWIRE_INVALID_TYPE, $errors);
    }

    public function validAutowireValueProvider()
    {
        return array(
            array('none'),
            array('auto'),
            array('name'),
            array('type'),
        );
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider validAutowireValueProvider
     */
    public function IsValid_AutowireValidValue_ReturnsTrue($autowire)
    {
        $definition['concrete'] = '\stdClass';
        $definition['autowire'] = $autowire;

        $this->assertTrue($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_AutowireInvalidValue_ReturnsFalse()
    {
        $definition['concrete'] = '\stdClass';
        $definition['autowire'] = 'invalid';

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     */
    public function GetErrors_AutowireInvalidValue_ReturnsError()
    {
        $definition['concrete'] = '\stdClass';
        $definition['autowire'] = 'invalid';

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::AUTOWIRE_INVALID_VALUE, $errors);
    }

    public function nonBooleanTypeProvider()
    {
        return array(
            array(0xBAD),
            array(0.1),
            array('invalid'),
            array(array()),
            array(new \stdClass()),
            array(tmpfile()),
            array(function() {}),
            array(NAN),
        );
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider nonBooleanTypeProvider
     */
    public function IsValid_CandidateInvalidType_ReturnsFalse($candidate)
    {
        $definition['concrete'] = '\stdClass';
        $definition['candidate'] = $candidate;

        $this->assertFalse($this->validator->isValid($definition));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getDefinitionErrors
     * @dataProvider nonBooleanTypeProvider
     */
    public function GetErrors_CandidateInvalidType_ReturnsError($candidate)
    {
        $definition['concrete'] = '\stdClass';
        $definition['candidate'] = $candidate;

        $this->validator->isValid($definition);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(DefinitionValidator::CANDIDATE_INVALID_TYPE, $errors);
    }

    public function validCandidateValueProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider validCandidateValueProvider
     */
    public function IsValid_CandidateValidValue_ReturnsTrue($candidate)
    {
        $definition['concrete'] = '\stdClass';
        $definition['candidate'] = $candidate;

        $this->assertTrue($this->validator->isValid($definition));
    }

    public function invalidNameProvider()
    {
        return array(
            array('0invalid'),
            array('i nvalid'),
            array("\x00"),
            array("i\x00"),
            array('inv-alid'),
        );
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider invalidNameProvider
     */
    public function IsValid_InvalidNameValue_ReturnsFalse($name)
    {
        $parameter['concrete'] = '\stdClass';
        $parameter['name'] = $name;

        $this->assertFalse($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getErrors
     * @dataProvider invalidNameProvider
     */
    public function GetErrors_InvalidNameValue_ReturnsError($name)
    {
        $parameter['concrete'] = '\stdClass';
        $parameter['name'] = $name;

        $this->validator->isValid($parameter);

        $errors = $this->validator->getDefinitionErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::NAME_INVALID_VALUE, $errors);
    }
}