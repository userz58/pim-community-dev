<?php

namespace Pim\Bundle\EnrichBundle\AbstractController;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Base abstract controller for managing entities
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @deprecated will be removed in 1.5, please avoid to use parent controller
 */
abstract class AbstractDoctrineController
{
    /** @var ManagerRegistry */
    protected $doctrine;

    /**
     * Constructor
     *
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Returns the Doctrine registry service.
     *
     * @deprecated never directly manipulate the ObjectManager in the controller
     *
     * @return ManagerRegistry
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * Returns the Doctrine manager
     *
     * @deprecated never directly manipulate the ObjectManager in the controller
     *
     * @return ObjectManager
     */
    protected function getManager()
    {
        return $this->doctrine->getManager();
    }

    /**
     * Returns the Doctrine manager for the given class
     *
     * @param string $class
     *
     * @deprecated never directly manipulate the ObjectManager in the controller
     *
     * @return ObjectManager
     */
    protected function getManagerForClass($class)
    {
        return $this->doctrine->getManagerForClass($class);
    }

    /**
     * @param string $className
     *
     * @deprecated prefer inject a repository service in the controller
     *
     * @return ObjectRepository
     */
    protected function getRepository($className)
    {
        return $this->doctrine->getRepository($className);
    }

    /**
     * Persist an object
     *
     * @param object $object
     * @param bool   $flush
     *
     * @deprecated prefer inject a SaverInterface service in the controller
     */
    protected function persist($object, $flush = true)
    {
        $manager = $this->doctrine->getManagerForClass(get_class($object));
        $manager->persist($object);

        if ($flush) {
            $manager->flush();
        }
    }

    /**
     * Remove an object
     *
     * @param object $object
     * @param bool   $flush
     *
     * @deprecated prefer inject a RemoverInterface service in the controller
     */
    protected function remove($object, $flush = true)
    {
        $manager = $this->doctrine->getManagerForClass(get_class($object));
        $manager->remove($object);

        if ($flush) {
            $manager->flush();
        }
    }

    /**
     * Find an entity or throw a 404
     *
     * @param string $className Example: 'PimCatalogBundle:Category'
     * @param int    $id        The id of the entity
     *
     * @throws NotFoundHttpException
     *
     * @deprecated prefer inject a repository in the controller
     *
     * @return object
     */
    protected function findOr404($className, $id)
    {
        $result = $this->getRepository($className)->find($id);

        if (!$result) {
            throw $this->createNotFoundException(sprintf('%s entity not found', $className));
        }

        return $result;
    }
}
