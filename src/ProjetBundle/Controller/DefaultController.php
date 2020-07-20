<?php

namespace ProjetBundle\Controller;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Paiement;
use AppBundle\Entity\Postulation;
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


    /**
     * Finds and displays a projet entity.
     *
     * @Route("/pay/{id}", name="payment_project")
     * @Method("GET")
     */
    public function paymentAction(Postulation $postulation)
    {
        return $this->render('@Projet/payment/payment.html.twig',['postulation' => $postulation]);

    }


    /**
     * Finds and displays a projet entity.
     *
     * @Route("/payment/{id}/{montan}", name="payment_confirm")
     * @Method("GET")
     */
    public function paymentConfirmAction(Postulation $postulation,$montan)
    {
        $em = $this->getDoctrine()->getManager();
        $pay = new Paiement();

        $pay->setMontant($montan);
        $pay->setPostulation($postulation);

        $em->persist($pay);
        $em->flush();

        return $this->redirectToRoute('postulation_index', ['id' => $postulation->getProject()->getId()]);

    }
}
