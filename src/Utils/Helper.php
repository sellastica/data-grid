<?php
namespace Sellastica\DataGrid\Utils;

class Helper
{
	/**
	 * @param array $array
	 * @return \MongoDB\BSON\ObjectId[]
	 */
	public static function arrayToObjectIds(array $array): array
	{
		$objectIds = [];
		foreach ($array as $k => $v) {
			$objectIds[$k] = new \MongoDB\BSON\ObjectId($v);
		}

		return $objectIds;
	}

	/**
	 * @param iterable $bulkIds
	 * @return \MongoDB\BSON\ObjectId[]
	 */
	public static function bulkIdsToObjectIds(iterable $bulkIds): array
	{
		$objectIds = [];
		foreach ($bulkIds as $id => $v) {
			if ($v->bulk_id) {
				$objectIds[] = new \MongoDB\BSON\ObjectId($id);
			}
		}

		return $objectIds;
	}

	/**
	 * @param iterable $bulkIds
	 * @return \MongoDB\BSON\ObjectId[]
	 */
	public static function bulkIdsToArray(iterable $bulkIds): array
	{
		$array = [];
		foreach ($bulkIds as $id => $v) {
			if ($v->bulk_id) {
				$array[] = $id;
			}
		}

		return $array;
	}

	/**
	 * @param \Sellastica\DataGrid\Component\DataGridControl $dataGrid
	 * @param bool $allPages
	 * @param iterable $content
	 * @return \Sellastica\DataGrid\Model\FilterRuleCollection|\Sellastica\DataGrid\Model\FilterRule[]
	 */
	public static function postDataToFilterRules(
		\Sellastica\DataGrid\Component\DataGridControl $dataGrid,
		bool $allPages = true,
		iterable $content = []
	): \Sellastica\DataGrid\Model\FilterRuleCollection
	{
		if ($allPages) {
			$filterRules = $dataGrid->getFilterRules(true);
		} else {
			$filterRules = new \Sellastica\DataGrid\Model\FilterRuleCollection();
			$filterRules->addRule(new \Sellastica\DataGrid\Model\FilterRule(
				'_id',
				self::bulkIdsToObjectIds($content),
				\Sellastica\DataGrid\Model\FilterRule::ENUM
			));
		}

		return $filterRules;
	}
}
