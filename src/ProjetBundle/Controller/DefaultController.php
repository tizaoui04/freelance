<?php

namespace ProjetBundle\Controller;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Projet;
use AppBundle\Repository\categorieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Projet controller.
 *
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * Finds and displays a projet entity.
     *
     * @Route("/consulterprojet", name="projet_consult")
     * @Method("GET")
     */
    public function showAction()
    {
        $categorie=$this->getDoctrine()->getManager()->getRepository(Categorie::class)->findAll();
        $projets=$this->getDoctrine()->getManager()->getRepository(Projet::class)->findAll();

        return $this->render('@Projet/projet/consulterprog.html.twig', array(
            'projets' => $projets,
            'categorie' => $categorie,
        ));
    }
}
