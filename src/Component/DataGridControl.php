<?php
namespace Sellastica\DataGrid\Component;

use Nette\Http\Request;
use Sellastica\AdminUI\Component\BaseControl;
use Sellastica\DataGrid\Model\FilterRule;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\Mapping\IRepository;
use Sellastica\UI\Pagination\Pagination;
use Sellastica\UI\Pagination\PaginationFactory;

class DataGridControl extends BaseControl
{
	const DEFAULT_PAGINATION = 25,
		PARAM_FILTER_ID = 'filterId';

	private const MAX_BULK_PAGES = 100;

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
	/** @var int|null */
	private $filterId;
	/** @var bool */
	private $displayCustomTab = false;
	/** @var Request */
	private $request;
	/** @var \Sellastica\DataGrid\Component\ContentFormFactory */
	private $contentFormFactory;
	/** @var \Sellastica\Entity\EntityManager */
	private $em;
	/** @var bool */
	private $displayTabs = true;
	/** @var bool */
	private $displaySaveSearchForm = true;
	/** @var \Nette\Localization\ITranslator */
	private $translator;


	/**
	 * @param IRepository $repository
	 * @param Request $request
	 * @param FilterRuleCollection $filterRules
	 * @param int $filterId
	 * @param ISearchFormFactory $searchFormFactory
	 * @param ITabsFactory $searchTabsFactory
	 * @param ITagsFactory $searchTagsFactory
	 * @param ISaveSearchFormFactory $saveSearchFormFactory
	 * @param \Sellastica\DataGrid\Component\ContentFormFactory $contentFormFactory
	 * @param \Sellastica\Entity\EntityManager $em
	 * @param \Nette\Localization\ITranslator $translator
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
		ContentFormFactory $contentFormFactory,
		\Sellastica\Entity\EntityManager $em,
		\Nette\Localization\ITranslator $translator
	)
	{
		parent::__construct();
		$this->searchFormFactory = $searchFormFactory;
		$this->searchTabsFactory = $searchTabsFactory;
		$this->searchTagsFactory = $searchTagsFactory;
		$this->saveSearchFormFactory = $saveSearchFormFactory;
		$this->repository = $repository;
		$this->filterId = $filterId;
		$this->em = $em;
		$this->translator = $translator;

		if (isset($filterRules)) {
			$this->displayCustomTab = $this->displayCustomTab($filterRules);
			$this->filterRules = $this->mergeFilterRules($filterRules, $filterId);
		}

		$this->request = $request;
		$this->contentFormFactory = $contentFormFactory;
	}

	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection|FilterRule[] $rules
	 * @return bool
	 */
	private function displayCustomTab(FilterRuleCollection $rules): bool
	{
		if (!$this->displaySaveSearchForm) {
			return false;
		}

		foreach ($rules as $rule) {
			if ($rule->isInTags() && !$rule->isDefault()) {
				return true;
			}
		}

		return false;
	}

	public function hideTabs(): void
	{
		$this->displayTabs = false;
	}

