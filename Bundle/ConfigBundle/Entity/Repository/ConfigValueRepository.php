<?php

namespace Oro\Bundle\ConfigBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ConfigValueRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConfigValueRepository extends EntityRepository
{
    /**
     * Remove values by params
     *
     * @param integer $configId
     * @param string  $section
     * @param string  $name
     *
     * @return array
     */
    public function removeValues($configId, $section, $name)
    {
        $builder = $this->getEntityManager()->createQueryBuilder();

        $builder->getEntityManager()->beginTransaction();
        $builder->delete('OroConfigBundle:ConfigValue', 'cv')
            ->where('cv.config = :configId')
            ->andWhere('cv.name = :name')
            ->andWhere('cv.section = :section')
            ->setParameter('configId', $configId)
            ->setParameter('section', $section)
            ->setParameter('name', $name);

        $builder->getQuery()->execute();
        $builder->getEntityManager()->commit();
    }
}
