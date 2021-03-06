<?php

namespace spec\Pim\Bundle\BaseConnectorBundle\Writer\Doctrine;

use Akeneo\Component\Batch\Model\StepExecution;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\Model\CategoryInterface;
use Pim\Bundle\TransformBundle\Cache\CacheClearer;
use Prophecy\Argument;

class WriterSpec extends ObjectBehavior
{
    function let(ManagerRegistry $registry, CacheClearer $clearer, StepExecution $stepExecution)
    {
        $this->beConstructedWith($registry, $clearer);
        $this->setStepExecution($stepExecution);
    }

    function it_is_a_configurable_step_execution_aware_writer()
    {
        $this->shouldBeAnInstanceOf('Akeneo\Component\Batch\Item\AbstractConfigurableStepElement');
        $this->shouldImplement('Akeneo\Component\Batch\Item\ItemWriterInterface');
        $this->shouldImplement('Akeneo\Component\Batch\Step\StepExecutionAwareInterface');
    }

    function it_massively_insert_and_update_objects(
        $registry,
        $clearer,
        $stepExecution,
        ObjectManager $manager,
        CategoryInterface $object1,
        CategoryInterface $object2
    ) {
        $registry->getManagerForClass(Argument::any())->willReturn($manager);
        $manager->persist($object1)->shouldBeCalled();
        $object1->getId()->willReturn(null);
        $stepExecution->incrementSummaryInfo('create')->shouldBeCalled();

        $manager->persist($object2)->shouldBeCalled();
        $object2->getId()->willReturn(42);
        $stepExecution->incrementSummaryInfo('process')->shouldBeCalled();

        $registry->getManagers()->willReturn([$manager]);
        $manager->flush()->shouldBeCalled();

        $this->write([$object1, $object2]);
    }

    function it_throw_exception_when_receiving_anything_else_than_object()
    {
        $this->shouldThrow(new \InvalidArgumentException('Expecting item of type object, got "string"'))
            ->duringWrite(['myRawData']);
    }
}