	/**
	 * @return \Sellastica\DataGrid\Model\DataGrid
	 */
	public function createGrid(): \Sellastica\DataGrid\Model\DataGrid
	{
		$this->dataGrid = new \Sellastica\DataGrid\Model\DataGrid(
			$this->getPresenter(),
			$this,
			$this->repository,
			$this->filterRules
		);
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
	 * @param array $bulkValues
	 * @return iterable
	 */
	public function getBulksById(array $bulkValues): iterable
	{
		$bulkIds = [];
		foreach ($bulkValues as $bulkId => $bulkValue) {
			if (is_scalar($bulkValue)) {
				$bulkIds[] = (int)$bulkValue;
			} elseif ($bulkValue->bulk_id) {
				$bulkIds[] = $bulkId;
			}
		}

		if (!sizeof($bulkIds)) {
			//e.g. by the GET request - return empty array
			$this->repository->getEmptyCollection();
		}

		$rules = clone $this->dataGrid->getFilterRules();
		$rules->addSet($this->dataGrid->getBulkPrimaryKey(), $bulkIds)
			->setMapping($this->dataGrid->getBulkPrimaryKey());

		$pagination = clone $this->dataGrid->getPagination();
		$pagination->setPage(1);
		return $this->dataGrid->getResults($rules, $pagination);
	}

	/**
	 * @param int $page
	 * @return iterable
	 */
	public function getBulksByPage(int $page): iterable
	{
		//too much results protection
		if ($this->dataGrid->getPagination()->getPageCount() > self::MAX_BULK_PAGES) {
			throw new \Sellastica\DataGrid\Exception\DataGridException(
				'Cannot run bulk actions if number of pages is grater than ' . self::MAX_BULK_PAGES
			);
		}

		if ($this->dataGrid->getPagination()->getPageCount() !== null
			&& $page > $this->dataGrid->getPagination()->getPageCount()) {
			return $this->repository->getEmptyCollection();
		} else {
			$this->dataGrid->getPagination()->setPage($page);
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
			/** @var \Sellastica\DataGrid\Entity\AdminFilter $savedSearch */
			$savedSearch = $this->em->getRepository(\Sellastica\DataGrid\Entity\AdminFilter::class)->find($filterId);
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
		$control = $this->searchFormFactory->create($this->filterRules, !empty($this->dataGrid->getQueryColumns()));
		$control->onSuccess[] = function (array $params) {
			$this->onSuccess($params);
		};
		return $control;
	}

	/**
	 * @return Tabs
	 */
	protected function createComponentSearchTabs(): Tabs
	{
		$control = $this->searchTabsFactory->create($this->filterRules, $this->displayCustomTab, $this->filterId);
		$control->onDelete[] = function (array $params) {
			$this->onDelete($params);
		};
		return $control;
	}

	public function hideAllResultsTab(): void
	{
		/** @var \Sellastica\DataGrid\Component\Tabs $tabs */
		$tabs = $this->getComponent('searchTabs');
		$tabs->hideAllResultsTab();
	}

	public function deactivateAllResultsTab(): void
	{
		/** @var \Sellastica\DataGrid\Component\Tabs $tabs */
		$tabs = $this->getComponent('searchTabs');
		$tabs->deactivateAllResultsTab();
	}

	/**
	 * @param string $title
	 * @param string $url
	 * @return \Sellastica\DataGrid\Model\Tab
	 */
	public function createTab(string $title, string $url): \Sellastica\DataGrid\Model\Tab
	{
		/** @var \Sellastica\DataGrid\Component\Tabs $tabs */
		$tabs = $this->getComponent('searchTabs');
		return $tabs->createTab($title, $url);
	}

	/**
	 * @return Tags
	 */
	protected function createComponentSearchTags(): Tags
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
	protected function createComponentPagination(): \Sellastica\AdminUI\Component\Pagination
	{
		$control = new \Sellastica\AdminUI\Component\Pagination(
			$this->getPagination(),
			$this->translator
		);
		$control->onSuccess[] = function (array $params) {
			$this->onSuccess($params);
		};
		return $control;
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
	 * @return \Sellastica\UI\Form\Form
	 */
	protected function createComponentContentForm(): \Sellastica\UI\Form\Form
	{
		return $this->contentFormFactory->create();
	}

	/**
	 * @return \Sellastica\UI\Form\Form
	 */
	public function getContentForm(): \Sellastica\UI\Form\Form
	{
		return $this['contentForm'];
	}

	/**
	 * @return bool
	 */
	public function displaySaveSearchForm(): bool
	{
		return $this->displaySaveSearchForm;
	}

	/**
	 * @param bool $displaySaveSearchForm
	 */
	public function setDisplaySaveSearchForm(bool $displaySaveSearchForm): void
	{
		$this->displaySaveSearchForm = $displaySaveSearchForm;
		if (!$displaySaveSearchForm) {
			$this->displayCustomTab = false;
		}
	}

	/**
	 * @param array $params
	 */
	protected function beforeRender(array $params = [])
	{
		$this->template->striped = !empty($params['striped']);
		$this->template->grid = $this->dataGrid;
		$this->template->displayTabs = $this->displayTabs;
	}

	public function renderCardView()
	{
		$this->setFile(__DIR__ . '/DataGridControl.cardView.latte');
		$this->template->grid = $this->dataGrid;
		$this->template->displayTabs = $this->displayTabs;
		$this->template->render();
	}
}
