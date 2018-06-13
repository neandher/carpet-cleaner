<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Util\Pagination;
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
     * @var Pagination
     */
    private $pagination;

    /**
     * UserController constructor.
     * @param Pagination $pagination
     */
    public function __construct(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $pagination = $this->pagination->handle($request, User::class);

        $users = $this->getDoctrine()->getRepository(User::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($users as $user) {
            $deleteForms[$user->getId()] = $this->createDeleteForm($user)->createView();
        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms
        ]);
    }
}
