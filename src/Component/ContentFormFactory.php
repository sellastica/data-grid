<?php
namespace Sellastica\DataGrid\Component;

class ContentFormFactory
{
	/** @var \Sellastica\UI\Form\FormFactory */
	private $formFactory;


	/**
	 * @param \Sellastica\UI\Form\FormFactory $formFactory
	 */
	public function __construct(\Sellastica\UI\Form\FormFactory $formFactory)
	{
		$this->formFactory = $formFactory;
	}

	/**
	 * @return \Sellastica\UI\Form\Form
	 */
	public function create(): \Sellastica\UI\Form\Form
	{
		$form = $this->formFactory->create();
		$form->getElementPrototype()->setAttribute('id', 'data-grid-content-form');
		$form->addHidden('bulk_action')
			->setHtmlId('data-grid-bulk-action');
		$form->addCheckbox('all_pages');
		$form->addContainer('content');

		return $form;
	}
}
