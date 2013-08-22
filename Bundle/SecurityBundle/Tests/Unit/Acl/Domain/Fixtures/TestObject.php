<?php

namespace Oro\Bundle\SecurityBundle\Tests\Unit\Acl\Domain\Fixtures;

class TestObject
{
    private $id;

    private $owner;

    public function __construct($id, $owner = null)
    {
        $this->id = $id;
        $this->owner = $owner;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }
}
