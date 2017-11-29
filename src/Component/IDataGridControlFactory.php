<?php
namespace Sellastica\DataGrid\Component;

use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Mapping\IRepository;

interface IDataGridControlFactory
{
	/**
	 * @param IRepository $repository
	 * @param FilterRuleCollection $filterRules
	 * @param int $filterId
	 * @return DataGridControl
	 */
	function create(
		IRepository $repository,
		FilterRuleCollection $filterRules = null,
		int $filterId = null
	): DataGridControl;
}
