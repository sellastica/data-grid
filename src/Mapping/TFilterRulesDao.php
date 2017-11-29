<?php
namespace Sellastica\DataGrid\Mapping;

use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Configuration;
use Sellastica\Entity\Entity\EntityCollection;
use Sellastica\Entity\Mapping\IMapper;

/**
 * @method EntityCollection getEntitiesFromCacheOrStorage(array $idsArray, $first = null, $second = null)
 */
trait TFilterRulesDao
{
	/** @var IMapper */
	protected $mapper;


	/**
	 * @param FilterRuleCollection $rules
	 * @param Configuration $configuration
	 * @return EntityCollection
	 */
	public function findByFilterRules(
		FilterRuleCollection $rules,
		Configuration $configuration
	): EntityCollection
	{
		$idsArray = $this->mapper->findByFilterRules($rules, $configuration);
		return $this->getEntitiesFromCacheOrStorage($idsArray);
	}
}
