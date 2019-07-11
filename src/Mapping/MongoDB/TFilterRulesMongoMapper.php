<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

/**
 * @property \MongoDB\Client $mongo
 * @method \MongoDB\Collection getCollection(\Sellastica\Entity\Configuration $configuration = null)
 */
trait TFilterRulesMongoMapper
{
	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @return array
	 */
	public function findByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		\Sellastica\Entity\Configuration $configuration = null
	): iterable
	{
		$filter = \Sellastica\DataGrid\Mapping\MongoDB\RulesToMatchConverter::convert($rules);
		return $this->getCollection($configuration)->find(
			$filter,
			$this->getOptions($filter, $configuration)
		);
	}

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @return int
	 */
	public function findCountByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules
	): int
	{
		$filter = \Sellastica\DataGrid\Mapping\MongoDB\RulesToMatchConverter::convert($rules);
		return $this->getCollection()->count(
			$filter,
			$this->getOptions($filter)
		);
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
		$this->getCollection()->updateMany(
			\Sellastica\DataGrid\Mapping\MongoDB\RulesToMatchConverter::convert($rules),
			$data
		);
	}

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 */
	public function deleteByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		\Sellastica\Entity\Configuration $configuration = null
	): void
	{
		$filter = \Sellastica\DataGrid\Mapping\MongoDB\RulesToMatchConverter::convert($rules);
		$this->getCollection()->deleteMany(
			$filter,
			$this->getOptions($filter, $configuration)
		);
	}
}
