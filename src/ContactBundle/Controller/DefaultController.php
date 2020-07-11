<?php

namespace ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/contact")
     */
    public function indexAction()
    {
        return $this->render('@Contact/Default/index.html.twig');
    }
}
