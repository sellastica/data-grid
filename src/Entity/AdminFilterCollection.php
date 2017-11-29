<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Entity\EntityCollection;

/**
 * @property AdminFilter[] $items
 * @method AdminFilterCollection add($entity)
 * @method AdminFilterCollection remove($key)
 * @method AdminFilter|mixed getEntity(int $entityId, $default = null)
 * @method AdminFilter|mixed getBy(string $property, $value, $default = null)
 */
class AdminFilterCollection extends EntityCollection
{
}