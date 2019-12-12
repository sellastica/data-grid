<?php
namespace Sellastica\DataGrid\Model;

use Nette\Application\UI;
use Sellastica\Entity\Entity\EntityCollection;
use Sellastica\Entity\Entity\IEntity;
use Sellastica\Entity\Sorter;
use Sellastica\UI\Pagination\Pagination;
use Sellastica\Utils\Strings;

class DataGrid
{
	/** @var UI\Control */
	private $control;
	/** @var \Sellastica\DataGrid\Component\DataGridControl */
	private $dataGridControl;
	/** @var \Sellastica\Entity\Mapping\IRepository */
	private $repository;
	/** @var \Sellastica\DataGrid\Model\FilterRuleCollection|null */
	private $filterRules;
	/** @var array */
	private $queryColumns = [];
	/** @var DataGridHeader */
	private $header;
	/** @var DataGridRow[] */
	private $rows = [];
	/** @var string|null */
	private $orderBy;
	/** @var bool */
	private $sortAsc = true;
	/** @var Pagination|null */
	private $pagination;
	/** @var array */
	private $sortableColumns = [];
	/** @var array */
	private $mapping = [];
	/** @var array */
	private $bulkActions = [];
	/** @var string */
	private $bulkPrimaryKey = 'id';
	/** @var bool */
	private $bulkAllPages = true;
	/** @var callable */
	private $resultsCallback;


	/**
	 * @param UI\Control $control
	 * @param \Sellastica\DataGrid\Component\DataGridControl $dataGridControl
	 * @param \Sellastica\Entity\Mapping\IRepository $repository
	 * @param FilterRuleCollection $filterRules
	 */
	public function __construct(
		UI\Control $control,
		\Sellastica\DataGrid\Component\DataGridControl $dataGridControl,
		\Sellastica\Entity\Mapping\IRepository $repository,
		\Sellastica\DataGrid\Model\FilterRuleCollection $filterRules = null
	)
	{
		$this->control = $control;
		$this->dataGridControl = $dataGridControl;
		$this->repository = $repository;
		$this->filterRules = $filterRules;
		$this->header = new DataGridHeader($this);
		$this->resultsCallback = function (?FilterRuleCollection $filterRules, \Sellastica\Entity\Configuration $configuration) {
			return $this->repository->findByFilterRules($filterRules, $configuration);
		};
	}

	/**
	 * @return \Sellastica\DataGrid\Component\DataGridControl
	 */
	public function getDataGridControl(): \Sellastica\DataGrid\Component\DataGridControl
	{
		return $this->dataGridControl;
	}

	/**
	 * @return \Sellastica\DataGrid\Model\FilterRuleCollection
	 */
	public function getFilterRules(): \Sellastica\DataGrid\Model\FilterRuleCollection
	{
		return $this->filterRules ?? new \Sellastica\DataGrid\Model\FilterRuleCollection();
	}

	/**
	 * @param string $col
	 */
	public function addSortableColumn(string $col)
	{
		$this->sortableColumns[] = $col;
	}

	/**
	 * @param string $orig
	 * @param string|null $translated
	 */
	public function addMapping(string $orig, string $translated = null)
	{
		$this->mapping[$orig] = $translated ?? Strings::toCamelCase($orig);
	}

	/**
	 * @return array
	 */
	public function getQueryColumns(): array
	{
		return $this->queryColumns;
	}

