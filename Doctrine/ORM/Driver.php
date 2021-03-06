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

use Miky\Component\Grid\Data\DriverInterface;
use Miky\Component\Grid\Parameters;
use Doctrine\ORM\EntityManagerInterface;


class Driver implements DriverInterface
{
    const NAME = 'doctrine/orm';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(array $configuration, Parameters $parameters)
    {
        if (!array_key_exists('class', $configuration)) {
            throw new \InvalidArgumentException('"class" must be configured.');
        }

        $repository = $this->entityManager->getRepository($configuration['class']);
        if (isset($configuration['repository']['method'])) {
            $callable = [$repository, $configuration['repository']['method']];
            $arguments = isset($configuration['repository']['arguments']) ? $configuration['repository']['arguments'] : [];

            $queryBuilder = call_user_func_array($callable, $arguments);
        } else {
            $queryBuilder = $repository->createQueryBuilder('o');
        }

        return new DataSource($queryBuilder);
    }
}
