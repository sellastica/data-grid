<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Configuration;
use Sellastica\Entity\Mapping\IRepository;

/**
 * @method AdminFilterRule find(int $id)
 * @method AdminFilterRule findOneBy(array $filterValues)
 * @method AdminFilterRule[] findAll(Configuration $configuration = null)
 * @method AdminFilterRule[] findBy(array $filterValues, Configuration $configuration = null)
 * @method AdminFilterRule[] findByIds(array $idsArray, Configuration $configuration = null)
 * @method AdminFilterRule findPublishable(int $id)
 * @method AdminFilterRule findOnePublishableBy(array $filterValues)
 * @method AdminFilterRule[] findAllPublishable(Configuration $configuration = null)
 * @method AdminFilterRule[] findPublishableBy(array $filterValues, Configuration $configuration = null)
 */
interface IAdminFilterRuleRepository extends IRepository
{
}
