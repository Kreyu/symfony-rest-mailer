<?php

declare(strict_types=1);

namespace App\Project\Doctrine\DataFixtures;

use App\Project\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $letters = ['A', 'B', 'C'];

        for ($i = 0; $i < 3; $i++) {
            $project = new Project();
            $project->setName('Project ' . $letters[$i]);
            $project->getMailerConfiguration()->setDsn('smtp://mailhog:1025');

            $manager->persist($project);
            $manager->flush();
        }
    }
}
