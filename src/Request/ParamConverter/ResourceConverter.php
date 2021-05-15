<?php

declare(strict_types=1);

namespace App\Request\ParamConverter;

use ElevenLabs\Api\Service\ApiService;
use ElevenLabs\Api\Service\Resource\ErrorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ResourceConverter.
 */
class ResourceConverter implements ParamConverterInterface
{
    /**
     * @var ApiService
     */
    private $client;

    public function __construct(ApiService $client)
    {
        $this->client = $client;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $resourceName = $request->get('resourceName');
        $resource = ucfirst($resourceName);
        $response = $this->client->call(sprintf('get%sItem', $resource), ['id' => $request->get('resource')]);

        if ($response instanceof ErrorInterface) {
            throw new NotFoundHttpException('The item does not exist!', null, 404);
        }

        $request->attributes->set($configuration->getName(), $response->getData());
    }

    public function supports(ParamConverter $configuration): bool
    {
        return false;
    }
}
