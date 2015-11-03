<?php

namespace Pim\Component\Catalog\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Pim\Component\Catalog\Model\AssociationTypeInterface;
use Pim\Component\Catalog\Model\GroupInterface;
use Pim\Bundle\CatalogBundle\Model\ReferableInterface;
use Pim\Component\Catalog\Model\ProductInterface;

/**
 * Association interface
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface AssociationInterface extends ReferableInterface
{
    /**
     * Get id
     *
     * @return int|string
     */
    public function getId();

    /**
     * Get products
     *
     * @return ProductInterface[]|ArrayCollection
     */
    public function getProducts();

    /**
     * Set products
     *
     * @param ProductInterface[] $products
     *
     * @return AssociationInterface
     */
    public function setProducts($products);

    /**
     * Add a product
     *
     * @param ProductInterface $product
     *
     * @return AssociationInterface
     */
    public function addProduct(ProductInterface $product);

    /**
     * Remove a product
     *
     * @param ProductInterface $product
     *
     * @return AssociationInterface
     */
    public function removeProduct(ProductInterface $product);

    /**
     * Has a product
     *
     * @param ProductInterface $product
     *
     * @return bool
     */
    public function hasProduct(ProductInterface $product);

    /**
     * Set groups
     *
     * @param GroupInterface[] $groups
     *
     * @return AssociationInterface
     */
    public function setGroups($groups);

    /**
     * Get association type
     *
     * @return AssociationTypeInterface
     */
    public function getAssociationType();

    /**
     * Set association type
     *
     * @param AssociationTypeInterface $associationType
     *
     * @return AssociationInterface
     */
    public function setAssociationType(AssociationTypeInterface $associationType);

    /**
     * Add a group
     *
     * @param GroupInterface $group
     *
     * @return AssociationInterface
     */
    public function addGroup(GroupInterface $group);

    /**
     * Get groups
     *
     * @return GroupInterface[]
     */
    public function getGroups();

    /**
     * Remove a group
     *
     * @param GroupInterface $group
     *
     * @return AssociationInterface
     */
    public function removeGroup(GroupInterface $group);

    /**
     * Get owner
     *
     * @return ProductInterface
     */
    public function getOwner();

    /**
     * Set owner
     *
     * @param ProductInterface $owner
     *
     * @return AssociationInterface
     */
    public function setOwner(ProductInterface $owner);
}
