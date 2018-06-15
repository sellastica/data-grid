<?php
namespace Sellastica\DataGrid\Mapping\Dibi;

use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Configuration;
use Sellastica\Entity\Entity\EntityCollection;
use Sellastica\Entity\Mapping\IRepository;

/**
 * @method IRepository getRepository
 */
trait TFilterRulesRepositoryProxy
{
	/** @var \Sellastica\Entity\Mapping\IDao */
	protected $dao;


	/**
	 * {@inheritDoc}
	 */
	public function findByFilterRules(
		FilterRuleCollection $rules,
		Configuration $configuration
	): EntityCollection
	{
		return $this->getRepository()->findByFilterRules($rules, $configuration);
	}
}
