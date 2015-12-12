<?php

namespace HMLB\DateBundle\Tests\Request\ParamConverter;

use HMLB\Date\Date;
use HMLB\DateBundle\Request\ParamConverter\DateParamConverter;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class DateParamConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DateParamConverter
     */
    private $converter;

    /**
     * @var string
     */
    private $dateClass;

    public function setUp()
    {
        $this->converter = new DateParamConverter();
        $this->dateClass = Date::class;
    }

    public function testSupports()
    {
        $config = $this->createConfiguration($this->dateClass);
        $this->assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(__CLASS__);
        $this->assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration();
        $this->assertFalse($this->converter->supports($config));
    }

    public function testApply()
    {
        $request = new Request([], [], ['start' => '2012-07-21 00:00:00']);
        $config = $this->createConfiguration($this->dateClass, 'start');

        $this->converter->apply($request, $config);

        $this->assertInstanceOf($this->dateClass, $request->attributes->get('start'));
        $this->assertEquals('2012-07-21', $request->attributes->get('start')->format('Y-m-d'));
    }

    public function testApplyInvalidDate404Exception()
    {
        $request = new Request([], [], ['start' => 'Invalid DateTime Format']);
        $config = $this->createConfiguration($this->dateClass, 'start');

        $this->setExpectedException(
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            'Invalid date given.'
        );
        $this->converter->apply($request, $config);
    }

    public function testApplyWithFormatInvalidDate404Exception()
    {
        $request = new Request([], [], ['start' => '2012-07-21']);
        $config = $this->createConfiguration($this->dateClass, 'start');
        $config->expects($this->any())->method('getOptions')->will($this->returnValue(['format' => 'd.m.Y']));

        $this->setExpectedException(
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
            'Invalid date given.'
        );
        $this->converter->apply($request, $config);
    }

    public function testApplyOptionalWithEmptyAttribute()
    {
        $request = new Request([], [], ['start' => null]);
        $config = $this->createConfiguration($this->dateClass, 'start');
        $config->expects($this->once())
            ->method('isOptional')
            ->will($this->returnValue(true));

        $this->assertFalse($this->converter->apply($request, $config));
        $this->assertNull($request->attributes->get('start'));
    }

    /**
     * @param null $class
     * @param null $name
     *
     * @return PHPUnit_Framework_MockObject_MockObject|ParamConverter
     */
    public function createConfiguration($class = null, $name = null)
    {
        $config = $this
            ->getMockBuilder('Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter')
            ->disableOriginalConstructor()
            ->getMock();

        if ($name !== null) {
            $config->expects($this->any())->method('getName')->will($this->returnValue($name));
        }
        if ($class !== null) {
            $config->expects($this->any())->method('getClass')->will($this->returnValue($class));
        }

        return $config;
    }
}
