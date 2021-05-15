<?php

declare(strict_types=1);

namespace App\Client;

use ElevenLabs\Api\Schema;
use ElevenLabs\Api\Service\ApiService;
use ElevenLabs\Api\Service\Exception\RequestViolations;
use ElevenLabs\Api\Service\Resource\ErrorInterface;
use ElevenLabs\Api\Service\Resource\ResourceInterface;
use Http\Promise\Promise;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ApiClient.
 */
class ApiClient implements ApiClientInterface
{
    /**
     * @var ApiService
     */
    private $client;

    public function __construct(ApiService $client)
    {
        $this->client = $client;
    }

    public function getItem(string $operationId, array $params = []): array
    {
        return $this->call($operationId, $params)->getData();
    }

    public function collection(string $operationId, array $params = [])
    {
        return $this->call($operationId, $params);
    }

    public function write(string $operationId, array $params = [])
    {
        try {
            $response = $this->call($operationId, $params);

            if ($response instanceof ResourceInterface) {
                return $response;
            }
        } catch (RequestViolations $e) {
            $errors = [];
            foreach ($e->getViolations() as $violation) {
                $errors[$violation->getProperty()] = $violation->getMessage();
            }

            return $errors;
        } catch (HttpException $e) {
            return ['form.errors.http'];
        }

        return ['form.errors'];
    }

    public function delete(string $operationId, array $params = [])
    {
        try {
            $this->call($operationId, $params);
        } catch (\Exception $e) {
        }
    }

    public function callAsync(string $operationId, array $params = []): Promise
    {
        return $this->client->callAsync($operationId, $params);
    }

    public function getSchema(): Schema
    {
        return $this->client->getSchema();
    }

    private function call(string $operationId, array $params)
    {
        $response = $this->client->call($operationId, $params);

        if ($response instanceof ErrorInterface) {
            throw new HttpException($response->getCode(), $response->getMessage());
        }

        return $response;
    }
}
