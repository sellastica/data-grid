<?php
namespace Sellastica\DataGrid\Model;

class DataGridHeader
{
	/** @var array */
	private $cols = [];
	/** @var DataGrid */
	private $dataGrid;

	/**
	 * @param \Sellastica\DataGrid\Model\DataGrid $dataGrid
	 */
	public function __construct(DataGrid $dataGrid)
	{
		$this->dataGrid = $dataGrid;
	}

	/**
	 * @param string $title
	 * @return DataGridHeaderColumn
	 */
	public function addText(string $title)
	{
		$this->cols[] = $col = new DataGridHeaderColumn($this->dataGrid, DataGridHeaderColumn::TYPE_TEXT, $title, 'not-allowed');
		return $col;
	}

	/**
	 * @return DataGridHeaderColumn
	 */
	public function addEmpty()
	{
		$this->cols[] = $col = new DataGridHeaderColumn($this->dataGrid, DataGridHeaderColumn::TYPE_TEXT);
		return $col;
	}

	/**
	 * @param string $title
	 * @param string $sortableColumn
	 * @param bool $ascByDefault
	 * @return DataGridHeaderColumn
	 */
	public function addSortable(
		string $title,
		string $sortableColumn,
		bool $ascByDefault = true
	)
	{
		$this->dataGrid->addSortableColumn($sortableColumn);
		$this->cols[] = $col = new DataGridHeaderColumn(
			$this->dataGrid,
			DataGridHeaderColumn::TYPE_SORTABLE,
			$title,
			null,
			$sortableColumn,
			$ascByDefault
		);
		return $col;
	}

	/**
	 * @return DataGridHeaderColumn
	 */
	public function addCheckbox()
	{
		$this->cols[] = $col = new DataGridHeaderColumn($this->dataGrid, DataGridHeaderColumn::TYPE_CHECKBOX);
		return $col;
	}

	/**
	 * @return DataGridHeaderColumn
	 */
	public function addBulkActions()
	{
		$this->cols[] = $col = new DataGridHeaderColumn($this->dataGrid, DataGridHeaderColumn::TYPE_BULK_ACTIONS);
		return $col;
	}

	/**
	 * @return DataGridHeaderColumn[]
	 */
	public function getColumns()
	{
		return $this->cols;
	}
}