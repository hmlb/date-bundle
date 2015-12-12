<?php

namespace HMLB\DateBundle\Tests\Form\Type;

use HMLB\Date\Date;
use HMLB\DateBundle\Form\Type\DateType;
use HMLB\DateBundle\Tests\DumperCapabilities;
use HMLB\DateBundle\Tests\Functional\AbstractTypeTest;
use Locale;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormError;
use Symfony\Component\Intl\Util\IntlTestHelper;

class DateTypeTest extends AbstractTypeTest
{
    use DumperCapabilities;

    private $defaultTimezone;

    protected function setUp()
    {
        $this->enableGlobalDumpFunction();
        parent::setUp();

        $this->defaultTimezone = date_default_timezone_get();
    }

    protected function tearDown()
    {
        date_default_timezone_set($this->defaultTimezone);
        Locale::setDefault('en');
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testInvalidWidgetOption()
    {
        $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => 'fake_widget',
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testInvalidInputOption()
    {
        $this->factory->create(
            DateType::class,
            null,
            [
                'input' => 'fake_input',
            ]
        );
    }

    public function testSubmitFromSingleTextDateTimeWithDefaultFormat()
    {
        var_dump('instanciating');
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
                'input' => 'datetime',
            ]
        );
        var_dump('submitting');

        $form->submit('2010-06-02');

        $this->assertDateEquals(new Date('2010-06-02 UTC'), $form->getData());
        $this->assertEquals('2010-06-02', $form->getViewData());
    }

