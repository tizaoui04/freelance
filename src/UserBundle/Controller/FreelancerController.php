<?php

namespace UserBundle\Controller;

use AppBundle\Entity\Freelancer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
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
    public function updateprofile(Request $request, Freelancer $freelancer){
        $editForm = $this->createForm(FreelancerType::class, $freelancer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('update_profile', array('id' => $freelancer->getId()));
        }

        return $this->render('@User/freelancer/profile.html.twig', array(
            'freelancer' => $freelancer,
            'edit_form' => $editForm->createView(),
        ));

    }

    /**
     * @Route("/updatepass",name="update_pass")
     */
    public function updatepass(Request $request, Freelancer $freelancer)
    {

        $factory = $this->get('security.encoder_factory');

        $encoder = $factory->getEncoder($freelancer);


        if ($encoder->isPasswordValid($freelancer->getPassword(), $request->get("oldpass"), $freelancer->getSalt()) and
            strcmp($request->get("newpass"), $request->get("newpass1")) == 0) {
            $freelancer->setPlainPassword($request->get("newpass"));
            $this->getDoctrine()->getManager()->flush();
            return $this->render('@User/freelancer/profile.html.twig', array(
                'edit_form' => $this->createForm(FreelancerType::class, $freelancer)->createView(),
                "freelancer" => $freelancer, "passmsg" => "Mot de pass verifié"));

        } else {
            return $this->render('@User/freelancer/profile.html.twig', array(
                'edit_form' => $this->createForm(FreelancerType::class, $freelancer)->createView(),
                "freelancer" => $freelancer, "passmsg" => "Verifiez Mot de pass verifié"));


        }
    }


    /**
     * @Route("/updateskills",name="updateskills")
     */
    public function updateskills(Request $request,Freelancer $freelancer){


        if($request->get("skills")){
            $freelancer->setDomaine($request->get("skills"));
            $this->getDoctrine()->getManager()->flush();
            return $this->render('@User/freelancer/profile.html.twig', array(
                'freelancer' => $freelancer,
                'edit_form' => $this->createForm(FreelancerType::class, $freelancer)->createView(),
                "skillsmsg"=>"domaine modifié"
            ));
        }
        return $this->render('@User/freelancer/profile.html.twig', array(
            'freelancer' => $freelancer,
            'edit_form' => $this->createForm(FreelancerType::class, $freelancer)->createView(),
        ));




    }








    /**
     * Lists all freelancer entities.
     *
     * @Route("/", name="freelancer_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $freelancers = $em->getRepository(Freelancer::class)->findAll();

        return $this->render('@User/freelancer/index.html.twig', array(
            'freelancers' => $freelancers,
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

            $freelancer->addRole("ROLE_ADMIN");

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
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Freelancer $freelancer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('freelancer_delete', array('id' => $freelancer->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
