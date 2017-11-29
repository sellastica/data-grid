<?php
namespace Sellastica\DataGrid\Model;

class Tag
{
	/** @var string */
	private $title;
	/** @var string */
	private $value;
	/** @var string */
	private $displayValue;
	/** @var string */
	private $url;

	/**
	 * @param string $title
	 * @param string $value
	 * @param string $displayValue
	 * @param string $url
	 */
	public function __construct(
		string $title,
		string $value,
		string $displayValue,
		string $url
	)
	{
		$this->title = $title;
		$this->value = $value;
		$this->displayValue = $displayValue;
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @return string
	 */
	public function getDisplayValue()
	{
		return $this->displayValue;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @param FilterRule $criterion
	 * @param string $url
	 * @return Tag
	 */
	public static function fromSearchCriterion(FilterRule $criterion, string $url): self
	{
		return new self(
			(string)$criterion->getTitle(),
			$criterion->getValue(),
			$criterion->display(),
			$url
		);
	}
}