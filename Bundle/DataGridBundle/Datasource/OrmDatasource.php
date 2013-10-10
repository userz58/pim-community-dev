<?php

namespace Oro\Bundle\DataGridBundle\Datasource;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\DataGridBundle\Datasource\QueryConverter\YamlConverter;

class OrmDatasource implements DatasourceInterface
{
    /** @var QueryBuilder */
    protected $qb;

    /** @var EntityManager */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function process(DatagridInterface $grid, array $config)
    {
        $queryConfig = array_intersect_key($config, array_flip(array('query')));

        $converter = new YamlConverter();
        $this->qb  = $converter->parse($queryConfig, $this->em);

        $grid->setDatasource($this);
    }

    /**
     * {@inheritDoc}
     */
    public function getResults()
    {
        $results = $this->qb->getQuery()->execute();

        return $results;
    }

    /**
     * Returns query builder
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }
}
