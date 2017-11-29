<?php
namespace Sellastica\DataGrid\Component;

use Nette;
use Nette\Application\UI;
use Sellastica\AdminUI\Component\BaseControl;
use Sellastica\DataGrid\Entity\AdminFilterBuilder;
use Sellastica\DataGrid\Entity\AdminFilterFactory;
use Sellastica\DataGrid\Entity\IAdminFilterRepository;
use Sellastica\DataGrid\Model\FilterRule;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\EntityManager;
use Sellastica\UI\Form\Form;
use Sellastica\UI\Form\FormFactory;
use Sellastica\UI\Presenter\IPresenter;

/**
 * @property-read UI\Presenter|IPresenter $presenter
 */
class SaveSearchForm extends BaseControl
{
	/** @var array */
	public $onSuccess = [];
	/** @var Nette\Security\User */
	private $user;
	/** @var FormFactory */
	private $formFactory;
	/** @var AdminFilterFactory */
	private $adminFilterFactory;
	/** @var \Sellastica\Entity\EntityManager */
	private $entityManager;
	/** @var FilterRuleCollection|FilterRule[] */
	private $filterRules;


	/**
	 * @param FilterRuleCollection $filterRules
	 * @param IAdminFilterRepository $adminFilterRepository
	 * @param \Sellastica\Entity\EntityManager $entityManager
	 * @param Nette\Security\User $user
	 * @param FormFactory $formFactory
	 * @param AdminFilterFactory $adminFilterFactory
	 */
	public function __construct(
		FilterRuleCollection $filterRules,
		IAdminFilterRepository $adminFilterRepository,
		EntityManager $entityManager,
		Nette\Security\User $user,
		FormFactory $formFactory,
		AdminFilterFactory $adminFilterFactory
	)
	{
		parent::__construct();
		$this->user = $user;
		$this->formFactory = $formFactory;
		$this->adminFilterFactory = $adminFilterFactory;
		$this->entityManager = $entityManager;
		$this->filterRules = $filterRules;
	}

	/**
	 * Text search form for the listing controllers
	 * @return Form
	 */
	protected function createComponentSaveSearchForm()
	{
		$form = $this->formFactory->create();
		$form->addText('title')
			->setRequired()
			->setAttribute('placeholder', 'Název hledání');
		$form->addSubmit('submit', 'Uložit');
		$form->onSuccess[] = [$this, 'processForm'];

		return $form;
	}

	/**
	 * @param \Sellastica\UI\Form\Form $form
	 * @param mixed $values
	 */
	public function processForm(Form $form, $values)
	{
		$adminFilter = $this->adminFilterFactory->build(
			AdminFilterBuilder::create(
				$this->user->getId(),
				$values->title,
				$this->presenter->getShortName()
			)
		);

		$redirectArray = [];
		foreach ($this->filterRules as $rule) {
			$redirectArray[$rule->getKey()] = null;
			$parameter = $this->presenter->getParameter($rule->getKey());
			if (isset($parameter) && strlen($parameter)) {
				$adminFilter->addCriterion($rule->getKey(), $parameter);
			}
		}

		$this->entityManager->persist($adminFilter);
		$this->onSuccess($redirectArray + [DataGridControl::PARAM_FILTER_ID => $adminFilter->getId()]);
	}

	/**
	 * @param array $params
	 */
	protected function beforeRender(array $params = [])
	{
	}
}
