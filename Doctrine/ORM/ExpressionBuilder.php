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

use Miky\Component\Grid\Data\ExpressionBuilderInterface;
use Doctrine\ORM\QueryBuilder;


class ExpressionBuilder implements ExpressionBuilderInterface
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function andX(...$expressions)
    {
        return $this->queryBuilder->expr()->andX(...$expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function orX(...$expressions)
    {
        return $this->queryBuilder->expr()->orX(...$expressions);
    }

    /**
     * {@inheritdoc}
     */
    public function comparison($field, $operator, $value)
    {
        throw new \BadMethodCallException('Not supported yet.');
        // TODO: Implement comparison() method.
    }

    /**
     * {@inheritdoc}
     */
    public function equals($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->eq($this->getFieldName($field), ':'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function notEquals($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        return $this->queryBuilder->expr()->neq($this->getFieldName($field), ':'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThan($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' < :'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function lessThanOrEqual($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' <= :'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThan($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' > :'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function greaterThanOrEqual($field, $value)
    {
        $parameterName = $this->getParameterName($field);
        $this->queryBuilder->setParameter($parameterName, $value);

        $this->queryBuilder->andWhere($this->getFieldName($field).' >= :'.$parameterName);
    }

    /**
     * {@inheritdoc}
     */
    public function in($field, array $values)
    {
        return $this->queryBuilder->expr()->in($this->getFieldName($field), $values);
    }

    /**
     * {@inheritdoc}
     */
    public function notIn($field, array $values)
    {
        return $this->queryBuilder->expr()->notIn($this->getFieldName($field), $values);
    }

    /**
     * {@inheritdoc}
     */
    public function isNull($field)
    {
        return $this->queryBuilder->expr()->isNull($this->getFieldName($field));
    }

    /**
     * {@inheritdoc}
     */
    public function isNotNull($field)
    {
        return $this->queryBuilder->expr()->isNotNull($this->getFieldName($field));
    }

    /**
     * {@inheritdoc}
     */
    public function like($field, $pattern)
    {
        return $this->queryBuilder->expr()->like($this->getFieldName($field), $this->queryBuilder->expr()->literal($pattern));
    }

    /**
     * {@inheritdoc}
     */
    public function notLike($field, $pattern)
    {
        return $this->queryBuilder->expr()->notLike($this->getFieldName($field), $this->queryBuilder->expr()->literal($pattern));
    }

    /**
     * {@inheritdoc}
     */
    public function orderBy($field, $direction)
    {
        return $this->queryBuilder->orderBy($this->getFieldName($field), $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function addOrderBy($field, $direction)
    {
        return $this->queryBuilder->addOrderBy($this->getFieldName($field), $direction);
    }

    /**
     * {@inheritdoc}
     */
    private function getFieldName($field)
    {
        if (false === strpos($field, '.')) {
            return $this->queryBuilder->getRootAlias().'.'.$field;
        }

        return $field;
    }

    /**
     * @param string $field
     *
     * @return string
     */
    private function getParameterName($field)
    {
        return str_replace('.', '_', $field);
    }
}
