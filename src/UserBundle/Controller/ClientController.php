<?php

namespace UserBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\Freelancer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use UserBundle\Form\ClientType;
use UserBundle\Form\FreelancerType;

/**
 * Client controller.
 *
 * @Route("client")
 */
class ClientController extends Controller
{

    /**
     * @Route("/update/{id}",name="update_profilec", requirements={"id":"\d+"})
     * @Method({"GET","POST"})
     */
    public function updateprofile(Request $request, Client  $client){
        $editForm = $this->createForm(ClientType::class, $client);
        $editForm->remove("username")->remove("datenaiss")->remove("plainPassword");

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {


            //get the passwords old one and the new one with its confirmation
            $oldpass = $request->get("oldpass");
            $newpass = $request->get("newpass");
            $sedondpass = $request->get("newpass1");

            if ($oldpass != null && $newpass != null && $sedondpass != null) {


                //get the encoder which encode the password to verify the validity of the old pass
                $encoder = $this->get('security.encoder_factory')->getEncoder($client);
                $salt = $client->getSalt();

                //verify if the neww passwords match if not return the page
                if (strcmp($newpass, $sedondpass) != 0) {
                    return $this->render('@User/client/profile.html.twig', array(
                        'freelancer' => $client,
                        'edit_form' => $editForm->createView(),
                        "msg" => "les mots de passe ne correspondent pas",
                    ));
                }
                //check if the old pass match the given one
                if (!$encoder->isPasswordValid($client->getPassword(), $oldpass, $salt)) {
                    return $this->render('@User/client/profile.html.twig', array(
                        'freelancer' => $client,
                        'edit_form' => $editForm->createView(),
                        "msg" => "Verifiez mot de passe actuel",
                    ));
                } else {
                    $client->setPlainPassword($newpass);
                }


            }
            if($request->get("datenaiss")){
                $client->setDatenaiss(new \DateTime($request->get("datenaiss")));
            }



            if ($editForm->isValid()) {
                $client->setUpdateAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('update_profilec', array('id' => $client->getId()));
        }
        }

        return $this->render('@User/client/profile.html.twig', array(
            'client' => $client,
            'edit_form' => $editForm->createView(),
        ));

    }

    /**
     * Lists all client entities.
     *
     * @Route("/", name="client_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $clients = $em->getRepository(Client::class)->findAll();

        return $this->render('@User/client/index.html.twig', array(
            'clients' => $clients,
        ));
    }

    /**
     * Creates a new client entity.
     *
     * @Route("/new", name="client_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('client_show', array('id' => $client->getId()));
        }

        return $this->render('@User/client/new.html.twig', array(
            'client' => $client,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a client entity.
     *
     * @Route("/{id}", name="client_show")
     * @Method("GET")
     */
    public function showAction(Client $client)
    {
        $deleteForm = $this->createDeleteForm($client);

        return $this->render('@User/client/show.html.twig', array(
            'client' => $client,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing client entity.
     *
     * @Route("/{id}/edit", name="client_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Client $client)
    {
        $deleteForm = $this->createDeleteForm($client);
        $editForm = $this->createForm(ClientType::class, $client);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_edit', array('id' => $client->getId()));
        }

        return $this->render('@User/client/edit.html.twig', array(
            'client' => $client,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a client entity.
     *
     * @Route("delete/{id}", name="client_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Client $client)
    {
        $form = $this->createDeleteForm($client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($client);
            $em->flush();
        }

        return $this->redirectToRoute('client_index');
    }

    /**
     * Creates a form to delete a client entity.
     *
     * @param Client $client The client entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Client $client)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('client_delete', array('id' => $client->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
