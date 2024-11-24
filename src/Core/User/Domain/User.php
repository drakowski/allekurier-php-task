<?php

namespace App\Core\User\Domain;

use App\Common\EventManager\EventsCollectorTrait;
use App\Core\User\Domain\Status\UserStatus;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 *
 * @ORM\Table(name="users")
 */
class User
{
    use EventsCollectorTrait;

    /**
     * @ORM\Id
     *
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=300, nullable=false)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=16, nullable=false, enumType="\App\Core\User\Domain\Status\UserStatus")
     */
    private UserStatus $status;

    public function __construct(string $email)
    {
        $this->id = null;
        $this->email = $email;
        $this->status = UserStatus::INACTIVE;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function hasActiveStatus(): bool
    {
        return $this->hasStatus(UserStatus::ACTIVE);
    }

    public function hasStatus(UserStatus $status): bool
    {
        return $this->status === $status;
    }
}