	/**
	 * @param array $columns
	 * @return $this
	 */
	public function setQueryColumns(array $columns)
	{
		$this->queryColumns = $columns;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function displaySearchForm(): bool
	{
		return !empty($this->queryColumns) || $this->getControl()->getComponent('itemFilter', false);
	}

	/**
	 * @param string $col
	 * @param string $fallback
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function orderBy(string $col = null, string $fallback)
	{
		if (!in_array($col, $this->sortableColumns)
			&& !in_array($fallback, $this->sortableColumns)
		) {
			throw new \InvalidArgumentException(
				sprintf('Cannot order by "%s" neither by "%s". Columns are not sortable', $col, $fallback)
			);
		}

		$this->orderBy = in_array($col, $this->sortableColumns) ? $col : $fallback;
		return $this;
	}

	/**
	 * @param bool $asc
	 * @return $this
	 */
	public function sortAsc($asc = true)
	{
		if (false === $asc
			|| $asc === Sorter::DESC
		) {
			$this->sortAsc = false;
		} else {
			$this->sortAsc = true;
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSortAsc(): bool
	{
		return $this->sortAsc;
	}

	/**
	 * @param \Sellastica\UI\Pagination\Pagination|null $pagination
	 * @return $this
	 */
	public function setPagination(?Pagination $pagination)
	{
		$this->pagination = $pagination;
		return $this;
	}

	/**
	 * @return null|\Sellastica\UI\Pagination\Pagination
	 */
	public function getPagination(): ?\Sellastica\UI\Pagination\Pagination
	{
		return $this->pagination;
	}

	/**
	 * @return DataGridHeader
	 */
	public function getHeader(): DataGridHeader
	{
		return $this->header;
	}

	/**
	 * @param callable $resultsCallback
	 */
	public function setResultsCallback(callable $resultsCallback): void
	{
		$this->resultsCallback = $resultsCallback;
	}

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection|null $filterRules
	 * @param \Sellastica\UI\Pagination\Pagination|null $pagination
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @return iterable
	 */
	public function getResults(
		FilterRuleCollection $filterRules = null,
		Pagination $pagination = null,
		\Sellastica\Entity\Configuration $configuration = null
	): iterable
	{
		$filterRules = $filterRules ?? $this->filterRules;
		$pagination = $pagination ?? $this->pagination;

		if (!isset($configuration)) {
			$configuration = new \Sellastica\Entity\Configuration();
		}

		if ($pagination) {
			$configuration->setPaginator($pagination);
		}

		if ($this->getOrderBy()) {
			$configuration->addSorterRule($this->getOrderBy(), $this->sortAsc);
		}

		return call_user_func_array(
			$this->resultsCallback, [
				$this->resolveFilterRules($filterRules),
				$configuration
			]
		);
	}

	/**
	 * @param FilterRuleCollection $filterRules
	 * @return FilterRuleCollection
	 */
	public function resolveFilterRules(FilterRuleCollection $filterRules): FilterRuleCollection
	{
		if ($filterRules && sizeof($this->mapping)) {
			$filterRules = $filterRules->applyMapping($this->mapping); //creates clone
		} else {
			$filterRules = clone $filterRules; //do not change state of original rules
		}

		if (sizeof($this->queryColumns)) {
			$queryColumns = [];
			foreach ($this->queryColumns as $column) {
				$queryColumns[$column] = isset($this->mapping[$column]) ? $this->mapping[$column] : null;
			}

			$filterRules = $filterRules->expand('q', $queryColumns);
		}

		return $filterRules;
	}

	/**
	 * @param \Sellastica\Entity\Configuration|null $configuration
	 * @return iterable
	 */
	public function getStandardResults(\Sellastica\Entity\Configuration $configuration = null): iterable
	{
		return $this->getResults(null, null, $configuration);
	}

	/**
	 * @param bool $map
	 * @return string|null Column name
	 */
	public function getOrderBy(bool $map = true)
	{
		return (true === $map && isset($this->mapping[$this->orderBy]))
			? $this->mapping[$this->orderBy]
			: $this->orderBy;
	}

	/**
	 * @param string $class
	 * @return DataGridRow
	 */
	public function addRow($class = null)
	{
		return $this->rows[] = new DataGridRow($this->dataGridControl, $class);
	}

	/**
	 * @return DataGridRow[]
	 */
	public function getRows()
	{
		return $this->rows;
	}

    /**
     * @return bool
     */
    public function hasRows(): bool
	{
        return (bool)$this->rows;
	}

	/**
	 * @return UI\Control
	 */
	public function getControl()
	{
		return $this->control;
	}

	/**
	 * @return string
	 */
	public function getBulkPrimaryKey(): string
	{
		return $this->bulkPrimaryKey;
	}

	/**
	 * @param string $bulkPrimaryKey
	 */
	public function setBulkPrimaryKey(string $bulkPrimaryKey)
	{
		$this->bulkPrimaryKey = $bulkPrimaryKey;
	}

	/**
	 * @param string $title
	 * @param string $action
	 * @param bool $confirm
	 * @param array $dataAttributes
	 * @param string|null $icon
	 * @param string|null $linkClass
	 */
	public function addBulkAction(
		string $title,
		string $action = null,
		bool $confirm = false,
		array $dataAttributes = [],
		string $icon = null,
		string $linkClass = null
	)
	{
		$this->bulkActions[] = [
			'title' => $title,
			'action' => $action,
			'confirm' => $confirm,
			'data' => $dataAttributes,
			'icon' => $icon,
			'linkClass' => $linkClass,
		];
	}

	public function addBulkActionDivider(): void
	{
		$this->bulkActions[] = [];
	}

	/**
	 * @return array
	 */
	public function getBulkActions(): array
	{
		return $this->bulkActions;
	}

	/**
	 * @return bool
	 */
	public function isBulkAllPages(): bool
	{
		return $this->bulkAllPages;
	}

	/**
	 * @param bool $bulkAllPages
	 */
	public function setBulkAllPages(bool $bulkAllPages): void
	{
		$this->bulkAllPages = $bulkAllPages;
	}
}
