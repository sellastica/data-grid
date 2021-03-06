<?php
namespace Sellastica\DataGrid\Model;

use Nette\Utils\Html;
use Sellastica\AdminUI\Button\AbstractButton;

class DataGridColumn
{
	const TYPE_STANDARD = 1,
		TYPE_BUTTON_GROUP = 2,
		TYPE_BUTTON_GROUP_SMALL = 3;

	/** @var string|null */
	private $class;
	/** @var array */
	private $content = [];
	/** @var int */
	private $type = self::TYPE_STANDARD;
	/** @var int|null */
	private $colSpan;
	/** @var int|null */
	private $width;


	/**
	 * @param string $class
	 */
	public function __construct($class = null)
	{
		$this->class = $class;
	}

	/**
	 * @param int $type
	 * @return $this
	 */
	public function setType(int $type): DataGridColumn
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @param string $text
	 * @return $this
	 */
	public function addText(string $text = null): DataGridColumn
	{
		$this->content[] = $text;
		return $this;
	}

	/**
	 * @param string $text
	 * @return DataGridColumn
	 */
	public function addGrayText(string $text): DataGridColumn
	{
		$this->content[] = Html::el('span')
			->setAttribute('class', 'dark-gray txt-medium-grey')
			->setHtml($text);
		return $this;
	}

