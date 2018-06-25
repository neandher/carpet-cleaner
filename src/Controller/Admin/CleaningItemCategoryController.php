<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\CleaningItemCategory;
use App\Event\FlashBagEvents;
use App\Form\CleaningItemCategoryType;
use App\Util\FlashBag;
use App\Util\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CleaningItemCategoryController
 * @package App\Controller\Admin
 *
 * @Route("/cleaning-item-category", name="admin_cleaning_item_category_")
 */
class CleaningItemCategoryController extends BaseController
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
     * CleaningItemCategoryController constructor.
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
        $pagination = $this->pagination->handle($request, CleaningItemCategory::class);

        $cleaningItemCategorys = $this->getDoctrine()->getRepository(CleaningItemCategory::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($cleaningItemCategorys as $cleaningItemCategory) {
            $deleteForms[$cleaningItemCategory->getId()] = $this->createDeleteForm($cleaningItemCategory)->createView();
        }

        return $this->render('admin/cleaningItemCategory/index.html.twig', [
            'cleaningItemCategorys' => $cleaningItemCategorys,
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
        $pagination = $this->pagination->handle($request, CleaningItemCategory::class);

        $cleaningItemCategory = new CleaningItemCategory();

        $form = $this->createForm(CleaningItemCategoryType::class, $cleaningItemCategory);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cleaningItemCategory);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_cleaning_item_category_new',
                'admin_cleaning_item_category_edit',
                ['id' => $cleaningItemCategory->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_cleaning_item_category_index');
        }

        return $this->render('admin/cleaningItemCategory/new.html.twig', [
            'cleaningItemCategory' => $cleaningItemCategory,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param CleaningItemCategory $cleaningItemCategory
     * @return Response
     */
    public function editAction(CleaningItemCategory $cleaningItemCategory, Request $request)
    {
        $pagination = $this->pagination->handle($request, CleaningItemCategory::class);

        $form = $this->createForm(CleaningItemCategoryType::class, $cleaningItemCategory);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($cleaningItemCategory);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_cleaning_item_category_new',
                'admin_cleaning_item_category_edit',
                ['id' => $cleaningItemCategory->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_cleaning_item_category_index', $pagination->getRouteParams());
        }

        return $this->render('admin/cleaningItemCategory/edit.html.twig', [
            'cleaningItemCategory' => $cleaningItemCategory,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="delete")
     * @Method("DELETE")
     * @param Request $request
     * @param CleaningItemCategory $cleaningItemCategory
     * @return Response
     */
    public function deletAction(Request $request, CleaningItemCategory $cleaningItemCategory)
    {
        $pagination = $this->pagination->handle($request, CleaningItemCategory::class);

        $form = $this->createDeleteForm($cleaningItemCategory);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($cleaningItemCategory);
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

        return $this->redirectToRoute('admin_cleaning_item_category_index', $pagination->getRouteParams());
    }

    /**
     * @param CleaningItemCategory $cleaningItemCategory
     * @return FormInterface
     */
    private function createDeleteForm(CleaningItemCategory $cleaningItemCategory)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_cleaning_item_category_delete', ['id' => $cleaningItemCategory->getId()]))
            ->setMethod('DELETE')
            ->setData($cleaningItemCategory)
            ->getForm();
    }
}
