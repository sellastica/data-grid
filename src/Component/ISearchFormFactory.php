<?php
namespace Sellastica\DataGrid\Component;

use Sellastica\DataGrid\Model\FilterRuleCollection;

interface ISearchFormFactory
{
	/**
	 * @param FilterRuleCollection $filterRules
	 * @return SearchForm
	 */
	function create(FilterRuleCollection $filterRules): SearchForm;
}