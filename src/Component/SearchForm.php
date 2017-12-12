<?php
namespace Sellastica\DataGrid\Component;

use Nette;
use Sellastica\AdminUI\Component\BaseControl;
use Sellastica\DataGrid\Entity\IAdminFilterRepository;
use Sellastica\DataGrid\Model\FilterRuleCollection;
use Sellastica\Entity\EntityManager;
use Sellastica\UI\Form\Form;
use Sellastica\UI\Form\FormFactory;

class SearchForm extends BaseControl
{
	/** @var array */
	public $onSuccess = [];
	/** @var array */
	public $onDelete = [];
	/** @var Nette\Security\User */
	private $user;
	/** @var \Sellastica\UI\Form\FormFactory */
	private $formFactory;
	/** @var \Sellastica\Entity\EntityManager */
	private $entityManager;
	/** @var FilterRuleCollection */
	private $filterRules;
	/** @var IAdminFilterRepository */
	private $adminFilterRepository;
	/** @var bool */
	private $displaySearchInput;


	/**
	 * @param \Sellastica\DataGrid\Model\FilterRuleCollection $filterRules
	 * @param bool $displaySearchInput
	 * @param IAdminFilterRepository $adminFilterRepository
	 * @param Nette\Security\User $user
	 * @param \Sellastica\UI\Form\FormFactory $formFactory
	 * @param \Sellastica\Entity\EntityManager $entityManager
	 */
	public function __construct(
		FilterRuleCollection $filterRules,
		bool $displaySearchInput,
		IAdminFilterRepository $adminFilterRepository,
		Nette\Security\User $user,
		FormFactory $formFactory,
		EntityManager $entityManager
	)
	{
		parent::__construct();
		$this->filterRules = $filterRules;
		$this->displaySearchInput = $displaySearchInput;
		$this->user = $user;
		$this->formFactory = $formFactory;
		$this->entityManager = $entityManager;
		$this->adminFilterRepository = $adminFilterRepository;
	}

	/**
	 * Text search form for the listing controllers
	 * @return \Sellastica\UI\Form\Form
	 */
	protected function createComponentSearchForm()
	{
		$form = $this->formFactory->create();
		$form->addText('q')
			->setDefaultValue($this->filterRules['q'])
			->setAttribute('placeholder', $form->getTranslator()->translate('admin.grid.search_form.placeholder'));

		$form->addSubmit('submit', 'Hledat');
		$form->onSuccess[] = [$this, 'processForm'];

		return $form;
	}

	/**
	 * @param \Sellastica\UI\Form\Form $form
	 * @param mixed $values
	 */
	public function processForm(Form $form, $values)
	{
		$params = [
			'q' => $values->q,
			DataGridControl::PARAM_FILTER_ID => null,
			'page' => null,
		];
		foreach ($this->filterRules as $rule) { //if saved search is active and rules are not present in the url
			if (!array_key_exists($rule->getKey(), $params)) {
				$params[$rule->getKey()] = $rule->getValue(); //add param to url
			}
		}

		$this->filterRules->addQuery($values->q);
		$this->onSuccess($params);
	}

	/**
	 * @return bool
	 */
	public function displaySearchInput(): bool
	{
		return $this->displaySearchInput;
	}

	/**
	 * @param array $params
	 */
	protected function beforeRender(array $params = [])
	{
	}
}
