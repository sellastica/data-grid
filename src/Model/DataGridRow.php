<?php
namespace Sellastica\DataGrid\Model;

class DataGridRow
{
	/** @var string */
	private $class;
	/** @var array */
	private $columns = [];
	/** @var mixed|null */
	private $bulkId;
	/** @var \Sellastica\DataGrid\Component\DataGridControl */
	private $dataGridControl;


	/**
	 * @param \Sellastica\DataGrid\Component\DataGridControl $dataGridControl
	 * @param string $class
	 */
	public function __construct(
		\Sellastica\DataGrid\Component\DataGridControl $dataGridControl,
		$class = null
	)
	{
		$this->dataGridControl = $dataGridControl;
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
	 * @return mixed|null
	 */
	public function getBulkId()
	{
		return $this->bulkId;
	}

	/**
	 * @param $bulkId
	 * @return $this
	 */
	public function setBulkId($bulkId)
	{
		$this->bulkId = $bulkId;

		//create form checkbox
		$contentForm = $this->dataGridControl
			->getContentForm();
		$contentForm['content']
			->addContainer($bulkId)
			->addPrettyCheckbox('bulk_id')
			->setAttribute('class', 'bulk-checkbox');

		return $this;
	}
}