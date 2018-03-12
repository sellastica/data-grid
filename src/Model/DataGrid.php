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
	 * @return DataGridHeader
	 */
	public function getHeader(): DataGridHeader
	{
		return $this->header;
	}

	/**
	 * @return EntityCollection|IEntity[]
	 */
	public function getResults(): EntityCollection
	{
		$configuration = new \Sellastica\Entity\Configuration();
		if (isset($this->pagination)) {
			$configuration->setPaginator($this->pagination);
		}

		if ($this->getOrderBy()) {
			$configuration->addSorterRule($this->getOrderBy(), $this->sortAsc);
		}

		if ($this->filterRules && sizeof($this->mapping)) {
			$filterRules = $this->filterRules->applyMapping($this->mapping);
		} else {
			$filterRules = clone $this->filterRules; //do not change state of original rules
		}

		if (sizeof($this->queryColumns)) {
			$queryColumns = [];
			foreach ($this->queryColumns as $column) {
				$queryColumns[$column] = isset($this->mapping[$column]) ? $this->mapping[$column] : null;
			}

			$filterRules = $filterRules->expand('q', $queryColumns);
		}

		return $this->repository->findByFilterRules($filterRules, $configuration);
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
	 */
	public function addBulkAction(
		string $title,
		string $action = null,
		bool $confirm = false,
		array $dataAttributes = []
	)
	{
		$this->bulkActions[] = [
			'title' => $title,
			'action' => $action,
			'confirm' => $confirm,
			'data' => $dataAttributes,
		];
	}

	/**
	 * @return array
	 */
	public function getBulkActions(): array
	{
		return $this->bulkActions;
	}
}
