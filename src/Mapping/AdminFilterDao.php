<?php
namespace Sellastica\DataGrid\Mapping;

use Sellastica\DataGrid\Entity\AdminFilterBuilder;
use Sellastica\DataGrid\Entity\AdminFilterCollection;
use Sellastica\Entity\Entity\EntityCollection;
use Sellastica\Entity\IBuilder;
use Sellastica\Entity\Mapping\Dao;

class AdminFilterDao extends Dao
{
	/**
	 * {@inheritdoc}
	 */
	protected function getBuilder($data, $first = null, $second = null): IBuilder
	{
		return AdminFilterBuilder::create($data->adminUserId, $data->title, $data->presenter)
			->hydrate($data);
	}

	/**
	 * @return EntityCollection|AdminFilterCollection
	 */
	protected function getEmptyCollection(): EntityCollection
	{
		return new AdminFilterCollection();
	}
}