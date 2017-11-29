<?php
namespace Sellastica\DataGrid\Component;

use Sellastica\DataGrid\Model\FilterRuleCollection;

interface ISaveSearchFormFactory
{
	/**
	 * @param FilterRuleCollection $filterRules
	 * @return SaveSearchForm
	 */
	function create(FilterRuleCollection $filterRules = null): SaveSearchForm;
}
