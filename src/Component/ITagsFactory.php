<?php
namespace Sellastica\DataGrid\Component;

use Sellastica\DataGrid\Model\FilterRuleCollection;

interface ITagsFactory
{
	/**
	 * @param FilterRuleCollection $filterRules
	 * @return Tags
	 */
	function create(FilterRuleCollection $filterRules): Tags;
}