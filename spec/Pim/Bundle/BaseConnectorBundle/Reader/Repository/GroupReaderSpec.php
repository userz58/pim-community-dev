<?php

namespace spec\Pim\Bundle\BaseConnectorBundle\Reader\Repository;

use Akeneo\Component\Batch\Model\StepExecution;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CatalogBundle\Repository\GroupRepositoryInterface;

class GroupReaderSpec extends ObjectBehavior
{
    function let(StepExecution $stepExecution, GroupRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
        $this->setStepExecution($stepExecution);
    }

    function it_is_a_configurable_step_execution_aware_reader()
    {
        $this->shouldBeAnInstanceOf('Akeneo\Component\Batch\Item\AbstractConfigurableStepElement');
        $this->shouldImplement('Akeneo\Component\Batch\Item\ItemReaderInterface');
        $this->shouldImplement('Akeneo\Component\Batch\Step\StepExecutionAwareInterface');
    }

    function it_reads_groups_one_by_one($repository)
    {
        $repository->getAllGroupsExceptVariant()
            ->shouldBeCalled()
            ->willReturn(array('foo', 'bar'));

        $this->read()->shouldReturn('foo');
        $this->read()->shouldReturn('bar');
        $this->read()->shouldReturn(null);
    }

    function it_increments_read_count_for_each_group_reading($stepExecution, $repository)
    {
        $repository->getAllGroupsExceptVariant()
            ->shouldBeCalled()
            ->willReturn(array('foo', 'bar'));

        $stepExecution->incrementSummaryInfo('read')->shouldBeCalledTimes(2);

        $this->read();
        $this->read();
        $this->read();
    }

    function it_does_not_expose_any_configuration_fields()
    {
        $this->getConfigurationFields()->shouldHaveCount(0);
    }
}
