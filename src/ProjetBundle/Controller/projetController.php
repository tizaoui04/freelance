<?php

namespace ProjetBundle\Controller;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Jardin;
use AppBundle\Entity\Paiement;
use AppBundle\Entity\Projet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Projet controller.
 *
 * @Route("projet")
 */
class projetController extends Controller
{
    /**
     * @Route("/paiement",name="paiement")
     */
    public function paiement(){

        $user=$this->get("security.token_storage")->getToken()->getUser();

        $em=$this->getDoctrine()->getManager()->getRepository(Paiement::class);
        if($this->container->get('security.authorization_checker')->isGranted("ROLE_FREELANCER")){
            $paiment=$em->paimentfreelance($user->getId());
            return $this->render("@Projet/payment/fpaiment.html.twig",array("paiments"=>$paiment));

        }else if ($this->container->get('security.authorization_checker')->isGranted("ROLE_CLIENT")){
            $paiment=$em->paimentclient($user->getId());

            return $this->render("@Projet/payment/cpaiment.html.twig",array("paiments"=>$paiment));

        }
    }

    /**
     * @Route("/search", name="search_project")
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function recherche(Request $request){
        //this action is so important because it containe filter, pagination and sorting using knp paginator
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        // $dql="select a from AppBundle:Remarque a";

        $filter="";
           if( $request->get("filter")){
               $filter= $request->get("filter");
           }
           $categorie=null;
        if($request->get("categorie")){
            $categorie= $request->get("categorie");
        };
        $price=null;
        if( $request->get("price")){
            $price=$request->get("price");
        };
        ;


        $pricemin=10;
        $pricemax=5000;

        if($price){
            $pricemin=explode(",",$price,2)[0];
            $pricemax=explode(",",$price,2)[1];
        }

        $projects = $em->getRepository(Projet::class)->searproject($filter,$categorie,$pricemin,$pricemax);

        $paginator  = $this->get('knp_paginator');


        $rq = $paginator->paginate(
            $projects,
            $request->query->get('page',1) /*page number*/,
            $request->query->get('limit',10) /*limit per page*/
        );


        $categories=$em->getRepository(Categorie::class)->findAll();
        return $this->render('@Projet/projet/consulterprog.html.twig', array(
            'projets' => $rq,
            'categorie'=>$categories,
        ));

    }







    /**
     * Lists all projet entities.
     *
     * @Route("/myprojects", name="projet_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();


        $projets = $em->getRepository(Projet::class)->myproject($user->getId());

        return $this->render('@Projet/projet/index.html.twig', array(
            'projets' => $projets,
        ));
    }

    /**
     * Creates a new projet entity.
     *
     * @Route("/new", name="projet_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $projet = new Projet();
        $form = $this->createForm('ProjetBundle\Form\projetType', $projet);


        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->container->get('security.token_storage')->getToken()->getUser();

            $projet->setClient($user);
            $projet->setDateprojet(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($projet);
            $em->flush();

            return $this->redirectToRoute('projet_show', array('id' => $projet->getId()));
        }

        return $this->render('@Projet/projet/new.html.twig', array(
            'projet' => $projet,
            'form' => $form->createView(),
        ));
    }




    /**
     * Finds and displays a projet entity.
     *
     * @Route("/{id}", name="projet_show")
     * @Method("GET")
     */
    public function showAction(projet $projet)
    {
        $deleteForm = $this->createDeleteForm($projet);

        return $this->render('@Projet/projet/show.html.twig', array(
            'projet' => $projet,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing projet entity.
     *
     * @Route("/{id}/edit", name="projet_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, projet $projet)
    {

        $editForm = $this->createForm('ProjetBundle\Form\projetType', $projet);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('projet_index');
        }

        return $this->render('@Projet/projet/edit.html.twig', array(
            'projet' => $projet,
            'edit_form' => $editForm->createView(),

        ));
    }

    /**
     * Deletes a projet entity.
     *
     * @Route("delete/{id}", name="projet_delete")
     *
     */
    public function deleteAction(Request $request, projet $projet)
    {

            $em = $this->getDoctrine()->getManager();
            $em->remove($projet);
            $em->flush();


        return $this->redirectToRoute('projet_index');
    }

    /**
     * Creates a form to delete a projet entity.
     *
     * @param projet $projet The projet entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(projet $projet)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('projet_delete', array('id' => $projet->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
