<?php

namespace App\Core\User\Domain;

use App\Common\EventManager\EventsCollectorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    use EventsCollectorTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true}, nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=300, nullable=false)
     */
    private string $email;

    public function __construct(string $email)
    {
        $this->id = null;
        $this->email = $email;
        $this->is_active = null;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
    * @ORM\Column(type="boolean", options={"default": false})
    */
    private $is_active;

    public function getIsActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->is_active = $isActive;

        return $this;
    }
}
