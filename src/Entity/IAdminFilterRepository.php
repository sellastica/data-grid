<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Configuration;
use Sellastica\Entity\Mapping\IRepository;

/**
 * @method AdminFilter find(int $id)
 * @method AdminFilter findOneBy(array $filterValues)
 * @method AdminFilter[] findAll(Configuration $configuration = null)
 * @method AdminFilter[] findBy(array $filterValues, Configuration $configuration = null)
 * @method AdminFilter[] findByIds(array $idsArray, Configuration $configuration = null)
 * @method AdminFilter findPublishable(int $id)
 * @method AdminFilter findOnePublishableBy(array $filterValues, Configuration $configuration = null)
 * @method AdminFilter[] findAllPublishable(Configuration $configuration = null)
 * @method AdminFilter[] findPublishableBy(array $filterValues, Configuration $configuration = null)
 */
interface IAdminFilterRepository extends IRepository
{
}
