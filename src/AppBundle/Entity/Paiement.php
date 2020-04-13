<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paiement
 *
 * @ORM\Table(name="paiement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PaiementRepository")
 */
class Paiement
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
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     */
    private $montant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datepaiment", type="date")
     */
    private $datepaiment;

    /**
     * @ORM\OneToOne(targetEntity="Postulation",cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id_postulation", referencedColumnName="id",nullable=true)
     */
    private $postulation;

    /**
     * @return mixed
     */
    public function getPostulation()
    {
        return $this->postulation;
    }

    /**
     * @param mixed $postulation
     */
    public function setPostulation($postulation)
    {
        $this->postulation = $postulation;
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
     * Set montant
     *
     * @param float $montant
     *
     * @return Paiement
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return float
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set datepaiment
     *
     * @param \DateTime $datepaiment
     *
     * @return Paiement
     */
    public function setDatepaiment($datepaiment)
    {
        $this->datepaiment = $datepaiment;

        return $this;
    }

    /**
     * Get datepaiment
     *
     * @return \DateTime
     */
    public function getDatepaiment()
    {
        return $this->datepaiment;
    }
}