	/**
	 * @param Html $html
	 * @return $this
	 */
	public function addHtml(Html $html): DataGridColumn
	{
		$this->content[] = $html;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addLineBreak(): DataGridColumn
	{
		$this->content[] = Html::el('br');
		return $this;
	}

	/**
	 * @param string $linkText
	 * @param string|null $url
	 * @param string|null $class
	 * @return Html
	 */
	public function addLink(string $linkText, string $url = null, string $class = null): Html
	{
		$this->content[] = $link = Html::el('a')
			->href($url)
			->setText($linkText)
			->setAttribute('class', $class ?? 'underline-hover');

		return $link;
	}

	/**
	 * @param string $src
	 * @param string $alt
	 * @return Html
	 */
	public function addImage(string $src, string $alt = null): Html
	{
		$this->content[] = $img = Html::el('img')
			->setAttribute('src', $src)
			->setAttribute('alt', $alt);

		return $img;
	}

	/**
	 * @param string $title
	 * @param string $class
	 * @param bool $marginLeft
	 * @return $this
	 */
	public function addLabel($title, string $class = 'primary label-primary', bool $marginLeft = false): DataGridColumn
	{
		$this->content[] = $span = Html::el('span')
			->class("label $class" . ($marginLeft ? ' margin-left-1' : ' no-margin-left'));
		if ($title instanceof Html) {
			$span->addHtml($title);
		} else {
			$span->setText($title);
		}


		return $this;
	}

	/**
	 * @return $this
	 */
	public function addSpace(): DataGridColumn
	{
		$this->content[] = ' ';
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addEmpty(): DataGridColumn
	{
		$this->content[] = null;
		return $this;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @param bool $checked
	 * @param string|null $inputClass
	 * @return $this
	 */
	public function addCheckbox(
		?string $name,
		$value,
		bool $checked = false,
		string $inputClass = null
	): DataGridColumn
	{
		$label = Html::el('label', [
			'class' => 'control input-checkbox',
			'for' => $name,
		]);
		$label->addHtml(
			Html::el('input', [
				'type' => 'checkbox',
				'name' => $name,
				'id' => $name,
				'value' => $value,
				'checked' => $checked,
				'class' => $inputClass,
			])
		);
		$label->addHtml(
			Html::el('div')->class('control-indicator')
		);
		$this->content[] = $label;

		return $this;
	}

	/**
	 * @param $value
	 * @return $this
	 */
	public function addBulkCheckbox($value): DataGridColumn
	{
		return $this->addCheckbox('', $value, false, 'bulk-checkbox');
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param string $ajaxUrl
	 * @return Html
	 */
	public function addTextInput(string $name, $value, string $ajaxUrl = null): Html
	{
		$input = Html::el('input')
			->setAttribute('type', 'text')
			->setAttribute('class', 'form-control select-on-focus show-on-hover')
			->setAttribute('name', $name)
			->setAttribute('value', $value);

		if ($ajaxUrl) {
			$input->data('ajax-url', $ajaxUrl);
		}

		$this->content[] = $input;
		return $input;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param string $ajaxUrl
	 * @return Html of the INPUT field
	 */
	public function addTextInputWithSpinner(
		string $name,
		$value,
		string $ajaxUrl = null
	): Html
	{
		$wrapper = \Nette\Utils\Html::el('div')
			->setAttribute('class', 'inner-addon right-inner-addon');
		$i = \Nette\Utils\Html::el('i')
			->setAttribute('class', 'fa fa-spinner fa-spin');
		$input = \Nette\Utils\Html::el('input')
			->setAttribute('type', 'text')
			->setAttribute('class', 'form-control select-on-focus show-on-hover')
			->setAttribute('name', $name)
			->setAttribute('value', $value)
			->setAttribute('autocomplete', 'off');
		if ($ajaxUrl) {
			$input->data('ajax-url', $ajaxUrl);
		}

		$this->content[] = $wrapper->addHtml($i)->addHtml($input);

		return $input;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @param string $ajaxUrl
	 * @return Html of the INPUT field
	 */
	public function addRightAlignedTextInputWithSpinner(
		string $name,
		$value,
		string $ajaxUrl = null
	): Html
	{
		$wrapper = \Nette\Utils\Html::el('div')
			->setAttribute('class', 'inner-addon left-inner-addon');
		$i = \Nette\Utils\Html::el('i')
			->setAttribute('class', 'fa fa-spinner fa-spin');
		$input = \Nette\Utils\Html::el('input')
			->setAttribute('type', 'text')
			->setAttribute('class', 'form-control select-on-focus show-on-hover text-right')
			->setAttribute('name', $name)
			->setAttribute('value', $value)
			->setAttribute('autocomplete', 'off')
			->data('original-value', $value);
		if ($ajaxUrl) {
			$input->data('ajax-url', $ajaxUrl);
		}

		$this->content[] = $wrapper->addHtml($i)->addHtml($input);

		return $input;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function addHiddenInput(string $name, string $value): DataGridColumn
	{
		$this->content[] = Html::el('input')
			->type('hidden')
			->name($name)
			->value($value);

		return $this;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @param bool $checked
	 * @param string $class
	 * @return DataGridColumn
	 */
	public function addSwitch(string $name, $value, bool $checked = false, string $class = null)
	{
		$class = $class ? $class . ' ' : '';
		$class .= 'iswitch iswitch-secondary';

		$this->content[] = Html::el('input')
			->type('checkbox')
			->class($class)
			->name($name)
			->value(is_string($value) ? (string)$value : (int)$value)
			->checked((bool)$checked);

		return $this;
	}

	/**
	 * @param \Sellastica\AdminUI\Button\AbstractButton $button
	 */
	public function addButton(AbstractButton $button)
	{
		$this->content[] = $button;
	}

	/**
	 * @return array
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return string|null
	 */
	public function getClass(): ?string
	{
		return $this->class;
	}

	/**
	 * @return int|null
	 */
	public function getColSpan(): ?int
	{
		return $this->colSpan;
	}

	/**
	 * @param int|null $colSpan
	 */
	public function setColSpan(?int $colSpan)
	{
		$this->colSpan = $colSpan;
	}

	/**
	 * @return int|null
	 */
	public function getWidth(): ?int
	{
		return $this->width;
	}

	/**
	 * @param int|null $width
	 * @return DataGridColumn
	 */
	public function setWidth(?int $width): DataGridColumn
	{
		$this->width = $width;
		return $this;
	}

	/**
	 * @return string
	 */
	public function render(): string
	{
		if ($this->type === self::TYPE_BUTTON_GROUP
		|| $this->type === self::TYPE_BUTTON_GROUP_SMALL) {
			$class = $this->type === self::TYPE_BUTTON_GROUP
				? 'button-group btn-group'
				: 'button-group small btn-group';
			$return = Html::el('div')->class($class);
			foreach ($this->content as $item) {
				if ($item instanceof Html) {
					$return->addHtml($item);
				} elseif (method_exists($item, 'toHtml')) {
					$return->addHtml($item->toHtml());
				} else {
					$return->addText($item);
				}
			}
		} else {
			$return = '';
			foreach ($this->content as $item) {
				$return .= $item;
			}
		}

		return (string)$return;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}
}