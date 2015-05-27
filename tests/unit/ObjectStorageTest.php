<?php

namespace Kampaw\Dic;

class ObjectStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectStorage $failsafe
     */
    private $failsafe;

    public function setUp()
    {
        $this->failsafe = new ObjectStorage();
    }

    /**
     * @test
     */
    public function End_EmptyMap_ReturnsFalse()
    {
        $this->assertFalse($this->failsafe->end());
    }

    /**
     * @test
     */
    public function End_OneElementAttached_ReturnsLastElement()
    {
        $o = new \stdClass();

        $this->failsafe->attach($o);

        $this->assertSame($o, $this->failsafe->end());
    }

    /**
     * @test
     */
    public function End_TwoElementsAttached_ReturnsLastElement()
    {
        $o = new \stdClass();
        $p = new \stdClass();

        $this->failsafe->attach($o);
        $this->failsafe->attach($p);

        $this->assertSame($p, $this->failsafe->end());
    }

    /**
     * @test
     */
    public function Contains_AttachedElement_ReturnsTrue()
    {
        $o = new \stdClass();

        $this->failsafe->attach($o);

        $this->assertTrue($this->failsafe->contains($o));
    }

    /**
     * @test
     */
    public function Contains_NotAttachedElement_ReturnsFalse()
    {
        $o = new \stdClass();

        $this->assertFalse($this->failsafe->contains($o));
    }

    /**
     * @test
     */
    public function Detach_AttachedElement_ElementRemoved()
    {
        $o = new \stdClass();

        $this->failsafe->attach($o);
        $this->failsafe->detach($o);

        $this->assertFalse($this->failsafe->contains($o));
    }

    /**
     * @test
     */
    public function Slice_EmptyMap_ReturnsEmptyArray()
    {
        $o = new \stdClass();

        $this->assertCount(0, $this->failsafe->slice($o));
    }

    /**
     * @test
     */
    public function Slice_OneElementAttached_ReturnsOneElement()
    {
        $o = new \stdClass();

        $this->failsafe->attach($o);

        $trace = $this->failsafe->slice($o);

        $this->assertCount(1, $trace);
        $this->contains($o);
    }

    /**
     * @test
     */
    public function Slice_TwoElementsAttachedOffsetSecondElement_ReturnsSecondElement()
    {
        $o = new \stdClass();
        $p = new \stdClass();

        $this->failsafe->attach($o);
        $this->failsafe->attach($p);

        $trace = $this->failsafe->slice($p);

        $this->assertCount(1, $trace);
        $this->contains($p);
    }
}