<?php declare(strict_types = 1);

namespace WebChemistry\SymfonyHttpClient\Response\Rule;

use Symfony\Contracts\HttpClient\ResponseInterface;

final class RepeatRule
{

	/** @var callable(ResponseInterface): bool */
	private $condition;

	/**
	 * @param callable(ResponseInterface): bool $condition
	 */
	public function __construct(
		callable $condition,
		public int $times,
		public ?int $waitTime = null,
	)
	{
		$this->condition = $condition;
	}

	public function repeat(ResponseInterface $response): bool
	{
		return ($this->condition)($response);
	}

}
