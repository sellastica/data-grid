<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

/**
 * @method initialize($entities, $first = null, $second = null)
 * @property TFilterRulesDao $dao
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

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param array $data
	 */
	public function updateByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		array $data
	): void
	{
		$this->dao->updateByFilterRules($rules, $data);
	}
}
