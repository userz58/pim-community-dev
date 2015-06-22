<?php

namespace spec\Pim\Component\Catalog\Comparator;

use PhpSpec\ObjectBehavior;

class BooleanComparatorSpec extends ObjectBehavior
{
    function it_is_a_comparator()
    {
        $this->shouldBeAnInstanceOf('Pim\Component\Catalog\Comparator\ComparatorInterface');
    }

    function it_supports_comparison()
    {
        $this->supports('pim_catalog_boolean')->shouldBe(true);
    }

    function it_gets_changes_when_adding_value()
    {
        $changes = ['value' => true, 'locale' => 'en_US', 'scope' => 'ecommerce'];
        $originals = [];

        $this->compare($changes, $originals)->shouldReturn($changes);

        $changes = ['value' => 1, 'locale' => 'en_US', 'scope' => 'ecommerce'];
        $originals = [];

        $this->compare($changes, $originals)->shouldReturn($changes);
    }

    function it_gets_changes_when_changing_value()
    {
        $changes   = ['value' => false, 'locale' => 'en_US', 'scope' => 'ecommerce'];
        $originals = ['value' => true, 'locale' => 'en_US', 'scope' => 'ecommerce'];

        $this->compare($changes, $originals)->shouldReturn($changes);

        $changes   = ['value' => 0, 'locale' => 'en_US', 'scope' => 'ecommerce'];
        $originals = ['value' => true, 'locale' => 'en_US', 'scope' => 'ecommerce'];

        $this->compare($changes, $originals)->shouldReturn($changes);
    }

    function it_returns_null_when_values_are_the_same()
    {
        $changes   = ['value' => 1, 'locale' => 'en_US', 'scope' => 'ecommerce'];
        $originals = ['value' => true, 'locale' => 'en_US', 'scope' => 'ecommerce'];

        $this->compare($changes, $originals)->shouldReturn(null);

        $changes   = ['value' => true, 'locale' => 'en_US', 'scope' => 'ecommerce'];
        $originals = ['value' => true, 'locale' => 'en_US', 'scope' => 'ecommerce'];

        $this->compare($changes, $originals)->shouldReturn(null);
    }
}
