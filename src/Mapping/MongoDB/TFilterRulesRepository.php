<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

/**
 * @method initialize($entities, $first = null, $second = null)
 */
trait TFilterRulesRepository
{
	/** @var \Sellastica\MongoDB\Mapping\MongoDao */
	protected $dao;


	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param \Sellastica\Entity\Configuration $configuration
	 * @return \Sellastica\Entity\Entity\EntityCollection
	 */
	public function findByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		\Sellastica\Entity\Configuration $configuration
	): \Sellastica\Entity\Entity\EntityCollection
	{
		$entities = $this->dao->findByFilterRules($rules, $configuration);
		return $this->initialize($entities);
	}
}
