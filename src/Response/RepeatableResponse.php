<?php declare(strict_types = 1);

namespace WebChemistry\SymfonyHttpClient\Response;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use WebChemistry\SymfonyHttpClient\Response\Rule\RepeatRule;

final class RepeatableResponse implements ResponseInterface
{

	private ResponseInterface $response;

	/** @var RepeatRule[] */
	private array $repeatRules = [];

	/**
	 * @param mixed[] $options
	 */
	public function __construct(
		private HttpClientInterface $httpClient,
		private string $method,
		private string $url,
		private array $options = [],
	)
	{
		$this->retryRequest();
	}

	/**
	 * @param callable(ResponseInterface): bool $condition
	 * @param int|null $waitTime time in milliseconds (ms)
	 */
	public function repeatIf(callable $condition, int $times, ?int $waitTime = null): self
	{
		$this->repeatRules[] = new RepeatRule($condition, $times, $waitTime);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getStatusCode(): int
	{
		$this->tryRetry();

		return $this->response->getStatusCode();
	}

	/**
	 * @inheritDoc
	 */
	public function getHeaders(bool $throw = true): array
	{
		$this->tryRetry();

		return $this->response->getHeaders($throw);
	}

	/**
	 * @inheritDoc
	 */
	public function getContent(bool $throw = true): string
	{
		$this->tryRetry();

		return $this->response->getContent($throw);
	}

	/**
	 * @inheritDoc
	 * @return mixed[]
	 */
	public function toArray(bool $throw = true): array
	{
		$this->tryRetry();

		return $this->response->toArray($throw);
	}

	/**
	 * @inheritDoc
	 */
	public function cancel(): void
	{
		$this->response->cancel();
	}

	/**
	 * @inheritDoc
	 */
	public function getInfo(?string $type = null): mixed
	{
		$this->tryRetry();

		return $this->response->getInfo($type);
	}

	private function retryRequest(): void
	{
		$this->response = $this->httpClient->request($this->method, $this->url, $this->options);
	}

	private function tryRetry(): void
	{
		foreach ($this->repeatRules as $rule) {
			for (; $rule->times > 0; $rule->times--) {
				if (!$rule->repeat($this->response)) {
					break;
				}


				if ($rule->waitTime !== null) {
					usleep($rule->waitTime * 1000);
				}

				$this->retryRequest();
			}
		}
	}

}
