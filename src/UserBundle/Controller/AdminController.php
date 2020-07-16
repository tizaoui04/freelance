<?php

namespace UserBundle\Controller;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Freelancer;
use AppBundle\Entity\Projet;
use AppBundle\Entity\Reclamation;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Form\AdminType;
use UserBundle\Form\FreelancerType;

/**
 * Admin controller.
 *
 * @Route("admin")
 */
class AdminController extends Controller
{

    /**
     * @Route("/update/{id}",name="admin_profile", requirements={"id":"\d+"})
     * @Method({"GET","POST"})
     */
    public function updateprofile(Request $request, Admin $admin)
    {
        $editForm = $this->createForm(AdminType::class, $admin);
        $editForm->remove("date")->remove("plainPassword")->remove("username");
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {


            //get the passwords old one and the new one with its confirmation
            $oldpass = $request->get("oldpass");
            $newpass = $request->get("newpass");
            $sedondpass = $request->get("newpass1");

            if ($oldpass != null && $newpass != null && $sedondpass != null) {


                //get the encoder which encode the password to verify the validity of the old pass
                $encoder = $this->get('security.encoder_factory')->getEncoder($admin);
                $salt = $admin->getSalt();

                //verify if the neww passwords match if not return the page
                if (strcmp($newpass, $sedondpass) != 0) {
                    return $this->render('@User/admin/profile.html.twig', array(
                        'freelancer' => $admin,
                        'edit_form' => $editForm->createView(),
                        "msg" => "les mots de passe ne correspondent pas",
                    ));
                }
                //check if the old pass match the given one
                if (!$encoder->isPasswordValid($admin->getPassword(), $oldpass, $salt)) {
                    return $this->render('@User/admin/profile.html.twig', array(
                        'freelancer' => $admin,
                        'edit_form' => $editForm->createView(),
                        "msg" => "Verifiez mot de passe actuel",
                    ));
                } else {
                    $admin->setPlainPassword($newpass);
                }


            }


            if ($editForm->isValid()) {


                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('admin_profile', array('id' => $admin->getId(),"msg"=>"Votre profile a été mise a jour"));
            }
        }


        return $this->render('@User/admin/profile.html.twig', array(
            'freelancer' => $admin,
            'edit_form' => $editForm->createView(),
            "msg"=>$request->get("msg"),
        ));

    }


    /**
     * @Route("/projects",name="project_management")
     */
    public function projects(){
        $em=$this->getDoctrine()->getManager();
        $projects=$em->getRepository(Projet::class)->findAll();
        return $this->render("@User/admin/Projects.html.twig",array("projets"=>$projects));

    }


    /**
     * @Route("/delproject/{id}",name="delproj")
     */
    public function deleteproject(Projet $projet){
        $this->getDoctrine()->getManager()->remove($projet);

        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute("project_management");
    }

    /**
     * @Route("/reclamations",name="reclamation_management")
     */
    public function reclamations(){
        $em=$this->getDoctrine()->getManager();
        $reclams=$em->getRepository(Reclamation::class)->findAll();
        return $this->render("@User/admin/Reclamations.html.twig",array("reclamations"=>$reclams));

    }

    public function fixreclam(Reclamation $reclamation,Request $request){
        $reclamation->setEtat(true);
        $this->getDoctrine()->getManager()->flush();
        $message = (new \Swift_Message("Reclamation traité"))
            ->setFrom('marwene04@gmail.com')
            ->setTo($reclamation->getSender()->getEmail())
            ->setSubject("Reclamation ".$reclamation->getTitle()." traité")
            ->setBody($request->get("message"));
        $this->get('mailer')->send($message);
        return $this->redirectToRoute("reclamation_management");
    }



    /**
     * Lists all admin entities.
     *
     * @Route("/", name="admin_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $admins = $em->getRepository('AppBundle:Admin')->findAll();

        return $this->render('@User/admin/index.html.twig', array(
            'admins' => $admins,
        ));
    }

    /**
     * Creates a new admin entity.
     *
     * @Route("/new", name="admin_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //hethy lezma add role t9olo chnya role mte3o
            $admin->addRole("ROLE_ADMIN");
            $em->persist($admin);
            $em->flush();

            return $this->redirectToRoute('admin_show', array('id' => $admin->getId()));
        }

        return $this->render('@User/admin/new.html.twig', array(
            'admin' => $admin,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a admin entity.
     *
     * @Route("/{id}", name="admin_show")
     * @Method("GET")
     */
    public function showAction(Admin $admin)
    {
        $deleteForm = $this->createDeleteForm($admin);

        return $this->render('@User/admin/show.html.twig', array(
            'admin' => $admin,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing admin entity.
     *
     * @Route("/{id}/edit", name="admin_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Admin $admin)
    {
        $deleteForm = $this->createDeleteForm($admin);
        $editForm = $this->createForm(AdminType::class, $admin);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_show', array('id' => $admin->getId()));
        }

        return $this->render('@User/admin/edit.html.twig', array(
            'admin' => $admin,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a admin entity.
     *
     * @Route("/{id}", name="admin_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Admin $admin)
    {
        $form = $this->createDeleteForm($admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($admin);
            $em->flush();
        }

        return $this->redirectToRoute('admin_index');
    }

    /**
     * Creates a form to delete a admin entity.
     *
     * @param Admin $admin The admin entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Admin $admin)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_delete', array('id' => $admin->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
