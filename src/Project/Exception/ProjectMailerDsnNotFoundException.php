<?php

declare(strict_types=1);

namespace App\Project\Exception;

use App\Project\Entity\Project;
use RuntimeException;

class ProjectMailerDsnNotFoundException extends RuntimeException
{
    public static function createForProject(Project $project): static
    {
        return new static(
            message: "Project #{$project->getId()} is missing DSN in mailer configuration."
        );
    }
}
