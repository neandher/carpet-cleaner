<?php

namespace App\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller\Site
 *
 * @Route("", name="site_")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('site/home/step-1.html.twig');
    }

    /**
     * @Route("/step-2", name="step2")
     */
    public function step2()
    {
        return $this->render('site/home/step-2.html.twig');
    }
}