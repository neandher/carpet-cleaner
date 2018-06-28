<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\PromotionCoupon;
use App\Event\FlashBagEvents;
use App\Form\PromotionCouponType;
use App\Util\FlashBag;
use App\Util\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PromotionCouponController
 * @package App\Controller\Admin
 *
 * @Route("/promotion-coupon", name="admin_promotion_coupon_")
 */
class PromotionCouponController extends BaseController
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
     * PromotionCouponController constructor.
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
        $pagination = $this->pagination->handle($request, PromotionCoupon::class);

        $promotionCoupons = $this->getDoctrine()->getRepository(PromotionCoupon::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($promotionCoupons as $promotionCoupon) {
            $deleteForms[$promotionCoupon->getId()] = $this->createDeleteForm($promotionCoupon)->createView();
        }

        return $this->render('admin/promotionCoupon/index.html.twig', [
            'promotionCoupons' => $promotionCoupons,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms,
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
        $pagination = $this->pagination->handle($request, PromotionCoupon::class);

        $promotionCoupon = new PromotionCoupon();

        $form = $this->createForm(PromotionCouponType::class, $promotionCoupon);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($promotionCoupon);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_promotion_coupon_new',
                'admin_promotion_coupon_edit',
                ['id' => $promotionCoupon->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_promotion_coupon_index', $pagination->getRouteParams());
        }

        return $this->render('admin/promotionCoupon/new.html.twig', [
            'promotionCoupon' => $promotionCoupon,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param PromotionCoupon $promotionCoupon
     * @return Response
     */
    public function editAction(PromotionCoupon $promotionCoupon, Request $request)
    {
        $pagination = $this->pagination->handle($request, PromotionCoupon::class);

        $form = $this->createForm(PromotionCouponType::class, $promotionCoupon);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($promotionCoupon);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_promotion_coupon_new',
                'admin_promotion_coupon_edit',
                ['id' => $promotionCoupon->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_promotion_coupon_index');
        }

        return $this->render('admin/promotionCoupon/edit.html.twig', [
            'promotionCoupon' => $promotionCoupon,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="delete")
     * @Method("DELETE")
     * @param Request $request
     * @param PromotionCoupon $promotionCoupon
     * @return Response
     */
    public function deletAction(Request $request, PromotionCoupon $promotionCoupon)
    {
        $pagination = $this->pagination->handle($request, PromotionCoupon::class);

        $form = $this->createDeleteForm($promotionCoupon);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($promotionCoupon);
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

        return $this->redirectToRoute('admin_promotion_coupon_index', $pagination->getRouteParams());
    }

    /**
     * @param PromotionCoupon $promotionCoupon
     * @return FormInterface
     */
    private function createDeleteForm(PromotionCoupon $promotionCoupon)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_promotion_coupon_delete', ['id' => $promotionCoupon->getId()]))
            ->setMethod('DELETE')
            ->setData($promotionCoupon)
            ->getForm();
    }
}
