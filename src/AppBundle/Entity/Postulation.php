<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Postulation
 *
 * @ORM\Table(name="postulation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostulationRepository")
 */
class Postulation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="lettre", type="string", length=255)
     */
    private $lettre;

    /**
     * @var string
     *
     * @ORM\Column(name="accepte", type="string", length=255)
     */
    private $accepte;
    /**
     * @ORM\ManyToOne(targetEntity="Projet", inversedBy="postulations")
     * @ORM\JoinColumn(name="idproj", referencedColumnName="id")
     */
    private $project;


    /**
     * @ORM\ManyToOne(targetEntity="Freelancer", inversedBy="postulations")
     * @ORM\JoinColumn(name="idfre", referencedColumnName="id")
     */
    private $freelance;

    /**
     * @ORM\OneToOne(targetEntity="Paiement", inversedBy="postulation" , fetch="EAGER")

     */
    private $paiement;

    /**
     * @return mixed
     */
    public function getPaiement()
    {
        return $this->paiement;
    }

    /**
     * @param mixed $paiement
     */
    public function setPaiement($paiement)
    {
        $this->paiement = $paiement;
    }




    /**
     * @return mixed
     */
    public function getFreelance()
    {
        return $this->freelance;
    }

    /**
     * @param mixed $freelance
     */
    public function setFreelance($freelance)
    {
        $this->freelance = $freelance;
    }


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
     * Set lettre
     *
     * @param string $lettre
     *
     * @return Postulation
     */
    public function setLettre($lettre)
    {
        $this->lettre = $lettre;

        return $this;
    }


    /**
     * Get lettre
     *
     * @return string
     */
    public function getLettre()
    {
        return $this->lettre;
    }

    /**
     * Set accepte
     *
     * @param string $accepte
     *
     * @return Postulation
     */
    public function setAccepte($accepte)
    {
        $this->accepte = $accepte;

        return $this;
    }

    /**
     * Get accepte
     *
     * @return string
     */
    public function getAccepte()
    {
        return $this->accepte;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

}

