<?php
namespace Sellastica\DataGrid\Component;

use Nette\Http\Request;
use Sellastica\AdminUI\Component\BaseControl;
use Sellastica\DataGrid\Entity\IAdminFilterRepository;
use Sellastica\DataGrid\Model\FilterRule;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Entity\EntityCollection;
use Sellastica\Entity\Mapping\IRepository;
use Sellastica\UI\Pagination\Pagination;
use Sellastica\UI\Pagination\PaginationFactory;

class DataGridControl extends BaseControl
{
	const DEFAULT_PAGINATION = 25;
	const PARAM_FILTER_ID = 'filterId';

	/** @var array */
	public $onSuccess = [];
	/** @var array */
	public $onDelete = [];

	/** @var Pagination */
	private $pagination;
	/** @var ITabsFactory */
	private $searchTabsFactory;
	/** @var ITagsFactory */
	private $searchTagsFactory;
	/** @var ISearchFormFactory */
	private $searchFormFactory;
	/** @var ISaveSearchFormFactory */
	private $saveSearchFormFactory;
	/** @var \Sellastica\DataGrid\Model\DataGrid */
	private $dataGrid;
	/** @var IRepository */
	private $repository;
	/** @var FilterRuleCollection */
	private $filterRules;
	/** @var IAdminFilterRepository */
	private $adminFilterRepository;
	/** @var int|null */
	private $filterId;
	/** @var bool */
	private $displayCustomTab = false;
	/** @var Request */
	private $request;


	/**
	 * @param IRepository $repository
	 * @param Request $request
	 * @param FilterRuleCollection $filterRules
	 * @param int $filterId
	 * @param ISearchFormFactory $searchFormFactory
	 * @param ITabsFactory $searchTabsFactory
	 * @param ITagsFactory $searchTagsFactory
	 * @param ISaveSearchFormFactory $saveSearchFormFactory
	 * @param \Sellastica\DataGrid\Entity\IAdminFilterRepository $adminFilterRepository
	 */
	public function __construct(
		IRepository $repository,
		Request $request,
		FilterRuleCollection $filterRules = null,
		int $filterId = null,
		ISearchFormFactory $searchFormFactory,
		ITabsFactory $searchTabsFactory,
		ITagsFactory $searchTagsFactory,
		ISaveSearchFormFactory $saveSearchFormFactory,
		IAdminFilterRepository $adminFilterRepository
	)
	{
		parent::__construct();
		$this->searchFormFactory = $searchFormFactory;
		$this->searchTabsFactory = $searchTabsFactory;
		$this->searchTagsFactory = $searchTagsFactory;
		$this->saveSearchFormFactory = $saveSearchFormFactory;
		$this->repository = $repository;
		$this->adminFilterRepository = $adminFilterRepository;
		$this->filterId = $filterId;

		if (isset($filterRules)) {
			$this->displayCustomTab = $this->displayCustomTab($filterRules);
			$this->filterRules = $this->mergeFilterRules($filterRules, $filterId);
		}

		$this->request = $request;
	}

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection|FilterRule[] $rules
	 * @return bool
	 */
	private function displayCustomTab(FilterRuleCollection $rules): bool
	{
		foreach ($rules as $rule) {
			if ($rule->isInTags() && !$rule->isDefault()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return \Sellastica\DataGrid\Model\DataGrid
	 */
	public function createGrid(): \Sellastica\DataGrid\Model\DataGrid
	{
		$this->dataGrid = new \Sellastica\DataGrid\Model\DataGrid($this->getPresenter(), $this->repository, $this->filterRules);
		$this->dataGrid->setPagination($this->getPagination());
		return $this->dataGrid;
	}

	/**
	 * @return \Sellastica\DataGrid\Model\DataGrid
	 */
	public function getDataGrid(): \Sellastica\DataGrid\Model\DataGrid
	{
		return $this->dataGrid;
	}

	/**
	 * @return \Sellastica\Entity\Entity\EntityCollection
	 */
	public function getBulks(): EntityCollection
	{
		$allPages = (bool)$this->request->getPost('all_pages');
		$bulkIds = $this->request->getPost('bulk_id');

		if (!$allPages && empty($bulkIds)) {
			//e.g. by the GET request - return empty array
			return $this->dataGrid->getResults()->clear();
		}

		if ($allPages) {
			$this->dataGrid->setPagination(null);
		} else {
			$this->dataGrid->getFilterRules()->addSet($this->dataGrid->getBulkPrimaryKey(), $bulkIds);
		}

		return $this->dataGrid->getResults();
	}

	/**
	 * Merges filter rules definition with saved search content
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $rules
	 * @param int $filterId
	 * @return \Sellastica\DataGrid\Model\FilterRuleCollection
	 */
	private function mergeFilterRules(FilterRuleCollection $rules, int $filterId = null)
	{
		if (isset($filterId)) {
			$savedSearch = $this->adminFilterRepository->find($filterId);
		}

		if (isset($savedSearch)) {
			foreach ($savedSearch->getRules() as $savedRule) {
				if (isset($rules[$savedRule->getCriterion()])) {
					$rule = $rules[$savedRule->getCriterion()];
					if (!$rule->isDefault()) {
						//rule present in the url has higher priority than saved one
						continue;
					}

					$criterion = $rule->withValue(
						$savedRule->getValue()
					);
					$rules->addRule($criterion);
				}
			}
		}

		$rules = $rules->getActiveRules();
		$rules->saveState();
		return $rules;
	}

	/**
	 * @return SearchForm
	 */
	protected function createComponentSearchForm(): SearchForm
	{
		$control = $this->searchFormFactory->create($this->filterRules);
		$control->onSuccess[] = function (array $params) {
			$this->onSuccess($params);
		};
		return $control;
	}

	/**
	 * @return Tabs
	 */
	protected function createComponentSearchTabs()
	{
		$control = $this->searchTabsFactory->create($this->filterRules, $this->displayCustomTab, $this->filterId);
		$control->onDelete[] = function (array $params) {
			$this->onDelete($params);
		};
		return $control;
	}

	/**
	 * @return Tags
	 */
	protected function createComponentSearchTags()
	{
		return $this->searchTagsFactory->create($this->filterRules);
	}

	/**
	 * @return SaveSearchForm
	 */
	protected function createComponentSaveSearchForm(): SaveSearchForm
	{
		$control = $this->saveSearchFormFactory->create($this->filterRules);
		$control->onSuccess[] = function (array $params) {
			$this->onSuccess($params);
		};

		return $control;
	}

	/**
	 * @return \Sellastica\AdminUI\Component\Pagination
	 */
	protected function createComponentPagination()
	{
		return new \Sellastica\AdminUI\Component\Pagination($this->getPagination());
	}

	/**
	 * @return \Sellastica\UI\Pagination\Pagination
	 */
	protected function getPagination(): Pagination
	{
		if (!isset($this->pagination)) {
			$paginationFactory = new PaginationFactory($this->request);
			$this->pagination = $paginationFactory->create(
				$this->getPresenter()->getParameter('itemsPerPage', self::DEFAULT_PAGINATION), Pagination::RESULTS_LIMIT
			);
		}

		return $this->pagination;
	}

	/**
	 * @param array $params
	 */
	protected function beforeRender(array $params = [])
	{
		$this->template->striped = !empty($params['striped']);
		$this->template->grid = $this->dataGrid;
	}
}
