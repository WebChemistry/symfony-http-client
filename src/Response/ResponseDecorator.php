<?php declare(strict_types = 1);

namespace WebChemistry\SymfonyHttpClient\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class ResponseDecorator implements ResponseInterface
{

	public function __construct(
		protected ResponseInterface $decorate,
	)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function getStatusCode(): int
	{
		return $this->decorate->getStatusCode();
	}

	/**
	 * @inheritDoc
	 */
	public function getHeaders(bool $throw = true): array
	{
		return $this->decorate->getHeaders($throw);
	}

	/**
	 * @inheritDoc
	 */
	public function getContent(bool $throw = true): string
	{
		return $this->decorate->getContent($throw);
	}

	/**
	 * @inheritDoc
	 */
	public function toArray(bool $throw = true): array
	{
		return $this->decorate->toArray($throw);
	}

	/**
	 * @inheritDoc
	 */
	public function cancel(): void
	{
		$this->decorate->cancel();
	}

	/**
	 * @inheritDoc
	 */
	public function getInfo(string $type = null): mixed
	{
		return $this->decorate->getInfo($type);
	}

}
