<?php
namespace Sellastica\DataGrid\Bridges;

use Sellastica\DataGrid\Component\DataGridControl;
use Sellastica\DataGrid\Component\IDataGridControlFactory;
use Sellastica\DataGrid\Model\FilterRule;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Mapping\IRepository;

/**
 * @property-read \stdClass $payload
 * @method getParameter($name, $default = null)
 * @method string link($destination, $args = [])
 * @method redirect($code, $destination = null, $args = [])
 * @method redirectUrl($url, $code = null, bool $redirectIfAjax = false)
 * @method redrawControl($snippet = null, $redraw = true)
 */
trait TDataGrid
{
	/** @persistent */
	public $page;
	/** @persistent */
	public $orderBy;
	/** @persistent */
	public $sort;
	/** @persistent */
	public $itemsPerPage;
	/** @persistent */
	public $filterId;
	/** @persistent */
	public $q;

	/** @var IDataGridControlFactory @inject */
	public $dataGrid;
	/** @var FilterRuleCollection|FilterRule[] */
	protected $filterRules = [];


	protected function initializeDataGrid()
	{
		//redirect to custom search URL, if saved search and also some custom search parameters are present in the URL
		if ($this->getParameter(DataGridControl::PARAM_FILTER_ID)) {
			foreach ($this->getFilterRules() as $rule) {
				if (!is_null($this->getParameter($rule->getKey()))) {
					$this->redirect('this', [DataGridControl::PARAM_FILTER_ID => null]);
				}
			}
		}
	}

	/**
	 * @param \Sellastica\Entity\Mapping\IRepository $repository
	 * @return DataGridControl
	 */
	protected function createDatagrid(IRepository $repository): DataGridControl
	{
		$control
			= $this['dataGrid'] //attach to presenter
			= $this->dataGrid->create($repository, $this->getFilterRules(), $this->filterId);

		$control->onSuccess[] = function (array $params) {
			$this->payload->postGet = true;
			$this->payload->url = $url = $this->link('this', $params);
			$this->redrawControl('dataGrid');
			$this->redirectUrl($url);
		};
		$control->onDelete[] = function (array $params) {
			$this->payload->postGet = true;
			$this->payload->url = $url = $this->link('this', $params);
			$this->redrawControl('dataGrid');
			$this->redirectUrl($url);
		};

		return $control;
	}

	/**
	 * @return FilterRuleCollection
	 */
	abstract protected function getFilterRules(): FilterRuleCollection;
}
