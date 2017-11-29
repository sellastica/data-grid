<?php
namespace Sellastica\DataGrid\Model;

use Nette\Utils\Html;
use Sellastica\AdminUI\Button\AbstractButton;

class DataGridColumn
{
	const TYPE_STANDARD = 1,
		TYPE_BUTTON_GROUP = 2;

	/** @var string */
	private $class;
	/** @var array */
	private $content = [];
	/** @var int */
	private $type = self::TYPE_STANDARD;
	/** @var int|null */
	private $colSpan;


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
	public function setType(int $type)
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @param string $text
	 * @return $this
	 */
	public function addText(string $text = null)
	{
		$this->content[] = $text;
		return $this;
	}

	/**
	 * @param Html $html
	 * @return $this
	 */
	public function addHtml(Html $html)
	{
		$this->content[] = (string)$html;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addLineBreak()
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
			->class($class);

		return $link;
	}

	/**
	 * @param string $src
	 * @param string $alt
	 * @return $this
	 */
	public function addImage(string $src, string $alt = null)
	{
		$this->content[] = Html::el('img')
			->src($src)
			->alt($alt);

		return $this;
	}

	/**
	 * @param string $title
	 * @param string $class
	 * @param bool $marginLeft
	 * @return $this
	 */
	public function addLabel(string $title, string $class = 'primary', bool $marginLeft = false)
	{
		$this->content[] = Html::el('span')
			->class("label $class" . ($marginLeft ? ' margin-left-1' : ''))
			->setText($title);

		return $this;
	}

	/**
	 * @return $this
	 */
	public function addSpace()
	{
		$this->content[] = ' ';
		return $this;
	}

	/**
	 * @return $this
	 */
	public function addEmpty()
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
	 * @return $this
	 */
	public function addTextInput(string $name, $value, string $ajaxUrl = null)
	{
		$input = Html::el('input')
			->type('text')
			->class('form-control select-on-focus input-masked')
			->name($name)
			->value($value);

		if ($ajaxUrl) {
			$input->data('ajax-url', $ajaxUrl);
		}

		$this->content[] = $input;
		return $this;
	}

	/**
	 * @param string $name
	 * @param string $value
	 * @return $this
	 */
	public function addHiddenInput(string $name, string $value)
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
	 * @return string
	 */
	public function getClass()
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
	 * @return string
	 */
	public function render()
	{
		if ($this->type === self::TYPE_BUTTON_GROUP) {
			$return = Html::el('div')->class('button-group float-right');
			foreach ($this->content as $item) {
				if ($item instanceof Html) {
					$return->addHtml($item);
				} elseif ($item instanceof AbstractButton) {
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