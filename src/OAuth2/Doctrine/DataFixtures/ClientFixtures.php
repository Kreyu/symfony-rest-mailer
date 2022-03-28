<?php

declare(strict_types=1);

namespace App\OAuth2\Doctrine\DataFixtures;

use App\OAuth2\Entity\Client;
use App\Project\Doctrine\DataFixtures\ProjectFixtures;
use App\Project\Entity\Project;
use App\Shared\Doctrine\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use League\Bundle\OAuth2ServerBundle\Event\PreSaveClientEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ClientFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception due to 'random_bytes' function
     */
    public function load(ObjectManager $manager): void
    {
        $projects = $manager->getRepository(Project::class)->findAll();

        foreach ($projects as $project) {
            $client = new Client(
                name: $project->getName(),
                identifier: hash('md5', random_bytes(16)),
                secret: hash('sha512', random_bytes(32)),
            );

            $client->addProject($project);

            $this->eventDispatcher->dispatch(new PreSaveClientEvent($client), OAuth2Events::PRE_SAVE_CLIENT);

            $manager->persist($client);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
        ];
    }
}
