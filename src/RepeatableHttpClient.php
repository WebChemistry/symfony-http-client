<?php declare(strict_types = 1);

namespace WebChemistry\SymfonyHttpClient;

use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;
use WebChemistry\SymfonyHttpClient\Response\RepeatableResponse;

final class RepeatableHttpClient implements HttpClientInterface
{

	private HttpClientInterface $httpClient;

	public function __construct(?HttpClientInterface $httpClient = null)
	{
		$this->httpClient = $httpClient ?? SymfonyHttpClient::create();
	}

	/**
	 * @param mixed[] $options
	 */
	public function request(string $method, string $url, array $options = []): RepeatableResponse
	{
		return new RepeatableResponse($this->httpClient, $method, $url, $options);
	}

	public function stream(iterable|ResponseInterface $responses, float $timeout = null): ResponseStreamInterface
	{
		return $this->stream($responses, $timeout);
	}

	/**
	 * @param mixed[] $options
	 */
	public function withOptions(array $options): static
	{
		$this->httpClient->withOptions($options);

		return new self($this->httpClient);
	}

}
