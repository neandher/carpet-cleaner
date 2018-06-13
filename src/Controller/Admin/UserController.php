<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller\Admin
 *
 * @Route("/user", name="admin_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $pagination = $this->get('app.util.pagination')->handle($request, User::class);

        $adminUsers = $this->getDoctrine()->getRepository(User::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($adminUsers as $user) {
            $deleteForms[$user->getId()] = $this->createDeleteForm($user)->createView();
        }

        return $this->render('admin/user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
