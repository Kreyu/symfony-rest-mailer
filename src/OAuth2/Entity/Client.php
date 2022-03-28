<?php /** @noinspection PhpMissingFieldTypeInspection */

declare(strict_types=1);

namespace App\OAuth2\Entity;

use App\Project\Entity\Project;
use App\Shared\Entity\EntityInterface;
use App\Shared\Entity\TimestampableEntityInterface;
use App\Shared\Entity\TimestampableEntityTrait;
use App\Shared\Entity\UuidEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use League\Bundle\OAuth2ServerBundle\Model\AbstractClient;

#[ORM\Entity]
#[ORM\Table(name: "oauth2_client")]
class Client extends AbstractClient implements EntityInterface, TimestampableEntityInterface
{
    use UuidEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: "string", length: 80, unique: true)]
    protected /* string */ $identifier;

    #[ORM\ManyToMany(targetEntity: Project::class)]
    #[ORM\JoinTable(name: "oauth2_client_project")]
    #[ORM\JoinColumn(name: "oauth2_client_id", referencedColumnName: "identifier")]
    #[ORM\InverseJoinColumn(name: "project_id", referencedColumnName: "id")]
    private Collection $projects;

    public function __construct(string $name, string $identifier, ?string $secret)
    {
        parent::__construct($name, $identifier, $secret);

        $this->projects = new ArrayCollection();

        $this->initializeUuid();
        $this->initializeTimestamps();
    }

    public function __toString(): string
    {
        return sprintf('%s [%s]', $this->getName(), $this->getIdentifier());
    }

    public function getId(): null|int|string
    {
        return $this->identifier;
    }

    /**
     * @return Collection<Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): void
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
        }
    }

    public function removeProject(Project $project): void
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
        }
    }
}
