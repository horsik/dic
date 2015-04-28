<?php

namespace Kampaw\Dic\Definition\Validator;

/**
 * @coversDefaultClass Kampaw\Dic\Definition\Validator\MutatorValidator
 * @covers ::<!public>
 */
class MutatorValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MutatorValidator $validator
     */
    private $validator;

    public function setUp()
    {
        $this->validator = new MutatorValidator();
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
    public function IsValid_InvalidParameterType_ReturnsFalse($mutator)
    {
        $this->assertFalse($this->validator->isValid($mutator));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::GetErrors
     * @dataProvider nonArrayTypeProvider
     */
    public function GetErrors_InvalidParameterType_ReturnsError($mutator)
    {
        $this->validator->isValid($mutator);

        $errors = $this->validator->getErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::ARGUMENT_NOT_AN_ARRAY, $errors);
    }
    
    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_NameKeyMissing_ReturnsFalse()
    {
        $parameter = array();

        $this->assertFalse($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::GetErrors
     */
    public function GetErrors_NameKeyMissing_ReturnsError()
    {
        $parameter = array();

        $this->validator->isValid($parameter);

        $errors = $this->validator->getErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::NAME_KEY_MISSING, $errors);
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
    public function IsValid_NameInvalidType_ReturnsFalse($name)
    {
        $parameter['name'] = $name;

        $this->assertFalse($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getErrors
     * @dataProvider nonStringTypeProvider
     */
    public function GetErrors_NameInvalidType_ReturnsError($name)
    {
        $parameter['name'] = $name;
        
        $this->validator->isValid($parameter);

        $errors = $this->validator->getErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::NAME_INVALID_TYPE, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_NameValidValue_ReturnsTrue()
    {
        $parameter['name'] = 'validName';

        $this->assertTrue($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_NameEmptyString_ReturnsFalse()
    {
        $parameter['name'] = '';

        $this->assertFalse($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getErrors
     */
    public function GetErrors_NameEmptyString_ReturnsError()
    {
        $parameter['name'] = '';

        $this->validator->isValid($parameter);

        $errors = $this->validator->getErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::NAME_EMPTY_STRING, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getErrors
     */
    public function GetErrors_CalledTwice_ReturnsSameResult()
    {
        $parameter = array();

        $this->assertFalse($this->validator->isValid($parameter));
        $first = $this->validator->getErrors();

        $this->assertFalse($this->validator->isValid($parameter));
        $second = $this->validator->getErrors();

        $this->assertSame($first, $second);
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
        $parameter['name'] = $name;

        $this->validator->isValid($parameter);

        $errors = $this->validator->getErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::NAME_INVALID_VALUE, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider nonStringTypeProvider
     */
    public function IsValid_TypeInvalidType_ReturnsFalse($type)
    {
        $parameter['name'] = 'validName';
        $parameter['type'] = $type;

        $this->assertFalse($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getErrors
     * @dataProvider nonStringTypeProvider
     */
    public function GetErrors_TypeInvalidType_ReturnsError($type)
    {
        $parameter['name'] = 'validName';
        $parameter['type'] = $type;

        $this->validator->isValid($parameter);

        $errors = $this->validator->getErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::TYPE_INVALID_TYPE, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_TypeClassNotExists_ReturnsFalse()
    {
        $parameter['name'] = 'validName';
        $parameter['type'] = '\nonExistent';

        $this->assertFalse($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getErrors
     */
    public function GetErrors_TypeClassNotExists_ReturnsError()
    {
        $parameter['name'] = 'validName';
        $parameter['type'] = '\nonExistent';

        $this->validator->isValid($parameter);

        $errors = $this->validator->getErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::TYPE_CLASS_NOT_FOUND, $errors);
    }

    public function validTypeProvider()
    {
        return array(
            array('\ArrayObject'),
            array('\ArrayAccess'),
        );
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider validTypeProvider
     */
    public function IsValid_TypeValidValue_ReturnsTrue($type)
    {
        $parameter['name'] = 'validName';
        $parameter['type'] = $type;

        $this->assertTrue($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_TypeEmptyString_ReturnsFalse()
    {
        $parameter['name'] = 'validName';
        $parameter['type'] = '';

        $this->assertFalse($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     * @dataProvider nonStringTypeProvider
     */
    public function IsValid_RefInvalidType_ReturnsFalse($ref)
    {
        $parameter['name'] = 'validName';
        $parameter['ref'] = $ref;

        $this->assertFalse($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getErrors
     * @dataProvider nonStringTypeProvider
     */
    public function GetErrors_RefInvalidType_ReturnsError($ref)
    {
        $parameter['name'] = 'validName';
        $parameter['ref'] = $ref;

        $this->validator->isValid($parameter);

        $errors = $this->validator->getErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::REF_INVALID_TYPE, $errors);
    }

    /**
     * @test
     * @covers ::isValid
     */
    public function IsValid_RefValidValue_ReturnsTrue()
    {
        $parameter['name'] = 'validName';
        $parameter['ref'] = 'validRef';

        $this->assertTrue($this->validator->isValid($parameter));
    }
    
    /**
     * @test
     * @covers ::isValid
     * @dataProvider invalidNameProvider
     */
    public function IsValid_InvalidRefValue_ReturnsFalse($name)
    {
        $parameter['name'] = 'validName';
        $parameter['ref'] = $name;

        $this->assertFalse($this->validator->isValid($parameter));
    }

    /**
     * @test
     * @covers ::isValid
     * @covers ::getErrors
     * @dataProvider invalidNameProvider
     */
    public function GetErrors_InvalidRefValue_ReturnsError($name)
    {
        $parameter['name'] = 'validName';
        $parameter['ref'] = $name;

        $this->validator->isValid($parameter);

        $errors = $this->validator->getErrors();

        $this->assertCount(1, $errors);
        $this->assertContains(MutatorValidator::REF_INVALID_VALUE, $errors);
    }
}