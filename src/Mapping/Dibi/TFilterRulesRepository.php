<?php
namespace Sellastica\DataGrid\Mapping\Dibi;

use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Configuration;
use Sellastica\Entity\Entity\EntityCollection;

/**
 * @method initialize($entities, $first = null, $second = null)
 */
trait TFilterRulesRepository
{
	/** @var \Sellastica\Entity\Mapping\IDao */
	protected $dao;


	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param \Sellastica\Entity\Configuration $configuration
	 * @return EntityCollection
	 */
	public function findByFilterRules(
		FilterRuleCollection $rules,
		Configuration $configuration
	): EntityCollection
	{
		$entities = $this->dao->findByFilterRules($rules, $configuration);
		return $this->initialize($entities);
	}
}
