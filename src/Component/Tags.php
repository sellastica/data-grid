<?php
namespace Sellastica\DataGrid\Component;

use Nette;
use Sellastica\AdminUI\Component\BaseControl;
use Sellastica\DataGrid\Model\FilterRule;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\DataGrid\Model\Tag;

class Tags extends BaseControl
{
	/** @var \Sellastica\DataGrid\Model\FilterRuleCollection|\Sellastica\DataGrid\Model\FilterRule[] */
	private $filterRules;
	/** @var Nette\Http\Request */
	private $request;


	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $filterRules
	 * @param Nette\Http\Request $request
	 */
	public function __construct(
		FilterRuleCollection $filterRules,
		Nette\Http\Request $request
	)
	{
		parent::__construct();
		$this->filterRules = $filterRules;
		$this->request = $request;
	}

	/**
	 * @param array $params
	 */
	protected function beforeRender(array $params = [])
	{
		$this->template->tags = $this->getTags();
	}

	/**
	 * @return \Sellastica\DataGrid\Model\Tag[]
	 */
	private function getTags()
	{
		/** @var Tag[] $tags */
		$tags = [];
		foreach ($this->filterRules as $rule) {
			/** @var FilterRule $rule */
			if (!$rule->isInTags()) {
				continue;
			}

			$array = ['page' => null];
			foreach ($this->filterRules as $filterRule) {
				if ($filterRule->isInTags()) {
					$array[$filterRule->getKey()] = $filterRule->getKey() !== $rule->getKey() ? $filterRule->getValue() : null;
				}
			}

			$tags[] = Tag::fromSearchCriterion(
				$rule,
				$this->presenter->link('this', $array)
			);
		}

		return $tags;
	}
}
