<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\ZipCode;
use App\Event\FlashBagEvents;
use App\Form\ZipCodeType;
use App\Util\FlashBag;
use App\Util\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ZipCodeController
 * @package App\Controller\Admin
 *
 * @Route("/zip-code", name="admin_zip_code_")
 */
class ZipCodeController extends BaseController
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
     * ZipCodeController constructor.
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
        $pagination = $this->pagination->handle($request, ZipCode::class);

        $zipCodes = $this->getDoctrine()->getRepository(ZipCode::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($zipCodes as $zipCode) {
            $deleteForms[$zipCode->getId()] = $this->createDeleteForm($zipCode)->createView();
        }

        return $this->render('admin/zipCode/index.html.twig', [
            'zipCodes' => $zipCodes,
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
        $pagination = $this->pagination->handle($request, ZipCode::class);

        $zipCode = new ZipCode();

        $form = $this->createForm(ZipCodeType::class, $zipCode);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($zipCode);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_zip_code_new',
                'admin_zip_code_edit',
                ['id' => $zipCode->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_zip_code_index');
        }

        return $this->render('admin/zipCode/new.html.twig', [
            'zipCode' => $zipCode,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param ZipCode $zipCode
     * @return Response
     */
    public function editAction(ZipCode $zipCode, Request $request)
    {
        $pagination = $this->pagination->handle($request, ZipCode::class);

        $form = $this->createForm(ZipCodeType::class, $zipCode);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($zipCode);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_zip_code_new',
                'admin_zip_code_edit',
                ['id' => $zipCode->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_zip_code_index', $pagination->getRouteParams());
        }

        return $this->render('admin/zipCode/edit.html.twig', [
            'zipCode' => $zipCode,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="delete")
     * @Method("DELETE")
     * @param Request $request
     * @param ZipCode $zipCode
     * @return Response
     */
    public function deletAction(Request $request, ZipCode $zipCode)
    {
        $pagination = $this->pagination->handle($request, ZipCode::class);

        $form = $this->createDeleteForm($zipCode);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($zipCode);
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

        return $this->redirectToRoute('admin_zip_code_index', $pagination->getRouteParams());
    }

    /**
     * @param ZipCode $zipCode
     * @return FormInterface
     */
    private function createDeleteForm(ZipCode $zipCode)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_zip_code_delete', ['id' => $zipCode->getId()]))
            ->setMethod('DELETE')
            ->setData($zipCode)
            ->getForm();
    }
}
