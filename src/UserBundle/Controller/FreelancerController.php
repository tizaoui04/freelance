<?php

namespace UserBundle\Controller;

use AppBundle\Entity\Categorie;
use AppBundle\Entity\Freelancer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Form\FreelancerType;

/**
 * Freelancer controller.
 *
 * @Route("freelancer")
 */
class FreelancerController extends Controller
{


    /**
     * @Route("/update/{id}",name="update_profile", requirements={"id":"\d+"})
     * @Method({"GET","POST"})
     */
    public function updateprofile(Request $request, Freelancer $freelancer)
    {
        $editForm = $this->createForm(FreelancerType::class, $freelancer);
        $editForm->remove("date")->remove("plainPassword")->remove("username");
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {


            //get the passwords old one and the new one with its confirmation
            $oldpass = $request->get("oldpass");
            $newpass = $request->get("newpass");
            $sedondpass = $request->get("newpass1");

            if ($oldpass != null && $newpass != null && $sedondpass != null) {


                //get the encoder which encode the password to verify the validity of the old pass
                $encoder = $this->get('security.encoder_factory')->getEncoder($freelancer);
                $salt = $freelancer->getSalt();

                //verify if the neww passwords match if not return the page
                if (strcmp($newpass, $sedondpass) != 0) {
                    return $this->render('@User/freelancer/profile.html.twig', array(
                        'freelancer' => $freelancer,
                        'edit_form' => $editForm->createView(),
                        "msg" => "les mots de passe ne correspondent pas",
                    ));
                }
                //check if the old pass match the given one
                if (!$encoder->isPasswordValid($freelancer->getPassword(), $oldpass, $salt)) {
                    return $this->render('@User/freelancer/profile.html.twig', array(
                        'freelancer' => $freelancer,
                        'edit_form' => $editForm->createView(),
                        "msg" => "Verifiez mot de passe actuel",
                    ));
                } else {
                    $freelancer->setPlainPassword($newpass);
                }


            }


            if ($editForm->isValid()) {


                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('update_profile', array('id' => $freelancer->getId()));
            }
        }


        return $this->render('@User/freelancer/profile.html.twig', array(
            'freelancer' => $freelancer,
            'edit_form' => $editForm->createView(),
        ));

    }


    /**
     * Lists all freelancer entities.
     *
     * @Route("/", name="freelancer_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $freelancers = $em->getRepository(Freelancer::class)->findAll();

        $paginator  = $this->get('knp_paginator');

        $categorie=null;
        if($request->get("categorie")){
            $categorie= $request->get("categorie");
        };
        $rq = $paginator->paginate(
            $freelancers,
            $request->query->get('page',1) /*page number*/,
            $request->query->get('limit',5) /*limit per page*/
        );


        $categories=$em->getRepository(Categorie::class)->findAll();
        return $this->render('@User/freelancer/index.html.twig', array(
            'freelancers' => $rq,
            'categories'=>$categories,
        ));
    }

    /**
     * Creates a new freelancer entity.
     *
     * @Route("/new", name="freelancer_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $freelancer = new Freelancer();
        $form = $this->createForm(FreelancerType::class, $freelancer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $freelancer->addRole("ROLE_FREELANCER");

            $em->persist($freelancer);
            $em->flush();

            return $this->redirectToRoute('freelancer_show', array('id' => $freelancer->getId()));
        }

        return $this->render('@User/freelancer/new.html.twig', array(
            'freelancer' => $freelancer,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a freelancer entity.
     *
     * @Route("/{id}", name="freelancer_show")
     * @Method("GET")
     */
    public function showAction(Freelancer $freelancer)
    {
        $deleteForm = $this->createDeleteForm($freelancer);

        return $this->render('@User/freelancer/show.html.twig', array(
            'freelancer' => $freelancer,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing freelancer entity.
     *
     * @Route("/{id}/edit", name="freelancer_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Freelancer $freelancer)
    {
        $deleteForm = $this->createDeleteForm($freelancer);
        $editForm = $this->createForm(FreelancerType::class, $freelancer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('freelancer_show', array('id' => $freelancer->getId()));
        }

        return $this->render('@User/freelancer/edit.html.twig', array(
            'freelancer' => $freelancer,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a freelancer entity.
     *
     * @Route("/{id}", name="freelancer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Freelancer $freelancer)
    {
        $form = $this->createDeleteForm($freelancer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($freelancer);
            $em->flush();
        }

        return $this->redirectToRoute('freelancer_index');
    }

    /**
     * Creates a form to delete a freelancer entity.
     *
     * @param Freelancer $freelancer The freelancer entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Freelancer $freelancer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('freelancer_delete', array('id' => $freelancer->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
