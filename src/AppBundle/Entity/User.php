<?php

namespace AppBundle\Entity;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"F" = "Freelancer", "A" = "Admin","C"="Client","U"="User"})
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
     * @ORM\OneToMany(targetEntity="Message", mappedBy="sender")
     */
     private $senderMessages;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="receiver")
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

    /**
     * @return mixed
     */
    public function getSenderMessages()
    {
        return $this->senderMessages;
    }

    /**
     * @param mixed $senderMessages
     */
    public function setSenderMessages($senderMessages)
    {
        $this->senderMessages = $senderMessages;
    }

    /**
     * @return mixed
     */
    public function getReceiverMessages()
    {
        return $this->receiverMessages;
    }

    /**
     * @param mixed $receiverMessages
     */
    public function setReceiverMessages($receiverMessages)
    {
        $this->receiverMessages = $receiverMessages;
    }

    /**
     * @return mixed
     */
    public function getReclamations()
    {
        return $this->reclamations;
    }

    /**
     * @param mixed $reclamations
     */
    public function setReclamations($reclamations)
    {
        $this->reclamations = $reclamations;
    }

}

