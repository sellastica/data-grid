<?php
namespace Sellastica\DataGrid\Component;

use Sellastica\DataGrid\Model\FilterRuleCollection;

interface ITabsFactory
{
	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $filterRules
	 * @param bool $displayCustomTab
	 * @param int $filterId
	 * @return Tabs
	 */
	function create(
		FilterRuleCollection $filterRules,
		bool $displayCustomTab,
		int $filterId = null
	): Tabs;
}