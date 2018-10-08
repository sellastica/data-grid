<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

/**
 * @property \MongoDB\Client $mongo
 * @method \MongoDB\Collection getCollection(\Sellastica\Entity\Configuration $configuration)
 */
trait TFilterRulesMongoMapper
{
	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @return array
	 */
	private function rulesToMatch(\Sellastica\DataGrid\Model\FilterRuleCollection $rules): array
	{
		$match = [];
		foreach ($rules->getActiveRules(\Sellastica\DataGrid\Model\FilterRule:: AND) as $rule) {
			if (!$rule->isColumnFilter()) {
				continue;
			}

			if ($rule->getMapping()) {
				$column = $rule->getMapping();
			} else {
				$column = \Sellastica\Utils\Strings::toCamelCase(preg_replace('~(.+)(_min|_max)~', '$1', $rule->getKey()));
			}

			switch ($rule->getType()) {
				case \Sellastica\DataGrid\Model\FilterRule::INT:
				case \Sellastica\DataGrid\Model\FilterRule::BOOLEAN:
					$match[$column] = $rule->getResolvedValue();
					break;
				case \Sellastica\DataGrid\Model\FilterRule::DATE:
					throw new \Nette\NotImplementedException();
					break;
				case \Sellastica\DataGrid\Model\FilterRule::ENUM:
					throw new \Nette\NotImplementedException();
					break;
				case \Sellastica\DataGrid\Model\FilterRule::SET:
					throw new \Nette\NotImplementedException();
					break;
				case \Sellastica\DataGrid\Model\FilterRule::STRING:
					$match[$column] = new \MongoDB\BSON\Regex($rule->getValue(), 'i');
					break;
				default:
					break;
			}
		}

		$or = [];
		foreach ($rules->getActiveRules(\Sellastica\DataGrid\Model\FilterRule:: OR) as $rule) {
			if (!$rule->isColumnFilter()) {
				continue;
			}

			$column = $rule->getMapping() ?: $rule->getKey();
			switch ($rule->getType()) {
				case \Sellastica\DataGrid\Model\FilterRule::INT:
				case \Sellastica\DataGrid\Model\FilterRule::BOOLEAN:
					$or[] = [$column => $rule->getResolvedValue()];
					break;
				case \Sellastica\DataGrid\Model\FilterRule::ENUM:
					throw new \Nette\NotImplementedException();
					break;
				case \Sellastica\DataGrid\Model\FilterRule::STRING:
					$or[] = [$column => new \MongoDB\BSON\Regex($rule->getValue(), 'i')];
					break;
				default:
					break;
			}
		}

		if (sizeof($or)) {
			$match['$or'] = $or;
		}

		return $match;
	}

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param \Sellastica\Entity\Configuration $configuration
	 * @return array
	 */
	public function findByFilterRules(
		\Sellastica\DataGrid\Model\FilterRuleCollection $rules,
		\Sellastica\Entity\Configuration $configuration
	): iterable
	{
		$filter = $this->rulesToMatch($rules);
		return $this->getCollection($configuration)->find(
			$filter,
			$this->getOptions($filter, $configuration)
		);
	}
}
