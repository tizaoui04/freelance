<?php

namespace UserBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Entity\Freelancer;
use AppBundle\Entity\User;
use Cassandra\Date;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/signin-form",name="formsignin")
     */
    public function indexAction()
    {

        return $this->render('@User/Default/login.html.twig');
    }



    /**
     * @Route("/signup",name="signup")
     * @Method({"GET","POST"})
     */
    public function signup(Request $request){
        if($request->isMethod("GET")){
            return $this->render('@User/Default/Signup.html.twig');
        }
        $validator=Validation::createValidator();
        $type=$request->get("account-type-radio");

        $firstpass=$request->get('password');
        $secondpass=$request->get("passtwo");
        if(strcmp($firstpass,$secondpass)!=0){
            return $this->render('@User/Default/Signup.html.twig',array("msg"=>"verifier les mot de passe"));
        }
        if(strcmp($type,"freelancer")==0){
            $freelancer=new Freelancer();
            $freelancer->setNom($request->get("nom"));
            $freelancer->setPrenom($request->get("prenom"));
            $freelancer->setNumtel($request->get("numtel"));
            $freelancer->setDate(new \DateTime($request->get("date")));
            $freelancer->setEmail($request->get("mail"));
            $freelancer->setUsername($request->get("username"));
            $freelancer->setPlainPassword($request->get("password"));
            $freelancer->addRole("ROLE_FREELANCER");
            $freelancer->setDomaine("");
            $errors = $validator->validate($freelancer);
            if(count($errors)>0){
                return $this->render('@User/Default/Signup.html.twig',array("msg"=>"verifier les donnes saisie"));
            }else{
                $token = sha1(uniqid());
                $freelancer->setConfirmationToken($token);
                $this->getDoctrine()->getManager()->persist($freelancer);
                $this->getDoctrine()->getManager()->flush();

                $message = (new \Swift_Message("Verification Email"))
                    ->setFrom('marwene04@gmail.com')
                    ->setTo($freelancer->getEmail())
                    ->setSubject("Verification Email")
                    ->setBody(
                        $this->renderView('@User/email/confirm.html.twig',[
                            'name' => $freelancer->getUsername() ,
                            'uri' => 'verification',
                            'param' => ['token' => $token]
                        ]),
                        'text/html'
                    );


                $this->get('mailer')->send($message);

                return new Response("<p> check your email </p>");
            }
        }else{
            $client=new Client();
            $client->setNom($request->get("nom"));
            $client->setPrenom($request->get("prenom"));
            $client->setNumtel($request->get("numtel"));
            $client->setDatenaiss(new \DateTime($request->get("date")));
            $client->setEmail($request->get("mail"));
            $client->setUsername($request->get("username"));
            $client->setAdress("");
            $client->setPlainPassword($request->get("password"));
            $client->addRole("ROLE_CLIENT");

            $errors = $validator->validate($client);
            if(count($errors)>0){
                return $this->render('@User/Default/Signup.html.twig',array("msg"=>"verifier les donnes saisie"));
            }else{

                $token = sha1(uniqid());
                $client->setConfirmationToken($token);
                $this->getDoctrine()->getManager()->persist($client);
                $this->getDoctrine()->getManager()->flush();

                $message = (new \Swift_Message("Verification Email"))
                    ->setFrom('marwene04@gmail.com')
                    ->setTo($client->getEmail())
                    ->setSubject("Verification Email")
                    ->setBody(
                        $this->renderView('@User/email/confirm.html.twig',[
                            'name' => $client->getUsername() ,
                            'uri' => 'verification',
                            'param' => ['token' => $token]
                        ]),
                        'text/html'
                    );


                $this->get('mailer')->send($message);

                return new Response("<p> check your email </p>");
            }
        }



    }


    /**
     * @Route("/verif/{token}",name="verification")
     * @Method({"GET"})
     */
    public function verifAction(Request $request,$token)
    {
        $usermanager = $this->get('fos_user.user_manager');
        $user = $usermanager->findUserByConfirmationToken($token);
        if($user ){
            $user->setEnabled(true);
            $user->setConfirmationToken(null);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('homepage');
        }else{
            return $this->redirectToRoute('emailerror',['msg' => 'erreur de token']);
        }

    }




    /**
     * @Route("/emailerror/{msg}",name="emailerror")
     * @Method({"GET"})
     */
    public function emailError(Request $request,$msg)
    {
        $this->render('@User/email/err.html.twig',['msg' => $msg]);
    }

    /**
     * @Route("/login", name="user_login")
     */
    public function loginAction(Request $request){

        $username=$request->get('username');
        $password=$request->get('password');

        if($request->isMethod("GET")){
            return $this->render("@User/Default/login.html.twig",array("msg"=>""));
        }else{

            // Retrieve the security encoder of symfony
            $factory = $this->get('security.encoder_factory');


            $user_manager = $this->get('fos_user.user_manager');
            //$user = $user_manager->findUserByUsername($username);
            // Or by yourself
            $user = $this->getDoctrine()->getManager()->getRepository("AppBundle:User")
                ->findOneBy(array('username' => $username));
            /// End Retrieve user

            // Check if the user exists !
            if(!$user){
                return $this->render("@User/Default/login.html.twig",array("msg"=>"username non trouvé"));
            }

            if(!$user->isEnabled()){
                return $this->render("@User/Default/login.html.twig",array("msg"=>"username n'est pas active check your email"));
            }

            /// Start verification
            $encoder = $factory->getEncoder($user);
            $salt = $user->getSalt();

            if(!$encoder->isPasswordValid($user->getPassword(), $password, $salt)) {
                return $this->render("@User/Default/login.html.twig",array("msg"=>"username ou mot de passe incorrect"));
            }
            /// End Verification

            // The password matches ! then proceed to set the user in session

            //Handle getting or creating the user entity likely with a posted form
            // The third parameter "main" can change according to the name of your firewall in security.yml
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);

            // If the firewall name is not main, then the set value would be instead:
            // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
            $this->get('session')->set('_security_main', serialize($token));

            // Fire the login event manually
            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            // $us=$this->container->get('security.token_storage')->getToken()->getUser();

            if ($this->container->get('security.authorization_checker')->isGranted("ROLE_FREELANCER")) {

                return $this->redirectToRoute("homepage");
            }elseif($this->container->get('security.authorization_checker')->isGranted('ROLE_CLIENT')) {
                // Everyone else goes to the `home` route
                return $this->redirectToRoute("homepage");
            }

        }
    }
}
