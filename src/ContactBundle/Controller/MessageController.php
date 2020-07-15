<?php

namespace ContactBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Message controller.
 *
 * @Route("message")
 */
class MessageController extends Controller
{

    //ok now we have to define 4 methode 2 for freelancer check the messages and his inbox  and send a message and the same for the client
    //i think i will have a lot to fix XD


    /**
     * Lists all message entities.
     *
     * @Route("/mess/", name="messlist_noid")
     * @Route("/mess/{id}", name="messlist")
     *
     */
    public function messlist(Request $request){
        //we defined 2 route for this methode if it s with params then he will get the message for the selected user if not he will get inbox list only
        //this method work for both user client and freelancer so we have to just make a test on the role to return the desired template
        $user = $this->get('security.token_storage')->getToken()->getUser();
        //know we have the user let s get his inbox

        $em=$this->getDoctrine()->getManager();

        $inbox=$em->getRepository(Message::class)->getinbox($user->getId());

        if($request->get("id")|| $request->query->get("id")){
            $id=$request->get("id")?$request->get("id"):$request->query->get("id");
            $mess=$em->getRepository(Message::class)->getmine($user->getId(),$id);
            $receiver=$em->getRepository(User::class)->find($id);



            if($this->container->get('security.authorization_checker')->isGranted("ROLE_FREELANCER")){
                return $this->render("@Contact/message/Fmesstemp.html.twig",array("inbox"=>$inbox,"messages"=>$mess,"receiver"=>$receiver));
            }else if($this->container->get('security.authorization_checker')->isGranted("ROLE_CLIENT")){
                return $this->render("@Contact/message/Cmesstemp.html.twig",array("inbox"=>$inbox,"messages"=>$mess,"receiver"=>$receiver));
            }



        }



        if($this->container->get('security.authorization_checker')->isGranted("ROLE_FREELANCER")){
            return $this->render("@Contact/message/Fmesstemp.html.twig",array("inbox"=>$inbox));
        }else if($this->container->get('security.authorization_checker')->isGranted("ROLE_CLIENT")){
            return $this->render("@Contact/message/Cmesstemp.html.twig",array("inbox"=>$inbox));
        }
    }

    /**
     * Creates a new message entity.
     *
     * @Route("/send", name="sendmsg")
     * @Method({"GET", "POST"})
     */
    public function sendAction(Request $request)
    {

        //for freelancer to send message

        if($request->get("mstxt")){
            $message = new Message();
            $em=$this->getDoctrine()->getManager();
            $message->setContenu($request->get("mstxt"));
            $message->setDate(new \DateTime());
            $message->setReceiver($em->getRepository(User::class)->find($request->get("to")));
            $message->setSender($this->get('security.token_storage')->getToken()->getUser());
            $em->persist($message);
            $em->flush();
            if($request->get("clmodal")){
                return $this->redirectToRoute("postulation_index",array("id"=>$request->get("projid")));
            }
            if($request->get("frmodal")){
                return $this->redirectToRoute("myposts");
            }
            return $this->redirectToRoute("messlist",array("id"=>$request->get("to")));


        }
        if($request->get("to")){
            return $this->redirectToRoute("messlist",array("id"=>$request->get("to")));
        }



        return $this->redirectToRoute("messlist");
    }






    /**
     * @Route("/startmsg",name="startmsg")
     */
    public function sendnewmsg(Request $request){
        //so this methode is for a client or a freelancer to start contacting each others we will only need to get the id for the receiver from the request and ofc the message and get the authenticated user
        $em=$this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
       if($request->isMethod("Get")){
           $inbox=$em->getRepository(Message::class)->getinbox($user->getId());
           $receiver=$em->getRepository(User::class)->find($request->get("id"));
           return $this->render("@Contact/message/start contacting.html.twig",array("receiver"=>$receiver,"inbox"=>$inbox));
       }else{
           if($request->get("mstxt")){
           $em=$this->getDoctrine()->getManager();
           $receiver=$em->getRepository(User::class)->find($request->get("id"));
           $message=new Message();
           $message->setReceiver($receiver);

           $message->setSender($user);
           $message->setContenu($request->get("mstxt"));
           $message->setDate(new \DateTime());
           $em->persist($message);
           $em->flush();
           return $this->redirectToRoute("messlist",array("id"=>$receiver->getId()));



           }
           if($request->get("to")){
               return $this->redirectToRoute("messlist",array("id"=>$request->get("to")));
           }
       }

        return $this->redirectToRoute("messlist");



    }








    /**
     * Lists all message entities.
     *
     * @Route("/", name="message_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $messages = $em->getRepository('AppBundle:Message')->findAll();

        return $this->render('@Contact/message/index.html.twig', array(
            'messages' => $messages,
        ));
    }

    /**
     * Creates a new message entity.
     *
     * @Route("/new", name="message_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $message = new Message();
        $form = $this->createForm('ContactBundle\Form\MessageType', $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('message_show', array('id' => $message->getId()));
        }

        return $this->render('@Contact/message/new.html.twig', array(
            'message' => $message,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a message entity.
     *
     * @Route("/{id}", name="message_show")
     * @Method("GET")
     */
    public function showAction(Message $message)
    {
        $deleteForm = $this->createDeleteForm($message);

        return $this->render('@Contact/message/show.html.twig', array(
            'message' => $message,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing message entity.
     *
     * @Route("/{id}/edit", name="message_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Message $message)
    {
        $deleteForm = $this->createDeleteForm($message);
        $editForm = $this->createForm('AppBundle\Form\MessageType', $message);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('message_edit', array('id' => $message->getId()));
        }

        return $this->render('@Contact/message/edit.html.twig', array(
            'message' => $message,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a message entity.
     *
     * @Route("/{id}", name="message_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Message $message)
    {
        $form = $this->createDeleteForm($message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($message);
            $em->flush();
        }

        return $this->redirectToRoute('message_index');
    }

    /**
     * Creates a form to delete a message entity.
     *
     * @param Message $message The message entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Message $message)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('message_delete', array('id' => $message->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
