<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Entity\EntityCollection;

/**
 * @property AdminFilterRule[] $items
 * @method AdminFilterRuleCollection add($entity)
 * @method AdminFilterRuleCollection remove($key)
 * @method AdminFilterRule|mixed getEntity(int $entityId, $default = null)
 * @method AdminFilterRule|mixed getBy(string $property, $value, $default = null)
 */
class AdminFilterRuleCollection extends EntityCollection
{
}