<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\CleaningItem;
use App\Entity\CleaningItemCategory;
use App\Event\FlashBagEvents;
use App\Form\CleaningItemType;
use App\Util\FlashBag;
use App\Util\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CleaningItemController
 * @package App\Controller\Admin
 *
 * @Route("/cleaning-item", name="admin_cleaning_item_")
 */
class CleaningItemController extends BaseController
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
     * CleaningItemController constructor.
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
        $pagination = $this->pagination->handle($request, CleaningItem::class);

        $cleaningItems = $this->getDoctrine()->getRepository(CleaningItem::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($cleaningItems as $cleaningItem) {
            $deleteForms[$cleaningItem->getId()] = $this->createDeleteForm($cleaningItem)->createView();
        }

        $cleaningItemCategories = $this->getDoctrine()->getRepository(CleaningItemCategory::class)->findAll();

        return $this->render('admin/cleaningItem/index.html.twig', [
            'cleaningItems' => $cleaningItems,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms,
            'categories' => $cleaningItemCategories
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
        $pagination = $this->pagination->handle($request, CleaningItem::class);

        $cleaningItem = new CleaningItem();

        $form = $this->createForm(CleaningItemType::class, $cleaningItem);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cleaningItem);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_cleaning_item_new',
                'admin_cleaning_item_edit',
                ['id' => $cleaningItem->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_cleaning_item_index');
        }

        return $this->render('admin/cleaningItem/new.html.twig', [
            'cleaningItem' => $cleaningItem,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param CleaningItem $cleaningItem
     * @return Response
     */
    public function editAction(CleaningItem $cleaningItem, Request $request)
    {
        $pagination = $this->pagination->handle($request, CleaningItem::class);

        $form = $this->createForm(CleaningItemType::class, $cleaningItem);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($cleaningItem);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_cleaning_item_new',
                'admin_cleaning_item_edit',
                ['id' => $cleaningItem->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_cleaning_item_index');
        }

        return $this->render('admin/cleaningItem/edit.html.twig', [
            'cleaningItem' => $cleaningItem,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="delete")
     * @Method("DELETE")
     * @param Request $request
     * @param CleaningItem $cleaningItem
     * @return Response
     */
    public function deletAction(Request $request, CleaningItem $cleaningItem)
    {
        $pagination = $this->pagination->handle($request, CleaningItem::class);

        $form = $this->createDeleteForm($cleaningItem);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($cleaningItem);
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

        return $this->redirectToRoute('admin_cleaning_item_index', $pagination->getRouteParams());
    }

    /**
     * @param CleaningItem $cleaningItem
     * @return FormInterface
     */
    private function createDeleteForm(CleaningItem $cleaningItem)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_cleaning_item_delete', ['id' => $cleaningItem->getId()]))
            ->setMethod('DELETE')
            ->setData($cleaningItem)
            ->getForm();
    }
}
