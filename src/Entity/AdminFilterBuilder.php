<?php
namespace Sellastica\DataGrid\Entity;

use Sellastica\Entity\IBuilder;
use Sellastica\Entity\TBuilder;

/**
 * @see AdminFilter
 */
class AdminFilterBuilder implements IBuilder
{
	use TBuilder;

	/** @var string */
	private $title;
	/** @var string */
	private $presenter;
	/** @var bool */
	private $generic = false;

	/**
	 * @param string $title
	 * @param string $presenter
	 */
	public function __construct(
		string $title,
		string $presenter
	)
	{
		$this->title = $title;
		$this->presenter = $presenter;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getPresenter(): string
	{
		return $this->presenter;
	}

	/**
	 * @return bool
	 */
	public function getGeneric(): bool
	{
		return $this->generic;
	}

	/**
	 * @param bool $generic
	 * @return $this
	 */
	public function generic(bool $generic)
	{
		$this->generic = $generic;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function generateId(): bool
	{
		return !AdminFilter::isIdGeneratedByStorage();
	}

	/**
	 * @return AdminFilter
	 */
	public function build(): AdminFilter
	{
		return new AdminFilter($this);
	}

	/**
	 * @param string $title
	 * @param string $presenter
	 * @return self
	 */
	public static function create(
		string $title,
		string $presenter
	): self
	{
		return new self($title, $presenter);
	}
}