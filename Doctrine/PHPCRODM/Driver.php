<?php

/*
 * This file is part of the Miky package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Miky\Bundle\GridBundle\Doctrine\PHPCRODM;

use Miky\Component\Grid\Data\DriverInterface;
use Miky\Component\Grid\Parameters;
use Doctrine\ODM\PHPCR\DocumentManagerInterface;

class Driver implements DriverInterface
{
    /**
     * Driver name
     */
    const NAME = 'doctrine/phpcr-odm';

    /**
     * Alias to use to reference fields from the data source class.
     */
    const QB_SOURCE_ALIAS = 'o';

    /**
     * @var DocumentManagerInterface
     */
    private $documentManager;

    /**
     * @param DocumentManagerInterface $documentManager
     */
    public function __construct(DocumentManagerInterface $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource(array $configuration, Parameters $parameters)
    {
        if (!array_key_exists('class', $configuration)) {
            throw new \InvalidArgumentException('"class" must be configured.');
        }

        $repository = $this->documentManager->getRepository($configuration['class']);
        $queryBuilder = $repository->createQueryBuilder(self::QB_SOURCE_ALIAS);

        return new DataSource($queryBuilder);
    }
}
