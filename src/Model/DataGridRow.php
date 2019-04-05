<?php
namespace Sellastica\DataGrid\Model;

class DataGridRow
{
	/** @var string|null */
	private $htmlId;
	/** @var string|null */
	private $class;
	/** @var DataGridColumn[] */
	private $columns = [];
	/** @var mixed|null */
	private $bulkId;
	/** @var \Sellastica\DataGrid\Component\DataGridControl */
	private $dataGridControl;
	/** @var array */
	private $data = [];


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
	public function addColumn($class = null): DataGridColumn
	{
		return $this->columns[] = new DataGridColumn($class);
	}

	/**
	 * @return DataGridColumn
	 */
	public function addButtonsColumn(): DataGridColumn
	{
		return $this->columns[] = new DataGridColumn('min-width no-wrap text-right');
	}

	/**
	 * @return DataGridColumn[]
	 */
	public function getColumns(): array
	{
		return $this->columns;
	}

	/**
	 * @return null|string
	 */
	public function getHtmlId(): ?string
	{
		return $this->htmlId;
	}

	/**
	 * @param null|string $htmlId
	 * @return DataGridRow
	 */
	public function setHtmlId(?string $htmlId): DataGridRow
	{
		$this->htmlId = $htmlId;
		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getClass(): ?string
	{
		return $this->class;
	}

	/**
	 * @param string $class
	 * @return DataGridRow
	 */
	public function setClass(string $class): DataGridRow
	{
		$this->class = $class;
		return $this;
	}

	/**
	 * @return mixed|null
	 */
	public function getBulkId()
	{
		return $this->bulkId;
	}

	/**
	 * @param string $name
	 * @param $value
	 * @return DataGridRow
	 */
	public function addData(string $name, $value): DataGridRow
	{
		$this->data[$name] = $value;
		return $this;
	}

	/**
	 * @param string|null $name
	 * @return mixed
	 */
	public function getData(string $name = null)
	{
		return isset($name)
			? ($this->data[$name] ?? null)
			: $this->data;
	}

	/**
	 * @param $bulkId
	 * @return $this
	 */
	public function setBulkId($bulkId): DataGridRow
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