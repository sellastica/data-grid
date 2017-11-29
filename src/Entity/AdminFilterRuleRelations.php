<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\EntityManager;
use Sellastica\Entity\Relation\IEntityRelations;

/**
 * @property AdminFilterRule $entity
 */
class AdminFilterRuleRelations implements IEntityRelations
{
	/** @var IEntity */
	private $entity;
	/** @var EntityManager */
	private $em;


	/**
	 * @param IEntity $entity
	 * @param EntityManager $em
	 */
	public function __construct(
		IEntity $entity,
		EntityManager $em
	)
	{
		$this->entity = $entity;
		$this->em = $em;
	}

	/**
	 * @return AdminFilter
	 */
	public function getAdminFilter(): AdminFilter
	{
		return $this->em->getRepository(AdminFilter::class)->find($this->entity->getAdminFilterId());
	}
}