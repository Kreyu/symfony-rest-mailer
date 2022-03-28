<?php

declare(strict_types=1);

namespace App\Mailer\Controller\Api;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Mailer\Entity\Dto\MessageDto;
use App\Mailer\Entity\Factory\MessageFactory;
use App\Mailer\Messenger\SendMessage\SendMessage;
use App\OAuth2\Entity\Client;
use App\Project\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\ScopeManagerInterface;
use League\Bundle\OAuth2ServerBundle\Security\Authentication\Token\OAuth2Token;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class CreateMessageController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private MessageFactory $messageFactory,
    ) {
        // ...
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(Request $request, Security $security): JsonResponse
    {
        /** @var null|OAuth2Token $token */
        $token = $security->getToken();

        if (!$token instanceof OAuth2Token) {
            throw new UnauthorizedHttpException('Bearer', 'Missing token');
        }

        $client = $this->entityManager
            ->getRepository(Client::class)
            ->find($token->getOAuthClientId());

        if (null === $client) {
            throw new UnauthorizedHttpException('Bearer', 'Client of given token does not exist');
        }

        $projectUuid = $request->headers->get('X-Project-UUID');

        if (null === $projectUuid || !Uuid::isValid($projectUuid)) {
            throw new UnauthorizedHttpException('Bearer', 'Project of given UUID does not exist');
        }

        $project = $this->entityManager
            ->getRepository(Project::class)
            ->findOneByUuid(Uuid::fromString($projectUuid));

        if (null === $project || !$client->getProjects()->contains($project)) {
            throw new UnauthorizedHttpException('Bearer', 'Client has no access to the project');
        }

        /** @var MessageDto $messageDto */
        $messageDto = $this->serializer->deserialize(
            data: $request->getContent(),
            type: MessageDto::class,
            format: 'json',
        );

        $violations = $this->validator->validate($messageDto);

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }

        $message = $this->messageFactory->createFromDto($messageDto);
        $message->setProject($project);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        $this->messageBus->dispatch(new SendMessage($message->getUuid()));

        return new JsonResponse(
            data: $this->serializer->serialize(
                data: $message,
                format: 'json',
                context: [
                    'groups' => ['read.item'],
                ],
            ),
            json: true,
        );
    }
}
