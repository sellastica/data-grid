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

	/** @var int */
	private $adminUserId;
	/** @var string */
	private $title;
	/** @var string */
	private $presenter;

	/**
	 * @param int $adminUserId
	 * @param string $title
	 * @param string $presenter
	 */
	public function __construct(
		int $adminUserId,
		string $title,
		string $presenter
	)
	{
		$this->adminUserId = $adminUserId;
		$this->title = $title;
		$this->presenter = $presenter;
	}

	/**
	 * @return int
	 */
	public function getAdminUserId(): int
	{
		return $this->adminUserId;
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
	 * @param int $adminUserId
	 * @param string $title
	 * @param string $presenter
	 * @return self
	 */
	public static function create(
		int $adminUserId,
		string $title,
		string $presenter
	): self
	{
		return new self($adminUserId, $title, $presenter);
	}
}