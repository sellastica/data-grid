<?php
namespace Sellastica\DataGrid\Mapping\Dibi;

use Dibi;
use Sellastica\DataGrid\Model\FilterRule;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Configuration;
use Sellastica\Utils\Strings;

/**
 * @method Dibi\Fluent applyConfiguration(Dibi\Fluent $resource, Configuration $configuration = null)
 * @method array getArray($arrayWithIds, $column = 'id')
 */
trait TFilterRulesDibiMapper
{
	/**
	 * @param FilterRuleCollection $rules
	 * @param \Sellastica\Entity\Configuration $configuration
	 * @return array
	 */
	public function findByFilterRules(
		FilterRuleCollection $rules,
		Configuration $configuration
	): array
	{
		$resource = $this->getAdminResourceWithIds($configuration, $rules)
			->setFlag('DISTINCT');
		foreach ($rules->getActiveRules(FilterRule::AND) as $rule) {
			if (!$rule->isColumnFilter()) {
				continue;
			}

			if ($rule->getMapping()) {
				$column = $rule->getMapping();
			} else {
				$column = Strings::toCamelCase(preg_replace('~(.+)(_min|_max)~', '$1', $rule->getKey()));
			}

			$modificator = preg_match('~^[a-zA-Z0-9\.]+$~', $column) ? '%n' : '%sql';
			switch ($rule->getType()) {
				case FilterRule::INT:
				case FilterRule::BOOLEAN:
					$resource->where(
						sprintf('%s %s %s', $modificator, $rule->getComparator(), '%i'), $column, $rule->getValue()
					);
					break;
				case FilterRule::DATE:
					$resource->where(
						sprintf('%s %s %s', $modificator, $rule->getComparator(), '%d'), $column, $rule->getValue()
					);
					break;
				case FilterRule::ENUM:
					$resource->where("$modificator = %s", $column, $rule->getValue());
					break;
				case FilterRule::SET:
					$resource->where("$modificator IN (%sN)", $column, $rule->getValue());
					break;
				case FilterRule::STRING:
					$resource->where("$modificator LIKE %~like~ COLLATE utf8mb4_unicode_ci", $column, $rule->getValue());
					break;
				default:
					break;
			}
		}

		$array = [];
		foreach ($rules->getActiveRules(FilterRule::OR) as $rule) {
			if (!$rule->isColumnFilter()) {
				continue;
			}

			$column = $rule->getMapping() ?: $rule->getKey();
			$modificator = preg_match('~^[a-zA-Z0-9\.]+$~', $column) ? '%n' : '%sql';
			switch ($rule->getType()) {
				case FilterRule::INT:
				case FilterRule::BOOLEAN:
					$array[] = ["$modificator = %i", $column, $rule->getValue()];
					break;
				case FilterRule::ENUM:
					$array[] = ["$modificator = %s", $column, $rule->getValue()];
					break;
				case FilterRule::STRING:
					$array[] = ["$modificator LIKE %~like~ COLLATE utf8mb4_unicode_ci", $column, $rule->getValue()];
					break;
				default:
					break;
			}
		}

		if (sizeof($array)) {
			$resource->where('(%or)', $array);
		}

		//paginator, sorter
		$this->applyConfiguration($resource, $configuration);
		return $resource->fetchPairs();
	}

	/**
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @param FilterRuleCollection|null $rules
	 * @return Dibi\Fluent
	 */
	protected function getAdminResourceWithIds(
		Configuration $configuration = null,
		FilterRuleCollection $rules = null
	): Dibi\Fluent
	{
		return $this->getAdminResource($configuration, $rules)
			->select(false)
			->select('%n.id', $this->getTableName());
	}
}