    public function testSubmitFromSingleTextDateTime()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => \IntlDateFormatter::MEDIUM,
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
                'input' => 'datetime',
            ]
        );

        $form->submit('2.6.2010');

        $this->assertDateEquals(new Date('2010-06-02 UTC'), $form->getData());
        $this->assertEquals('02.06.2010', $form->getViewData());
    }

    public function testSubmitFromSingleTextString()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => \IntlDateFormatter::MEDIUM,
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
                'input' => 'string',
            ]
        );

        $form->submit('2.6.2010');

        $this->assertEquals('2010-06-02', $form->getData());
        $this->assertEquals('02.06.2010', $form->getViewData());
    }

    public function testSubmitFromSingleTextTimestamp()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => \IntlDateFormatter::MEDIUM,
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
                'input' => 'timestamp',
            ]
        );

        $form->submit('2.6.2010');

        $dateTime = new Date('2010-06-02 UTC');

        $this->assertEquals($dateTime->format('U'), $form->getData());
        $this->assertEquals('02.06.2010', $form->getViewData());
    }

    public function testSubmitFromSingleTextRaw()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => \IntlDateFormatter::MEDIUM,
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
                'input' => 'array',
            ]
        );

        $form->submit('2.6.2010');

        $output = [
            'day' => '2',
            'month' => '6',
            'year' => '2010',
        ];

        $this->assertEquals($output, $form->getData());
        $this->assertEquals('02.06.2010', $form->getViewData());
    }

    public function testSubmitFromText()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'text',
            ]
        );

        $text = [
            'day' => '2',
            'month' => '6',
            'year' => '2010',
        ];

        $form->submit($text);

        $dateTime = new Date('2010-06-02 UTC');

        $this->assertDateEquals($dateTime, $form->getData());
        $this->assertEquals($text, $form->getViewData());
    }

    public function testSubmitFromChoice()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'choice',
            ]
        );

        $text = [
            'day' => '2',
            'month' => '6',
            'year' => '2010',
        ];

        $form->submit($text);

        $dateTime = new Date('2010-06-02 UTC');

        $this->assertDateEquals($dateTime, $form->getData());
        $this->assertEquals($text, $form->getViewData());
    }

    public function testSubmitFromChoiceEmpty()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'choice',
                'required' => false,
            ]
        );

        $text = [
            'day' => '',
            'month' => '',
            'year' => '',
        ];

        $form->submit($text);

        $this->assertNull($form->getData());
        $this->assertEquals($text, $form->getViewData());
    }

    public function testSubmitFromInputDateTimeDifferentPattern()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'format' => 'MM*yyyy*dd',
                'widget' => 'single_text',
                'input' => 'datetime',
            ]
        );

        $form->submit('06*2010*02');

        $this->assertDateEquals(new Date('2010-06-02 UTC'), $form->getData());
        $this->assertEquals('06*2010*02', $form->getViewData());
    }

    public function testSubmitFromInputStringDifferentPattern()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'format' => 'MM*yyyy*dd',
                'widget' => 'single_text',
                'input' => 'string',
            ]
        );

        $form->submit('06*2010*02');

        $this->assertEquals('2010-06-02', $form->getData());
        $this->assertEquals('06*2010*02', $form->getViewData());
    }

    public function testSubmitFromInputTimestampDifferentPattern()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'format' => 'MM*yyyy*dd',
                'widget' => 'single_text',
                'input' => 'timestamp',
            ]
        );

        $form->submit('06*2010*02');

        $dateTime = new Date('2010-06-02 UTC');

        $this->assertEquals($dateTime->format('U'), $form->getData());
        $this->assertEquals('06*2010*02', $form->getViewData());
    }

    public function testSubmitFromInputRawDifferentPattern()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'format' => 'MM*yyyy*dd',
                'widget' => 'single_text',
                'input' => 'array',
            ]
        );

        $form->submit('06*2010*02');

        $output = [
            'day' => '2',
            'month' => '6',
            'year' => '2010',
        ];

        $this->assertEquals($output, $form->getData());
        $this->assertEquals('06*2010*02', $form->getViewData());
    }

    /**
     * @dataProvider provideDateFormats
     */
    public function testDatePatternWithFormatOption($format, $pattern)
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => $format,
            ]
        );

        $view = $form->createView();

        $this->assertEquals($pattern, $view->vars['date_pattern']);
    }

    public function provideDateFormats()
    {
        return [
            ['dMy', '{{ day }}{{ month }}{{ year }}'],
            ['d-M-yyyy', '{{ day }}-{{ month }}-{{ year }}'],
            ['M d y', '{{ month }} {{ day }} {{ year }}'],
        ];
    }

    /**
     * This test is to check that the strings '0', '1', '2', '3' are not accepted
     * as valid IntlDateFormatter constants for FULL, LONG, MEDIUM or SHORT respectively.
     *
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testThrowExceptionIfFormatIsNoPattern()
    {
        $this->factory->create(
            DateType::class,
            null,
            [
                'format' => '0',
                'widget' => 'single_text',
                'input' => 'string',
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testThrowExceptionIfFormatDoesNotContainYearMonthAndDay()
    {
        $this->factory->create(
            DateType::class,
            null,
            [
                'months' => [6, 7],
                'format' => 'yy',
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testThrowExceptionIfFormatIsNoConstant()
    {
        $this->factory->create(
            DateType::class,
            null,
            [
                'format' => 105,
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testThrowExceptionIfFormatIsInvalid()
    {
        $this->factory->create(
            DateType::class,
            null,
            [
                'format' => [],
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testThrowExceptionIfYearsIsInvalid()
    {
        $this->factory->create(
            DateType::class,
            null,
            [
                'years' => 'bad value',
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testThrowExceptionIfMonthsIsInvalid()
    {
        $this->factory->create(
            DateType::class,
            null,
            [
                'months' => 'bad value',
            ]
        );
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testThrowExceptionIfDaysIsInvalid()
    {
        $this->factory->create(
            DateType::class,
            null,
            [
                'days' => 'bad value',
            ]
        );
    }

    public function testSetDataWithNegativeTimezoneOffsetStringInput()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => \IntlDateFormatter::MEDIUM,
                'model_timezone' => 'UTC',
                'view_timezone' => 'America/New_York',
                'input' => 'string',
                'widget' => 'single_text',
            ]
        );

        $form->setData('2010-06-02');

        // 2010-06-02 00:00:00 UTC
        // 2010-06-01 20:00:00 UTC-4
        $this->assertEquals('01.06.2010', $form->getViewData());
    }

    public function testSetDataWithNegativeTimezoneOffsetDateTimeInput()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => \IntlDateFormatter::MEDIUM,
                'model_timezone' => 'UTC',
                'view_timezone' => 'America/New_York',
                'input' => 'datetime',
                'widget' => 'single_text',
            ]
        );

        $dateTime = new Date('2010-06-02 UTC');

        $form->setData($dateTime);

        // 2010-06-02 00:00:00 UTC
        // 2010-06-01 20:00:00 UTC-4
        $this->assertDateEquals($dateTime, $form->getData());
        $this->assertEquals('01.06.2010', $form->getViewData());
    }

    public function testYearsOption()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'years' => [2010, 2011],
            ]
        );

        $view = $form->createView();

        $this->assertEquals(
            [
                new ChoiceView('2010', '2010', '2010'),
                new ChoiceView('2011', '2011', '2011'),
            ],
            $view['year']->vars['choices']
        );
    }

    public function testMonthsOption()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'months' => [6, 7],
                'format' => \IntlDateFormatter::SHORT,
            ]
        );

        $view = $form->createView();

        $this->assertEquals(
            [
                new ChoiceView(6, '6', '06'),
                new ChoiceView(7, '7', '07'),
            ],
            $view['month']->vars['choices']
        );
    }

    public function testMonthsOptionShortFormat()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'months' => [1, 4],
                'format' => 'dd.MMM.yy',
            ]
        );

        $view = $form->createView();

        $this->assertEquals(
            [
                new ChoiceView(1, '1', 'Jän'),
                new ChoiceView(4, '4', 'Apr.'),
            ],
            $view['month']->vars['choices']
        );
    }

    public function testMonthsOptionLongFormat()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'months' => [1, 4],
                'format' => 'dd.MMMM.yy',
            ]
        );

        $view = $form->createView();

        $this->assertEquals(
            [
                new ChoiceView(1, '1', 'Jänner'),
                new ChoiceView(4, '4', 'April'),
            ],
            $view['month']->vars['choices']
        );
    }

    public function testMonthsOptionLongFormatWithDifferentTimezone()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'months' => [1, 4],
                'format' => 'dd.MMMM.yy',
            ]
        );

        $view = $form->createView();

        $this->assertEquals(
            [
                new ChoiceView(1, '1', 'Jänner'),
                new ChoiceView(4, '4', 'April'),
            ],
            $view['month']->vars['choices']
        );
    }

    public function testIsDayWithinRangeReturnsTrueIfWithin()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'days' => [6, 7],
            ]
        );

        $view = $form->createView();

        $this->assertEquals(
            [
                new ChoiceView(6, '6', '06'),
                new ChoiceView(7, '7', '07'),
            ],
            $view['day']->vars['choices']
        );
    }

    public function testIsPartiallyFilledReturnsFalseIfSingleText()
    {
        $this->markTestIncomplete('Needs to be reimplemented using validators');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
            ]
        );

        $form->submit('7.6.2010');

        $this->assertFalse($form->isPartiallyFilled());
    }

    public function testIsPartiallyFilledReturnsFalseIfChoiceAndCompletelyEmpty()
    {
        $this->markTestIncomplete('Needs to be reimplemented using validators');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'choice',
            ]
        );

        $form->submit(
            [
                'day' => '',
                'month' => '',
                'year' => '',
            ]
        );

        $this->assertFalse($form->isPartiallyFilled());
    }

    public function testIsPartiallyFilledReturnsFalseIfChoiceAndCompletelyFilled()
    {
        $this->markTestIncomplete('Needs to be reimplemented using validators');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'choice',
            ]
        );

        $form->submit(
            [
                'day' => '2',
                'month' => '6',
                'year' => '2010',
            ]
        );

        $this->assertFalse($form->isPartiallyFilled());
    }

    public function testIsPartiallyFilledReturnsTrueIfChoiceAndDayEmpty()
    {
        $this->markTestIncomplete('Needs to be reimplemented using validators');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'choice',
            ]
        );

        $form->submit(
            [
                'day' => '',
                'month' => '6',
                'year' => '2010',
            ]
        );

        $this->assertTrue($form->isPartiallyFilled());
    }

    public function testPassDatePatternToView()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(DateType::class);
        $view = $form->createView();

        $this->assertSame('{{ day }}{{ month }}{{ year }}', $view->vars['date_pattern']);
    }

    public function testPassDatePatternToViewDifferentFormat()
    {
        // we test against "de_AT", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('de_AT');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => \IntlDateFormatter::LONG,
            ]
        );

        $view = $form->createView();

        $this->assertSame('{{ day }}{{ month }}{{ year }}', $view->vars['date_pattern']);
    }

    public function testPassDatePatternToViewDifferentPattern()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => 'MMyyyydd',
            ]
        );

        $view = $form->createView();

        $this->assertSame('{{ month }}{{ year }}{{ day }}', $view->vars['date_pattern']);
    }

    public function testPassDatePatternToViewDifferentPatternWithSeparators()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'format' => 'MM*yyyy*dd',
            ]
        );

        $view = $form->createView();

        $this->assertSame('{{ month }}*{{ year }}*{{ day }}', $view->vars['date_pattern']);
    }

    public function testDontPassDatePatternIfText()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => 'single_text',
            ]
        );
        $view = $form->createView();

        $this->assertFalse(isset($view->vars['date_pattern']));
    }

    public function testDatePatternFormatWithQuotedStrings()
    {
        // we test against "es_ES", so we need the full implementation
        IntlTestHelper::requireFullIntl($this);

        \Locale::setDefault('es_ES');

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                // EEEE, d 'de' MMMM 'de' y
                'format' => \IntlDateFormatter::FULL,
            ]
        );

        $view = $form->createView();

        $this->assertEquals('{{ day }}{{ month }}{{ year }}', $view->vars['date_pattern']);
    }

    public function testPassWidgetToView()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => 'single_text',
            ]
        );
        $view = $form->createView();

        $this->assertSame('single_text', $view->vars['widget']);
    }

    public function testInitializeWithDateTime()
    {
        // Throws an exception if "data_class" option is not explicitly set
        // to null in the type
        $this->factory->create(DateType::class, new Date());
    }

    public function testSingleTextWidgetShouldUseTheRightInputType()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => 'single_text',
            ]
        );

        $view = $form->createView();
        $this->assertEquals(DateType::class, $view->vars['type']);
    }

    public function testPassDefaultPlaceholderToViewIfNotRequired()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'required' => false,
            ]
        );

        $view = $form->createView();
        $this->assertSame('', $view['year']->vars['placeholder']);
        $this->assertSame('', $view['month']->vars['placeholder']);
        $this->assertSame('', $view['day']->vars['placeholder']);
    }

    public function testPassNoPlaceholderToViewIfRequired()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'required' => true,
            ]
        );

        $view = $form->createView();
        $this->assertNull($view['year']->vars['placeholder']);
        $this->assertNull($view['month']->vars['placeholder']);
        $this->assertNull($view['day']->vars['placeholder']);
    }

    public function testPassPlaceholderAsString()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'placeholder' => 'Empty',
            ]
        );

        $view = $form->createView();
        $this->assertSame('Empty', $view['year']->vars['placeholder']);
        $this->assertSame('Empty', $view['month']->vars['placeholder']);
        $this->assertSame('Empty', $view['day']->vars['placeholder']);
    }

    /**
     * @group legacy
     */
    public function testPassEmptyValueBC()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'empty_value' => 'Empty',
            ]
        );

        $view = $form->createView();
        $this->assertSame('Empty', $view['year']->vars['placeholder']);
        $this->assertSame('Empty', $view['month']->vars['placeholder']);
        $this->assertSame('Empty', $view['day']->vars['placeholder']);
        $this->assertSame('Empty', $view['year']->vars['empty_value']);
        $this->assertSame('Empty', $view['month']->vars['empty_value']);
        $this->assertSame('Empty', $view['day']->vars['empty_value']);
    }

    public function testPassPlaceholderAsArray()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'placeholder' => [
                    'year' => 'Empty year',
                    'month' => 'Empty month',
                    'day' => 'Empty day',
                ],
            ]
        );

        $view = $form->createView();
        $this->assertSame('Empty year', $view['year']->vars['placeholder']);
        $this->assertSame('Empty month', $view['month']->vars['placeholder']);
        $this->assertSame('Empty day', $view['day']->vars['placeholder']);
    }

    public function testPassPlaceholderAsPartialArrayAddEmptyIfNotRequired()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'required' => false,
                'placeholder' => [
                    'year' => 'Empty year',
                    'day' => 'Empty day',
                ],
            ]
        );

        $view = $form->createView();
        $this->assertSame('Empty year', $view['year']->vars['placeholder']);
        $this->assertSame('', $view['month']->vars['placeholder']);
        $this->assertSame('Empty day', $view['day']->vars['placeholder']);
    }

    public function testPassPlaceholderAsPartialArrayAddNullIfRequired()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'required' => true,
                'placeholder' => [
                    'year' => 'Empty year',
                    'day' => 'Empty day',
                ],
            ]
        );

        $view = $form->createView();
        $this->assertSame('Empty year', $view['year']->vars['placeholder']);
        $this->assertNull($view['month']->vars['placeholder']);
        $this->assertSame('Empty day', $view['day']->vars['placeholder']);
    }

    public function testPassHtml5TypeIfSingleTextAndHtml5Format()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => 'single_text',
            ]
        );

        $view = $form->createView();
        $this->assertSame(DateType::class, $view->vars['type']);
    }

    public function testDontPassHtml5TypeIfHtml5NotAllowed()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => 'single_text',
                'html5' => false,
            ]
        );

        $view = $form->createView();
        $this->assertFalse(isset($view->vars['type']));
    }

    public function testDontPassHtml5TypeIfNotHtml5Format()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => 'single_text',
                'format' => \IntlDateFormatter::MEDIUM,
            ]
        );

        $view = $form->createView();
        $this->assertFalse(isset($view->vars['type']));
    }

    public function testDontPassHtml5TypeIfNotSingleText()
    {
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => 'text',
            ]
        );

        $view = $form->createView();
        $this->assertFalse(isset($view->vars['type']));
    }

    public function provideCompoundWidgets()
    {
        return [
            ['text'],
            ['choice'],
        ];
    }

    /**
     * @dataProvider provideCompoundWidgets
     */
    public function testYearErrorsBubbleUp($widget)
    {
        $error = new FormError('Invalid!');
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => $widget,
            ]
        );
        $form['year']->addError($error);

        $this->assertSame([], iterator_to_array($form['year']->getErrors()));
        $this->assertSame([$error], iterator_to_array($form->getErrors()));
    }

    /**
     * @dataProvider provideCompoundWidgets
     */
    public function testMonthErrorsBubbleUp($widget)
    {
        $error = new FormError('Invalid!');
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => $widget,
            ]
        );
        $form['month']->addError($error);

        $this->assertSame([], iterator_to_array($form['month']->getErrors()));
        $this->assertSame([$error], iterator_to_array($form->getErrors()));
    }

    /**
     * @dataProvider provideCompoundWidgets
     */
    public function testDayErrorsBubbleUp($widget)
    {
        $error = new FormError('Invalid!');
        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'widget' => $widget,
            ]
        );
        $form['day']->addError($error);

        $this->assertSame([], iterator_to_array($form['day']->getErrors()));
        $this->assertSame([$error], iterator_to_array($form->getErrors()));
    }

    public function testYearsFor32BitsMachines()
    {
        if (4 !== PHP_INT_SIZE) {
            $this->markTestSkipped('PHP 32 bit is required.');
        }

        $form = $this->factory->create(
            DateType::class,
            null,
            [
                'years' => range(1900, 2040),
            ]
        );

        $view = $form->createView();

        $listChoices = [];
        foreach (range(1902, 2037) as $y) {
            $listChoices[] = new ChoiceView($y, $y, $y);
        }

        $this->assertEquals($listChoices, $view['year']->vars['choices']);
    }

    /**
     * @param Date $expected
     * @param Date $actual
     */
    protected function assertDateEquals(Date $expected, $actual)
    {
        $this->assertInstanceOf(Date::class, $actual);
        $this->assertEquals($expected->toIso8601String(), $actual->toIso8601String());
    }
}
