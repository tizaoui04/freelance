<?php

namespace ProjetBundle\Controller;

use AppBundle\Entity\Postulation;
use AppBundle\Form\PostulationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Postulation controller.
 *
 * @Route("postulation")
 */
class PostulationController extends Controller
{
    /**
     * Lists all postulation entities.
     *
     * @Route("/", name="postulation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $postulations = $em->getRepository(Postulation::class)->findAll();

        return $this->render('@Projet/postulation/index.html.twig', array(
            'postulations' => $postulations,
        ));
    }

    /**
     * Creates a new postulation entity.
     *
     * @Route("/new", name="postulation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $postulation = new Postulation();
        $form = $this->createForm(PostulationType::class, $postulation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($postulation);
            $em->flush();

            return $this->redirectToRoute('postulation_new', array('id' => $postulation->getId()));
        }

        return $this->render('@Projet/postulation/new.html.twig', array(
            'postulation' => $postulation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a postulation entity.
     *
     * @Route("/{id}", name="postulation_show")
     * @Method("GET")
     */
    public function showAction(Postulation $postulation)
    {
        $deleteForm = $this->createDeleteForm($postulation);

        return $this->render('@Projet/postulation/show.html.twig', array(
            'postulation' => $postulation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing postulation entity.
     *
     * @Route("/{id}/edit", name="postulation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Postulation $postulation)
    {
        $deleteForm = $this->createDeleteForm($postulation);
        $editForm = $this->createForm(PostulationType::class, $postulation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('postulation_edit', array('id' => $postulation->getId()));
        }

        return $this->render('@Projet/postulation/edit.html.twig', array(
            'postulation' => $postulation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a postulation entity.
     *
     * @Route("/{id}", name="postulation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Postulation $postulation)
    {
        $form = $this->createDeleteForm($postulation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($postulation);
            $em->flush();
        }

        return $this->redirectToRoute('postulation_index');
    }

    /**
     * Creates a form to delete a postulation entity.
     *
     * @param Postulation $postulation The postulation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Postulation $postulation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('postulation_delete', array('id' => $postulation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
