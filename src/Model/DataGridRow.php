<?php
namespace Sellastica\DataGrid\Model;

class DataGridRow
{
	/** @var string */
	private $class;
	/** @var array */
	private $columns = [];
	/** @var int|null */
	private $bulkId;


	/**
	 * @param string $class
	 */
	public function __construct($class = null)
	{
		$this->class = $class;
	}

	/**
	 * @param string $class
	 * @return DataGridColumn
	 */
	public function addColumn($class = null)
	{
		return $this->columns[] = new DataGridColumn($class);
	}

	/**
	 * @return DataGridColumn
	 */
	public function addButtonsColumn()
	{
		return $this->columns[] = new DataGridColumn('min-width no-wrap text-right');
	}

	/**
	 * @return DataGridColumn[]
	 */
	public function getColumns()
	{
		return $this->columns;
	}

	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * @param string $class
	 */
	public function setClass(string $class)
	{
		$this->class = $class;
	}

	/**
	 * @return int
	 * @throws \UnexpectedValueException
	 */
	public function getBulkId(): int
	{
		if (!$this->bulkId) {
			throw new \UnexpectedValueException('Bulk ID is not set on the data grid row');
		}

		return $this->bulkId;
	}

	/**
	 * @param int|null $bulkId
	 * @return $this
	 */
	public function setBulkId(int $bulkId)
	{
		$this->bulkId = $bulkId;
		return $this;
	}
}