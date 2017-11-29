<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Entity\EntityFactory;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\IBuilder;

/**
 * @method AdminFilter build(IBuilder $builder, bool $initialize = true, int $assignedId = null)
 */
class AdminFilterFactory extends EntityFactory
{
	/**
	 * @param IEntity|AdminFilter $entity
	 */
	public function doInitialize(IEntity $entity)
	{
		$entity->setRelationService(new AdminFilterRelations($entity, $this->em));
	}

	/**
	 * @return string
	 */
	public function getEntityClass(): string
	{
		return AdminFilter::class;
	}
}