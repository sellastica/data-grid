<?php
namespace Sellastica\DataGrid\Mapping;

use Sellastica\DataGrid\Entity\AdminFilterRuleBuilder;
use Sellastica\DataGrid\Entity\AdminFilterRuleCollection;
use Sellastica\Entity\Entity\EntityCollection;
use Sellastica\Entity\IBuilder;

class AdminFilterRuleDao extends \Sellastica\Entity\Mapping\Dao
{
	/**
	 * {@inheritdoc}
	 */
	protected function getBuilder($data, $first = null, $second = null): IBuilder
	{
		return AdminFilterRuleBuilder::create($data->adminFilterId, $data->criterion)
			->hydrate($data);
	}

	/**
	 * @return \Sellastica\Entity\Entity\EntityCollection|AdminFilterRuleCollection
	 */
	public function getEmptyCollection(): EntityCollection
	{
		return new AdminFilterRuleCollection();
	}
}