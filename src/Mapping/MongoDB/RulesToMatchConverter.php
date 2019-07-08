<?php
namespace Sellastica\DataGrid\Mapping\MongoDB;

class RulesToMatchConverter
{
	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @return array
	 */
	public static function convert(\Sellastica\DataGrid\Model\FilterRuleCollection $rules): array
	{
		$match = [];
		foreach ($rules->getActiveRules(\Sellastica\DataGrid\Model\FilterRule:: AND) as $rule) {
			/** @var \Sellastica\DataGrid\Model\FilterRule $rule */
			if (!$rule->isColumnFilter()) {
				continue;
			}

			if ($rule->getMapping()) {
				$column = $rule->getMapping();
			} else {
				$column = $rule->getKey() === '_id'
					? $rule->getKey()
					: \Sellastica\Utils\Strings::toCamelCase(preg_replace('~(.+)(_min|_max)~', '$1', $rule->getKey()));
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
					$match[$column] = ['$in' => $rule->getValue()];
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
}
