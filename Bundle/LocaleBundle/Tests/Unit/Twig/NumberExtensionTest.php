<?php

namespace Oro\Bundle\LocaleBundle\Tests\Unit\Model;

use Oro\Bundle\LocaleBundle\Twig\NumberExtension;

class NumberExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NumberExtension
     */
    protected $extension;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = $this->getMockBuilder('Oro\Bundle\LocaleBundle\Formatter\NumberFormatter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->extension = new NumberExtension($this->formatter);
    }

    public function testGetFilters()
    {
        $filters = $this->extension->getFilters();
        $this->assertArrayContainsHasFilter('oro_number_format', 'format', $filters);
        $this->assertArrayContainsHasFilter('oro_number_format_currency', 'formatCurrency', $filters);
        $this->assertArrayContainsHasFilter('oro_number_format_decimal', 'formatDecimal', $filters);
        $this->assertArrayContainsHasFilter('oro_number_format_percent', 'formatPercent', $filters);
        $this->assertArrayContainsHasFilter('oro_number_format_spellout', 'formatSpellout', $filters);
        $this->assertArrayContainsHasFilter('oro_number_format_duration', 'formatDuration', $filters);
        $this->assertArrayContainsHasFilter('oro_number_format_ordinal', 'formatOrdinal', $filters);
    }

    protected function assertArrayContainsHasFilter($filterName, $extensionMethod, array $filters)
    {
        $this->assertArrayHasKey($filterName, $filters);
        /** @var \Twig_Filter_Method $filter */
        $filter = $filters[$filterName];
        $this->assertInstanceOf('Twig_Filter_Method', $filter);
        $this->assertAttributeEquals($extensionMethod, 'method', $filter);
    }

    public function testFormat()
    {
        $value = 1234.5;
        $style = 'decimal';
        $attributes = array('grouping_size' => 3);
        $textAttributes = array('grouping_separator_symbol' => ',');
        $locale = 'fr_CA';
        $options = array('attributes' => $attributes, 'textAttributes' => $textAttributes, 'locale' => $locale);
        $expectedResult = '1,234.45';

        $this->formatter->expects($this->once())->method('format')
            ->with($value, $style, $attributes, $textAttributes, $locale)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->extension->format($value, $style, $options));
    }

    public function testFormatCurrency()
    {
        $value = 1234.5;
        $currency = 'USD';
        $attributes = array('grouping_size' => 3);
        $textAttributes = array('grouping_separator_symbol' => ',');
        $locale = 'en_US';
        $options = array(
            'currency' => $currency,
            'attributes' => $attributes,
            'textAttributes' => $textAttributes,
            'locale' => $locale
        );
        $expectedResult = '$1,234.45';

        $this->formatter->expects($this->once())->method('formatCurrency')
            ->with($value, $currency, $attributes, $textAttributes, $locale)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->extension->formatCurrency($value, $options));
    }

    public function testFormatDecimal()
    {
        $value = 1234.5;
        $attributes = array('grouping_size' => 3);
        $textAttributes = array('grouping_separator_symbol' => ',');
        $locale = 'en_US';
        $options = array(
            'attributes' => $attributes,
            'textAttributes' => $textAttributes,
            'locale' => $locale
        );
        $expectedResult = '1,234.45';

        $this->formatter->expects($this->once())->method('formatDecimal')
            ->with($value, $attributes, $textAttributes, $locale)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->extension->formatDecimal($value, $options));
    }

    public function testFormatPercent()
    {
        $value = 99;
        $attributes = array('grouping_size' => 3);
        $textAttributes = array('grouping_separator_symbol' => ',');
        $locale = 'en_US';
        $options = array(
            'attributes' => $attributes,
            'textAttributes' => $textAttributes,
            'locale' => $locale
        );
        $expectedResult = '99%';

        $this->formatter->expects($this->once())->method('formatPercent')
            ->with($value, $attributes, $textAttributes, $locale)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->extension->formatPercent($value, $options));
    }

    public function testFormatSpellout()
    {
        $value = 1;
        $attributes = array('foo' => 1);
        $textAttributes = array('bar' => 'baz');
        $locale = 'en_US';
        $options = array(
            'attributes' => $attributes,
            'textAttributes' => $textAttributes,
            'locale' => $locale
        );
        $expectedResult = 'one';

        $this->formatter->expects($this->once())->method('formatSpellout')
            ->with($value, $attributes, $textAttributes, $locale)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->extension->formatSpellout($value, $options));
    }

    public function testFormatDuration()
    {
        $value = 1;
        $attributes = array('foo' => 1);
        $textAttributes = array('bar' => 'baz');
        $locale = 'en_US';
        $options = array(
            'attributes' => $attributes,
            'textAttributes' => $textAttributes,
            'locale' => $locale
        );
        $expectedResult = '1 sec';

        $this->formatter->expects($this->once())->method('formatDuration')
            ->with($value, $attributes, $textAttributes, $locale)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->extension->formatDuration($value, $options));
    }

    public function testFormatOrdinal()
    {
        $value = 1;
        $attributes = array('foo' => 1);
        $textAttributes = array('bar' => 'baz');
        $locale = 'en_US';
        $options = array(
            'attributes' => $attributes,
            'textAttributes' => $textAttributes,
            'locale' => $locale
        );
        $expectedResult = '1st';

        $this->formatter->expects($this->once())->method('formatOrdinal')
            ->with($value, $attributes, $textAttributes, $locale)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->extension->formatOrdinal($value, $options));
    }
}
