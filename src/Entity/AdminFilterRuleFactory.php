<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Entity\EntityFactory;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\IBuilder;

/**
 * @method AdminFilterRule build(IBuilder $builder, bool $initialize = true, int $assignedId = null)
 */
class AdminFilterRuleFactory extends EntityFactory
{
	/**
	 * @param \Sellastica\Entity\Entity\IEntity $entity
	 */
	public function doInitialize(IEntity $entity)
	{
		$entity->setRelationService(new AdminFilterRuleRelations($entity, $this->em));
	}

	/**
	 * @return string
	 */
	public function getEntityClass(): string
	{
		return AdminFilterRule::class;
	}
}