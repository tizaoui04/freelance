<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Categorie;
use AppBundle\Entity\Client;
use AppBundle\Entity\Freelancer;
use AppBundle\Entity\Paiement;
use AppBundle\Entity\Postulation;
use AppBundle\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{

    /**
     * @Route("/stats",name="stats")
     */
    public function stats(){
        $em=$this->getDoctrine()->getManager();
        $catstat=$em->getRepository(Categorie::class)->categorystats();
        $projstat=$em->getRepository(Projet::class)->projectstats();
        $paimstat=$em->getRepository(Paiement::class)->paiementstats();
        return new JsonResponse(array_merge(array("catstat"=>$catstat),array("projstat"=>$projstat),array("paimstat"=>$paimstat)));
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        //let s get project count , freelancers count and post

        $em=$this->getDoctrine()->getManager();
        $fcount=$em->getRepository(Freelancer::class)->freelancercount()[0];
        $pcount=$em->getRepository(Projet::class)->projetcount()[0];
        $postcount=$em->getRepository(Postulation::class)->postcount()[0];
        $jobs=$em->getRepository(Projet::class)->findBy(array(), array("dateprojet"=>"DESC"), 5);
        $freelancers=$em->getRepository(Freelancer::class)->bynote();


        return $this->render('default/index.html.twig',
            array("fcount"=>$fcount,"pcount"=>$pcount,"postcount"=>$postcount,"jobs"=>$jobs,"freelancers"=>$freelancers) );
    }
    /**
     * @Route("/persist", name="persit")
     */
    public function persistance(){
        //this function to add user accounts to database or anything else you want
        //admin
        $admin=new Admin();
        $admin->setPlainPassword("karim123");
        $admin->setEnabled(true);
        $admin->setUsername("admin");
        $admin->setNom("admin");
        $admin->setEmail("admin@admin.com");
        $admin->setPrenom("admin");
        $admin->addRole("ROLE_ADMIN");
        $em=$this->getDoctrine()->getManager();
        $em->persist($admin);
        $em->flush();

        //client
/*
        $client=new Client();

        $client->setPrenom("mirak");
        $client->setEmail("karim@karim.com");
        $client->setNom("slaimi");
        $client->setUsername("mirak");
        $client->setEnabled(true);
        $client->setPlainPassword("karim123");
        $client->setNumtel("94446787");
        $client->setDatenaiss(new \DateTime());
        $client->setAdress("tunis");
        $client->addRole("ROLE_CLIENT");
        $em->persist($client);
        $em->flush();

        //freelancer

        $freelancer=new Freelancer();

        $freelancer->setPrenom("karim");
        $freelancer->setEmail("karimslaimi@karim.com");
        $freelancer->setNom("slaimi");
        $freelancer->setUsername("karim");
        $freelancer->setEnabled(true);
        $freelancer->setPlainPassword("karim123");
        $freelancer->setNumtel("51887898");
        $freelancer->setDate(new \DateTime());
        $freelancer->setDomaine("Dev Web");
        $freelancer->addRole("ROLE_FREELANCER");
        $em->persist($freelancer);
        $em->flush();*/

        return "nice";


    }
}
