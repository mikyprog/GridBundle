<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\GridBundle\Doctrine\ORM;

use Miky\Component\Grid\Data\DataSourceInterface;
use Miky\Component\Grid\Data\ExpressionBuilderInterface;
use Miky\Component\Grid\Parameters;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;


class DataSource implements DataSourceInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var ExpressionBuilderInterface
     */
    private $expressionBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->expressionBuilder = new ExpressionBuilder($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function restrict($expression, $condition = DataSourceInterface::CONDITION_AND)
    {
        switch ($condition) {
            case DataSourceInterface::CONDITION_AND:
                $this->queryBuilder->andWhere($expression);
                break;
            case DataSourceInterface::CONDITION_OR:
                $this->queryBuilder->orWhere($expression);
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionBuilder()
    {
        return $this->expressionBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(Parameters $parameters)
    {
        // Use output walkers option in DoctrineORMAdapter should be false as it affects performance greatly. (see #3775)
        $paginator = new Pagerfanta(new DoctrineORMAdapter($this->queryBuilder, true, false));
        $paginator->setNormalizeOutOfRangePages(true);
        $paginator->setCurrentPage($parameters->get('page', 1));

        return $paginator;
    }
}
