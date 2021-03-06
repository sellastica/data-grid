<?php
namespace Sellastica\DataGrid\Model;

class Tab
{
	/** @var int */
	private $id;
	/** @var string|\Nette\Utils\Html */
	private $title;
	/** @var string|\Nette\Utils\Html|null */
	private $subtitle;
	/** @var string|null */
	private $url;
	/** @var bool */
	private $active = false;
	/** @var bool */
	private $deletable = false;
	/** @var bool */
	private $saveable = false;


	/**
	 * @param string|\Nette\Utils\Html $title
	 * @param string $url
	 */
	public function __construct($title, string $url = null)
	{
		$this->title = $title;
		$this->url = $url;
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id)
	{
		$this->id = $id;
	}

	/**
	 * @return string|\Nette\Utils\Html
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @return \Nette\Utils\Html|string|null
	 */
	public function getSubtitle()
	{
		return $this->subtitle;
	}

	/**
	 * @param \Nette\Utils\Html|string|null $subtitle
	 */
	public function setSubtitle($subtitle): void
	{
		$this->subtitle = $subtitle;
	}

	/**
	 * @return string|null
	 */
	public function getUrl(): ?string
	{
		return $this->url;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active): void
	{
		$this->active = $active;
	}

	public function activate(): void
	{
		$this->active = true;
	}

	public function deactivate(): void
	{
		$this->active = false;
	}

	/**
	 * @return bool
	 */
	public function isSaveable(): bool
	{
		return $this->saveable;
	}

	public function makeSaveable(): void
	{
		$this->saveable = true;
	}

	/**
	 * @return bool
	 */
	public function isDeletable(): bool
	{
		return $this->deletable;
	}

	public function makeDeletable(): void
	{
		$this->deletable = true;
	}
}