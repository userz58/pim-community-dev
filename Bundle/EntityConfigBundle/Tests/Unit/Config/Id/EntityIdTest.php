<?php

namespace Oro\Bundle\EntityConfigBundle\Tests\Unit\Config\Id;

use Oro\Bundle\EntityConfigBundle\Config\Id\EntityId;

class EntityIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityId
     */
    protected $entityId;

    public function setUp()
    {
        $this->entityId = new EntityId('Test\Class', 'testScope');
    }

    public function testGetConfig()
    {
        $this->assertEquals('Test\Class', $this->entityId->getClassName());
        $this->assertEquals('testScope', $this->entityId->getScope());
        $this->assertEquals('entity_testScope_Test_Class', $this->entityId->getId());
    }

    public function testSerialize()
    {
        $this->assertEquals($this->entityId, unserialize(serialize($this->entityId)));
    }
}
