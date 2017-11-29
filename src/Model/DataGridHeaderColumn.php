<?php
namespace Sellastica\DataGrid\Model;

use Sellastica\Entity\Sorter;

class DataGridHeaderColumn
{
	use \Nette\SmartObject;

	const TYPE_TEXT = 'text',
      TYPE_SORTABLE = 'sortable',
	   TYPE_CHECKBOX = 'checkbox',
		TYPE_BULK_ACTIONS = 'bulkActions';

	/** @var array */
	private $class = [];
	/** @var string */
	private $type;
	/** @var string */
	private $title;
	/** @var string */
	private $sortableColumn;
	/** @var bool */
	private $ascByDefault;
	/** @var DataGrid */
	private $dataGrid;

	/**
	 * @param DataGrid $dataGrid
	 * @param string $type
	 * @param string $title
	 * @param string $class
	 * @param string $sortableColumn
	 * @param bool $ascByDefault
	 */
	public function __construct(
		DataGrid $dataGrid,
		string $type,
		string $title = null,
		string $class = null,
		string $sortableColumn = null,
		$ascByDefault = true
	)
	{
		$this->type = $type;
		$this->title = $title;
		$this->sortableColumn = $sortableColumn;
		$this->ascByDefault = $ascByDefault;
		$this->dataGrid = $dataGrid;
		if (isset($class)) {
			$this->class[] = $class;
		}
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return bool
	 */
	public function isText(): bool
	{
		return $this->type === self::TYPE_TEXT;
	}

	/**
	 * @return bool
	 */
	public function isSortable(): bool
	{
		return $this->type === self::TYPE_SORTABLE;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getSortableColumn()
	{
		return $this->sortableColumn;
	}

	/**
	 * @return string
	 */
	public function getClass()
	{
		return join(' ', $this->class);
	}

	/**
	 * @param string $class
	 */
	public function setClass(string $class)
	{
		$this->class[] = $class;
	}

	/**
	 * @return boolean
	 */
	public function isAscByDefault()
	{
		return $this->ascByDefault;
	}

	/**
	 * @return bool
	 */
	public function isVisible(): bool
	{
		return $this->getSortableColumn() === $this->dataGrid->getOrderBy(false);
	}

	/**
	 * @return bool
	 */
	public function isSortAsc(): bool
	{
		return $this->dataGrid->isSortAsc();
	}

	/**
	 * Creates link for listing template
	 * @return string
	 */
	public function getUrl()
	{
		if ($this->isVisible()) {
			$sort = $this->isSortAsc() ? Sorter::DESC : Sorter::ASC;
		} else {
			$sort = $this->ascByDefault ? Sorter::ASC : Sorter::DESC;
		}

		return $this->dataGrid->getControl()->link('this', ['orderBy' => $this->sortableColumn, 'sort' => $sort]);
	}
}