<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\EntityManager;
use Sellastica\Entity\Relation\IEntityRelations;

/**
 * @property AdminFilter $entity
 */
class AdminFilterRelations implements IEntityRelations
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
	 * @return AdminFilterRuleCollection
	 */
	public function getRules(): AdminFilterRuleCollection
	{
		return $this->em->getRepository(AdminFilterRule::class)->findBy([
			'adminFilterId' => $this->entity->getId(),
		]);
	}
}