<?php
namespace Sellastica\DataGrid\Component;

/**
 * @property-read \Sellastica\UI\Presenter\IPresenter $presenter
 */
class SaveSearchForm extends \Sellastica\AdminUI\Component\BaseControl
{
	/** @var array */
	public $onSuccess = [];
	/** @var \Nette\Security\User */
	private $user;
	/** @var \Sellastica\UI\Form\FormFactory */
	private $formFactory;
	/** @var \Sellastica\DataGrid\Entity\AdminFilterFactory */
	private $adminFilterFactory;
	/** @var \Sellastica\Entity\EntityManager */
	private $entityManager;
	/** @var \Sellastica\DataGrid\Model\FilterRuleCollection|\Sellastica\DataGrid\Model\FilterRule[] */
	private $filterRules;


	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $filterRules
	 * @param \Sellastica\Entity\EntityManager $entityManager
	 * @param \Nette\Security\User $user
	 * @param \Sellastica\UI\Form\FormFactory $formFactory
	 * @param \Sellastica\DataGrid\Entity\AdminFilterFactory $adminFilterFactory
	 */
	public function __construct(
		\Sellastica\DataGrid\Model\FilterRuleCollection $filterRules,
		\Sellastica\Entity\EntityManager $entityManager,
		\Nette\Security\User $user,
		\Sellastica\UI\Form\FormFactory $formFactory,
		\Sellastica\DataGrid\Entity\AdminFilterFactory $adminFilterFactory
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
	 * @return \Sellastica\UI\Form\Form
	 */
	protected function createComponentSaveSearchForm(): \Sellastica\UI\Form\Form
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
	public function processForm(\Sellastica\UI\Form\Form $form, $values): void
	{
		$adminFilter = $this->adminFilterFactory->build(
			\Sellastica\DataGrid\Entity\AdminFilterBuilder::create(
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
	protected function beforeRender(array $params = []): void
	{
	}
}
