<?php

namespace AppBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="message", mappedBy="sender")
     */
     private $senderMessages;

    /**
     * @ORM\OneToMany(targetEntity="message", mappedBy="receiver")
     */
    private $receiverMessages;

    /**
     * @ORM\OneToMany(targetEntity="Reclamation", mappedBy="sender")
     */
    private $reclamations;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

