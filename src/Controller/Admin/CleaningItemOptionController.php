<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\CleaningItemOption;
use App\Event\FlashBagEvents;
use App\Form\CleaningItemOptionType;
use App\Util\FlashBag;
use App\Util\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CleaningItemOptionController
 * @package App\Controller\Admin
 *
 * @Route("/cleaning-item-option", name="admin_cleaning_item_option_")
 */
class CleaningItemOptionController extends BaseController
{
    /**
     * @var Pagination
     */
    private $pagination;
    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * CleaningItemOptionController constructor.
     * @param Pagination $pagination
     * @param FlashBag $flashBag
     */
    public function __construct(Pagination $pagination, FlashBag $flashBag)
    {
        $this->pagination = $pagination;
        $this->flashBag = $flashBag;
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $pagination = $this->pagination->handle($request, CleaningItemOption::class);

        $cleaningItemOptions = $this->getDoctrine()->getRepository(CleaningItemOption::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($cleaningItemOptions as $cleaningItemOption) {
            $deleteForms[$cleaningItemOption->getId()] = $this->createDeleteForm($cleaningItemOption)->createView();
        }

        return $this->render('admin/cleaningItemOption/index.html.twig', [
            'cleaningItemOptions' => $cleaningItemOptions,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms
        ]);
    }

    /**
     * @Route("/new", name="new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->pagination->handle($request, CleaningItemOption::class);

        $cleaningItemOption = new CleaningItemOption();

        $form = $this->createForm(CleaningItemOptionType::class, $cleaningItemOption);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cleaningItemOption);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_cleaning_item_option_new',
                'admin_cleaning_item_option_edit',
                ['id' => $cleaningItemOption->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_cleaning_item_option_index');
        }

        return $this->render('admin/cleaningItemOption/new.html.twig', [
            'cleaningItemOption' => $cleaningItemOption,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param CleaningItemOption $cleaningItemOption
     * @return Response
     */
    public function editAction(CleaningItemOption $cleaningItemOption, Request $request)
    {
        $pagination = $this->pagination->handle($request, CleaningItemOption::class);

        $form = $this->createForm(CleaningItemOptionType::class, $cleaningItemOption);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($cleaningItemOption);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_cleaning_item_option_new',
                'admin_cleaning_item_option_edit',
                ['id' => $cleaningItemOption->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_cleaning_item_option_index', $pagination->getRouteParams());
        }

        return $this->render('admin/cleaningItemOption/edit.html.twig', [
            'cleaningItemOption' => $cleaningItemOption,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="delete")
     * @Method("DELETE")
     * @param Request $request
     * @param CleaningItemOption $cleaningItemOption
     * @return Response
     */
    public function deletAction(Request $request, CleaningItemOption $cleaningItemOption)
    {
        $pagination = $this->pagination->handle($request, CleaningItemOption::class);

        $form = $this->createDeleteForm($cleaningItemOption);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($cleaningItemOption);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_DELETED
            );
        } else {
            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                FlashBagEvents::MESSAGE_ERROR_DELETED
            );
        }

        return $this->redirectToRoute('admin_cleaning_item_option_index', $pagination->getRouteParams());
    }

    /**
     * @param CleaningItemOption $cleaningItemOption
     * @return FormInterface
     */
    private function createDeleteForm(CleaningItemOption $cleaningItemOption)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_cleaning_item_option_delete', ['id' => $cleaningItemOption->getId()]))
            ->setMethod('DELETE')
            ->setData($cleaningItemOption)
            ->getForm();
    }
}
