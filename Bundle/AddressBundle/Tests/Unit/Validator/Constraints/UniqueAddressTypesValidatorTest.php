<?php

namespace Oro\Bundle\AddressBundle\Tests\Unit\Validator\Constraints;

use Oro\Bundle\AddressBundle\Validator\Constraints\UniqueAddressTypesValidator;
use Oro\Bundle\AddressBundle\Validator\Constraints\UniqueAddressTypes;

class UniqueAddressTypesValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type array or Traversable and ArrayAccess, boolean given
     */
    public function testValidateExceptionWhenInvalidArgumentType()
    {
        $constraint = $this->getMock('Symfony\Component\Validator\Constraint');
        $validator = new UniqueAddressTypesValidator();
        $validator->validate(false, $constraint);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\ValidatorException
     * @expectedExceptionMessage Expected element of type Oro\Bundle\AddressBundle\Entity\AbstractTypedAddress, integer given
     */
    public function testValidateExceptionWhenInvalidArgumentElementType()
    {
        $constraint = $this->getMock('Symfony\Component\Validator\Constraint');
        $validator = new UniqueAddressTypesValidator();
        $validator->validate(array(1), $constraint);
    }

    /**
     * @dataProvider validAddressesDataProvider
     * @param array $addresses
     */
    public function testValidateValid(array $addresses)
    {
        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->never())
            ->method('addViolation');

        $constraint = $this->getMock('Oro\Bundle\AddressBundle\Validator\Constraints\UniqueAddressTypes');
        $validator = new UniqueAddressTypesValidator();
        $validator->initialize($context);

        $validator->validate($addresses, $constraint);
    }

    /**
     * @return array
     */
    public function validAddressesDataProvider()
    {
        return array(
            'no addresses' => array(
                array()
            ),
            'one address without type' => array(
                array($this->getTypedAddressMock(array()))
            ),
            'one address with type' => array(
                array($this->getTypedAddressMock(array('billing')))
            ),
            'many addresses unique types' => array(
                array(
                    $this->getTypedAddressMock(array('billing')),
                    $this->getTypedAddressMock(array('shipping')),
                    $this->getTypedAddressMock(array('billing_corporate')),
                    $this->getTypedAddressMock(array()),
                )
            ),
            'empty address' => array(
                array(
                    $this->getTypedAddressMock(array('billing')),
                    $this->getTypedAddressMock(array('shipping')),
                    $this->getTypedAddressMock(array(), true),
                )
            )
        );
    }

    /**
     * @dataProvider invalidAddressesDataProvider
     * @param array $addresses
     * @param string $types
     */
    public function testValidateInvalid($addresses, $types)
    {
        $context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
            ->disableOriginalConstructor()
            ->getMock();
        $context->expects($this->once())
            ->method('addViolation')
            ->with('Several addresses have the same type {{ types }}.', array('{{ types }}' => $types));

        $constraint = $this->getMock('Oro\Bundle\AddressBundle\Validator\Constraints\UniqueAddressTypes');
        $validator = new UniqueAddressTypesValidator();
        $validator->initialize($context);

        $validator->validate($addresses, $constraint);
    }

    /**
     * @return array
     */
    public function invalidAddressesDataProvider()
    {
        return array(
            'several addresses with one same type' => array(
                array(
                    $this->getTypedAddressMock(array('billing')),
                    $this->getTypedAddressMock(array('billing', 'shipping')),
                ),
                '"billing"'
            ),
            'several addresses with two same types' => array(
                array(
                    $this->getTypedAddressMock(array('billing')),
                    $this->getTypedAddressMock(array('shipping')),
                    $this->getTypedAddressMock(array('billing', 'shipping')),
                ),
                '"billing", "shipping"'
            ),
        );
    }

    /**
     * Get address mock.
     *
     * @param array $typeNames
     * @param bool $isEmpty
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTypedAddressMock(array $typeNames, $isEmpty = false)
    {
        $address = $this->getMockBuilder('Oro\Bundle\AddressBundle\Entity\AbstractTypedAddress')
            ->disableOriginalConstructor()
            ->getMock();

        $address->expects($this->any())
            ->method('getTypeNames')
            ->will($this->returnValue($typeNames));

        $address->expects($this->once())
            ->method('isEmpty')
            ->will($this->returnValue($isEmpty));

        return $address;
    }
}
