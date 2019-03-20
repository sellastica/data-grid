<?php
namespace Sellastica\DataGrid\Component;

use Nette;
use Nette\Application\UI;
use Nette\Localization\ITranslator;
use Sellastica\AdminUI\Component\BaseControl;
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
	/** @var \Sellastica\Entity\EntityManager */
	private $em;
	/** @var int */
	private $filterId;
	/** @var bool */
	private $displayCustomTab;
	/** @var bool */
	private $displayAllResultsTab = true;
	/** @var bool */
	private $allResultsTabDeactivated = false;
	/** @var Tab[] */
	private $customTabs = [];


	/**
	 * @param FilterRuleCollection $filterRules
	 * @param bool $displayCustomTab
	 * @param int $filterId
	 * @param Nette\Security\User $user
	 * @param \Sellastica\Entity\EntityManager $em
	 * @param ITranslator $translator
	 */
	public function __construct(
		FilterRuleCollection $filterRules,
		bool $displayCustomTab,
		int $filterId = null,
		Nette\Security\User $user,
		EntityManager $em,
		ITranslator $translator
	)
	{
		parent::__construct();
		$this->translator = $translator;
		$this->filterRules = $filterRules;
		$this->user = $user;
		$this->em = $em;
		$this->filterId = $filterId;
		$this->displayCustomTab = $displayCustomTab;
	}

	/**
	 * @param int $id
	 */
	public function handleRemove($id)
	{
		if ($savedFilter = $this->em->getRepository(\Sellastica\DataGrid\Entity\AdminFilter::class)->find($id)) {
			$this->em->remove($savedFilter);
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
		$savedFilters = $this->em->getRepository(\Sellastica\DataGrid\Entity\AdminFilter::class)
			->findBy(['presenter' => $this->presenter->getShortName()]);
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
			if (!empty($activeTab)) {
				$activeTab->deactivate();
			}

			$tab->activate();
			$tab->makeSaveable();
			$tabs[] = $tab;
		}

		//custom tabs
		foreach ($this->customTabs as $customTab) {
			$tabs[] = $customTab;
		}

		return $tabs;
	}

	/**
	 * @param string $title
	 * @param string $url
	 * @return Tab
	 */
	public function createTab(string $title, string $url): Tab
	{
		$this->customTabs[] = $tab = new Tab($title, $url);
		return $tab;
	}

	public function hideAllResultsTab(): void
	{
		$this->displayAllResultsTab = false;
	}

	public function deactivateAllResultsTab(): void
	{
		$this->allResultsTabDeactivated = true;
	}

	/**
	 * @param array $params
	 */
	protected function beforeRender(array $params = [])
	{
		$tabs = $this->getTabs();
		if ($this->allResultsTabDeactivated) {
			$tabs[0]->deactivate();
		}

		$this->template->tabs = $tabs;
	}
}
