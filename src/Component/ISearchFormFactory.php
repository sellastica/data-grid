<?php
namespace Sellastica\DataGrid\Component;

use Sellastica\DataGrid\Model\FilterRuleCollection;

interface ISearchFormFactory
{
	/**
	 * @param FilterRuleCollection $filterRules
	 * @param bool $displaySearchInput
	 * @return SearchForm
	 */
	function create(
		FilterRuleCollection $filterRules,
		bool $displaySearchInput
	): SearchForm;
}