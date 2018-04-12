<?php
namespace Sellastica\DataGrid\Component;

use Nette;
use Nette\Application\UI;
use Nette\Localization\ITranslator;
use Sellastica\AdminUI\Component\BaseControl;
use Sellastica\DataGrid\Entity\IAdminFilterRepository;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\DataGrid\Model\Tab;
use Sellastica\Entity\EntityManager;
use Sellastica\UI\Presenter\IPresenter;

/**
 * @property-read IPresenter|UI\Presenter $presenter
 */
class Tabs extends BaseControl
{
	/** @var array */
	public $onDelete = [];

	/** @var Nette\Security\User */
	private $user;
	/** @var ITranslator */
	private $translator;
	/** @var FilterRuleCollection */
	private $filterRules;
	/** @var \Sellastica\DataGrid\Entity\IAdminFilterRepository */
	private $adminFilterRepository;
	/** @var \Sellastica\Entity\EntityManager */
	private $entityManager;
	/** @var int */
	private $filterId;
	/** @var bool */
	private $displayCustomTab;
	/** @var bool */
	private $displayAllResultsTab = true;


	/**
	 * @param FilterRuleCollection $filterRules
	 * @param bool $displayCustomTab
	 * @param int $filterId
	 * @param \Sellastica\DataGrid\Entity\IAdminFilterRepository $adminFilterRepository
	 * @param Nette\Security\User $user
	 * @param \Sellastica\Entity\EntityManager $entityManager
	 * @param ITranslator $translator
	 */
	public function __construct(
		FilterRuleCollection $filterRules,
		bool $displayCustomTab,
		int $filterId = null,
		IAdminFilterRepository $adminFilterRepository,
		Nette\Security\User $user,
		EntityManager $entityManager,
		ITranslator $translator
	)
	{
		parent::__construct();
		$this->translator = $translator;
		$this->filterRules = $filterRules;
		$this->adminFilterRepository = $adminFilterRepository;
		$this->user = $user;
		$this->entityManager = $entityManager;
		$this->filterId = $filterId;
		$this->displayCustomTab = $displayCustomTab;
	}

	/**
	 * @param int $id
	 */
	public function handleRemove($id)
	{
		$savedFilter = $this->adminFilterRepository->findOneBy(
			['id' => $id, 'adminUserId' => $this->user->getId()]
		);
		if (isset($savedFilter)) {
			$this->entityManager->remove($savedFilter);
		}

		$params = [
			DataGridControl::PARAM_FILTER_ID => null,
			'page' => null,
		];
		foreach ($this->filterRules as $filterRule) { //if saved search is active and rules are not present in the url
			if (!array_key_exists($filterRule->getKey(), $params)) {
				$params[$filterRule->getKey()] = null; //clear all params
			}
		}

		$this->onDelete($params);
	}

	/**
	 * @return Tab[]
	 */
	private function getTabs(): array
	{
		$tabs = [];
		$emptyCriteria = [];
		foreach ($this->filterRules as $criterion => $title) {
			$emptyCriteria[$criterion] = null;
		}

		//all results tab
		if ($this->displayAllResultsTab) {
			$defaultTab = new Tab(
				$this->translator->translate('admin.grid.tabs.all'),
				$this->presenter->link('default', $emptyCriteria + [DataGridControl::PARAM_FILTER_ID => null, 'page' => null])
			);
			$defaultTab->activate();
			$activeTab = $defaultTab;

			$tabs[] = $defaultTab;
		}

		/** @var \Sellastica\DataGrid\Entity\AdminFilter[] $savedFilters */
		$savedFilters = $this->adminFilterRepository->findBy(['presenter' => $this->presenter->getShortName()]);
		foreach ($savedFilters as $savedFilter) {
			$tab = new Tab(
				$savedFilter->getTitle(),
				$this->presenter->link('default', $emptyCriteria + [DataGridControl::PARAM_FILTER_ID => $savedFilter->getId(), 'page' => null])
			);
			$tab->setId($savedFilter->getId());
			if ($this->filterId === $savedFilter->getId()
				&& !$this->displayCustomTab) {
				if (isset($activeTab)) {
					$activeTab->deactivate();
				}

				$tab->activate();
				$tab->makeDeletable();
				$activeTab = $tab;
			}

			$tabs[] = $tab;
		}

		if ($this->displayCustomTab || $this->filterRules->changed()) { //rules could be changed with search POST request
			$tab = new Tab(
				$this->translator->translate('admin.grid.tabs.unsaved')
			);
			$activeTab->deactivate();
			$tab->activate();
			$tab->makeSaveable();
			$tabs[] = $tab;
		}

		return $tabs;
	}

	public function hideAllResultsTab(): void
	{
		$this->displayAllResultsTab = false;
	}

	/**
	 * @param array $params
	 */
	protected function beforeRender(array $params = [])
	{
		$this->template->tabs = $this->getTabs();
	}
}
