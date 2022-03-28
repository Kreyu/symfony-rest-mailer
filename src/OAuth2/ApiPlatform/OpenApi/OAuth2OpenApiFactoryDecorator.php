<?php

declare(strict_types=1);

namespace App\OAuth2\ApiPlatform\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model;
use ApiPlatform\Core\OpenApi\OpenApi;
use ArrayObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * This OpenApi decorator adds following schemas:
 *
 * - #/components/schemas/OAuth2Token
 * - #/components/schemas/OAuth2LoginCredentials
 * - #/components/schemas/OAuth2RefreshCredentials
 *
 * and following paths:
 *
 * - GET /api/oauth2/login
 * - GET /api/oauth2/refresh
 */
class OAuth2OpenApiFactoryDecorator implements OpenApiFactoryInterface
{
    private OpenApiFactoryInterface $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $this->addSchemas($openApi);
        $this->addPaths($openApi);

        return $openApi;
    }

    private function addSchemas(OpenApi $openApi): void
    {
        /** @var ArrayObject $schemas */
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas->offsetSet('OAuth2Token', [
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas->offsetSet('OAuth2LoginCredentials', [
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'user@example.com',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '123',
                ],
            ],
        ]);

        $schemas->offsetSet('OAuth2RefreshCredentials', [
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                ],
            ],
        ]);
    }

    private function addPaths(OpenApi $openApi): void
    {
        $paths = $openApi->getPaths();

        $paths->addPath(
            '/api/oauth2/login',
            (new Model\PathItem())
                ->withRef('#/components/schemas/OAuth2LoginCredentials')
                ->withPost(
                    (new Model\Operation())
                        ->withOperationId('login')
                        ->withSummary('Retrieves an OAuth2Token resource using the OAuth2LoginCredentials.')
                        ->withTags(['OAuth2'])
                        ->withRequestBody(
                            (new Model\RequestBody())
                                ->withContent(new ArrayObject([
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/OAuth2LoginCredentials',
                                        ],
                                    ],
                                ]))
                        )
                        ->withResponses([
                            Response::HTTP_OK => (new Model\Response())
                                ->withDescription('Successfully retrieved OAuth token.')
                                ->withContent(new ArrayObject([
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/OAuth2Token',
                                        ],
                                    ],
                                ])),
                        ])
                )
        );

        $paths->addPath(
            '/api/oauth2/refresh',
            (new Model\PathItem())
                ->withRef('#/components/schemas/OAuth2RefreshCredentials')
                ->withPost(
                    (new Model\Operation())
                        ->withOperationId('refresh')
                        ->withSummary('Retrieves an OAuth2Token resource using the OAuth2RefreshCredentials.')
                        ->withTags(['OAuth2'])
                        ->withRequestBody(
                            (new Model\RequestBody())
                                ->withContent(new ArrayObject([
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/OAuth2RefreshCredentials',
                                        ],
                                    ],
                                ]))
                        )
                        ->withResponses([
                            Response::HTTP_OK => (new Model\Response())
                                ->withDescription('Successfully retrieved OAuth token.')
                                ->withContent(new ArrayObject([
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/OAuth2Token',
                                        ],
                                    ],
                                ])),
                        ])
                )
        );
    }
}
