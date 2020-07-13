<?php

namespace ProjetBundle\Controller;

use AppBundle\Entity\Postulation;
use AppBundle\Entity\Projet;
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
     * @Route("/{id}", name="postulation_index")
     * @Method("GET")
     */
    public function indexAction(Request $request,Projet $projet)
    {
        $em = $this->getDoctrine()->getManager();

        $postulations = $em->getRepository(Postulation::class)->projectBidders($projet->getId());

        return $this->render('@Projet/postulation/index.html.twig', array(
            'postulations' => $postulations,
        ));
    }

    /**
     * Creates a new postulation entity.
     *
     * @Route("/new/{id}", name="postulation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request,Projet $projet)
    {

        $postulation = new Postulation();
        $postulation->setProject($projet);
        $postulation->setFreelance($user = $this->get('security.token_storage')->getToken()->getUser());
        $form = $this->createForm(\ProjetBundle\Form\PostulationType::class, $postulation);
        $form->remove("freelance")->remove("accepte")->remove("project");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $postulation->setAccepte("en_attente");
            $em->persist($postulation);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('@Projet/postulation/new.html.twig', array(
            'postulation' => $postulation,
            "projet"=>$projet,
            'form' => $form->createView(),
        ));
    }


    /**
     *
     * @Route("/myposts/",name="myposts")
     */

    public function mypost(){
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $postulations=$this->getDoctrine()->getManager()
            ->getRepository(Postulation::class)->Mybids($user->getId());



        return $this->render('@Projet/postulation/show.html.twig', array(
            'postulations' => $postulations,

        ));
    }


    /**
     * @Route("/delmypost", name="delmypost")
     */
    public function deletemybid(Request $request){
        if($request->get("postid")){
            $em=$this->getDoctrine()->getManager();
            $post=$em->getRepository(Postulation::class)->find($request->get("postid"));
            $em->remove($post);
            $em->flush();


        }
        return $this->redirectToRoute("myposts");

    }

    /**
     * @Route("/accept",name="acceptpost")
     */
    public function acceptpost(Request $request){
        if($request->get("id")){
            $em=$this->getDoctrine()->getManager();
            $post=$em->getRepository(Postulation::class)->find($request->get("id"));
            $post->setAccept("ACCEPTED");
            $em->flush();

            $message = (new \Swift_Message("Postulation accepté"))
                ->setFrom('marwene04@gmail.com')
                ->setTo($post->getFreelancer()->getEmail())
                ->setSubject("Postulation accepté")
                ->setBody("Félicitation votre postulation sur le projet ".$post->getProject()->getTitre()." a été accepté veuillez contacter le client pour disccuter les detailles ");
            $this->get('mailer')->send($message);
        }
        return $this->redirectToRoute("postulation_index");


    }

    /**
     * Finds and displays a postulation entity.
     *
     * @Route("/mespost", name="mes_postulations")
     * @Method("GET")
     */
    public function showAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $postulations=$this->getDoctrine()->getManager()
            ->getRepository(Postulation::class)->Mybids($user->getId());



        return $this->render('@Projet/postulation/show.html.twig', array(
            'postulations' => $postulations,

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
