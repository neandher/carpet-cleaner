<?php

namespace App\Controller\Site;

use App\Entity\Address;
use App\Entity\CleaningItem;
use App\Entity\CleaningItemCategory;
use App\Entity\CleaningItemOption;
use App\Entity\CleaningItemOptions;
use App\Entity\Customer;
use App\Entity\CustomerAddresses;
use App\Entity\PromotionCoupon;
use App\Entity\Schedule;
use App\Entity\ScheduleItems;
use App\Entity\ZipCode;
use App\Event\FlashBagEvents;
use App\Event\ScheduleEvents;
use App\Form\ScheduleSiteType;
use App\Util\FlashBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ScheduleController
 * @package App\Controller\Site
 *
 * @Route("/schedule", name="site_schedule_")
 */
class ScheduleController extends AbstractController
{
    /**
     * @var FlashBag
     */
    private $flashBag;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * ScheduleController constructor.
     * @param FlashBag $flashBag
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(FlashBag $flashBag, EventDispatcherInterface $dispatcher)
    {
        $this->flashBag = $flashBag;
        $this->dispatcher = $dispatcher;
    }


    /**
     * @Route("/step-1", name="step_1")
     * @Method("GET")
     * @return Response
     */
    public function step1()
    {
        /** @var CleaningItem[] $items */
        $items = $this->getDoctrine()->getRepository(CleaningItem::class)->findAllCustom();

        /** @var CleaningItemCategory[] $categories */
        $categories = [];

        $checkCategory = [];

        foreach ($items as $item) {
            if (!in_array($item->getCleaningItemCategory()->getId(), $checkCategory)) {
                $checkCategory[] = $item->getCleaningItemCategory()->getId();
                $categories[] = $item->getCleaningItemCategory();
            }
        }

        return $this->render('site/schedule/step-1.html.twig', [
            'items' => $items,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/step-1-post", name="step_1_post")
     * @Method("POST")
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function step1Post(Request $request)
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('add-items', $submittedToken)) {

            if (!$request->getSession()->has('zipCode')) {
                $this->flashBag->newMessage(
                    FlashBagEvents::MESSAGE_TYPE_ERROR,
                    'Zip Code not found!'
                );
            } else {

                if ($request->getSession()->has('checkout'))
                    $request->getSession()->remove('checkout');

                $checkout = [];
                $total = 0;
                $checkSubmit = false;

                if ($request->request->has('items')) {
                    foreach ($request->request->all()['items'] as $item) {
                        if ($item['quantity'] > 0) {

                            $checkSubmit = true;

                            /** @var CleaningItem $cleaningItem */
                            $cleaningItem = $this->getDoctrine()->getRepository(CleaningItem::class)->findOneBy(['id' => $item['id']]);

                            $unitPrice = $cleaningItem->getAmount();

                            /** @var CleaningItemOptions $cleaningItemOption |null */
                            $cleaningItemOption = null;

                            if (!empty($item['item_option'])) {
                                $cleaningItemOption = $this->getDoctrine()->getRepository(CleaningItemOptions::class)->findOneBy(['id' => $item['item_option']]);
                                if ($cleaningItemOption->getPercentage() > 0) {
                                    $unitPrice = ($cleaningItemOption->getPercentage() * $cleaningItem->getAmount()) + $cleaningItem->getAmount();
                                }
                            }

                            /** @var ZipCode $zipCode */
                            $zipCode = $this->getDoctrine()->getRepository(ZipCode::class)->findOneyCustomByDescription(
                                $request->getSession()->get('zipCode')
                            );

                            if ($zipCode->getPercentage() > 0) {
                                $unitPrice = ($zipCode->getPercentage() * $unitPrice) + $unitPrice;
                            }

                            $itemTotal = $unitPrice * $item['quantity'];
                            $total += $itemTotal;

                            $checkout['items'][$item['id']]['item'] = [
                                'title' => $cleaningItem->getTitle(),
                                'categoryTitle' => $cleaningItem->getCleaningItemCategory()->getTitle(),
                                'unitPrice' => $unitPrice,
                                'quantity' => $item['quantity'],
                                'total' => round($itemTotal, 2)
                            ];
                            $checkout['items'][$item['id']]['item_option'] = !empty($item['item_option']) ? [
                                'id' => $cleaningItemOption->getCleaningItemOption()->getId(),
                                'title' => $cleaningItemOption->getCleaningItemOption()->getTitle(),
                            ] : null;
                            $checkout['items'][$item['id']]['item_total'] = $itemTotal;
                        }
                    }

                    if ($checkSubmit) {

                        $checkout['subtotal'] = round($total, 2);
                        $checkout['discount'] = 0.00;
                        $checkout['total'] = round($total, 2);

                        $request->getSession()->set('checkout', $checkout);

                        return $this->redirectToRoute('site_schedule_step_2');
                    }

                    $this->flashBag->newMessage(
                        FlashBagEvents::MESSAGE_TYPE_ERROR,
                        'Add at least one service!'
                    );
                }
            }
        } else {
            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                'Invalid token!'
            );
        }

        return $this->redirectToRoute('site_schedule_step_1');
    }

    /**
     * @Route("/step-2", name="step_2")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function step2(Request $request)
    {
        if ($request->getSession()->has('checkout')) {

            $checkout = $request->getSession()->get('checkout');
            $hasPhoneNumber = $request->getSession()->has('phoneNumber');

            $schedule = new Schedule();

            if (!$hasPhoneNumber) {
                $phoneNumber = $request->getSession()->has('newPhoneNumber') ? $request->getSession()->get('newPhoneNumber') : '';
                $schedule->setCustomer(
                    (new Customer())
                        ->setPhoneNumber($phoneNumber)
                        ->addCustomerAddress(
                            (new CustomerAddresses())
                                ->setAddress(
                                    (new Address())->setZipCode($request->getSession()->get('zipCode'))
                                )
                        )
                );
            }

            $scheduleForm = $this->createForm(ScheduleSiteType::class, $schedule, [
                'hasPhoneNumber' => $hasPhoneNumber
            ]);

            if ($hasPhoneNumber) {
                $customer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy([
                    'phoneNumber' => $request->getSession()->get('phoneNumber')
                ]);
                $schedule->setCustomer($customer);
            }

            /** @var PromotionCoupon|null $promotionCoupon */
            $promotionCoupon = null;

            if ($request->getSession()->has('couponCode')) {
                $promotionCoupon = $this->getDoctrine()->getRepository(PromotionCoupon::class)->findOneBy([
                    'code' => $request->getSession()->get('couponCode')
                ]);
                if (!$checkout['discount'] > 0) {
                    $checkout['discount'] = round($promotionCoupon->getPercentage() * $checkout['subtotal'], 2);
                    $checkout['total'] = $checkout['total'] - $checkout['discount'];
                    $request->getSession()->set('checkout', $checkout);
                }
            }

            $scheduleForm->handleRequest($request);

            if ($scheduleForm->isSubmitted() && $scheduleForm->isValid()) {

                $em = $this->getDoctrine()->getManager();

                $schedule->setItemsTotal($checkout['total'])
                    ->setItemsSubTotal($checkout['subtotal']);

                if ($checkout['discount'] > 0)
                    $schedule->setItemsDiscount($checkout['discount']);

                foreach ($checkout['items'] as $itemId => $item) {

                    $cleaningItem = $this->getDoctrine()->getRepository(CleaningItem::class)
                        ->findOneBy(['id' => $itemId]);
                    $cleaningItemOption = $this->getDoctrine()->getRepository(CleaningItemOption::class)
                        ->findOneBy(['id' => $item['item_option']['id']]);

                    $scheduleItems = new ScheduleItems();
                    $scheduleItems
                        ->setQuantity($item['item']['quantity'])
                        ->setCleaningItem($cleaningItem)
                        ->setSchedule($schedule)
                        ->setTotal($item['item']['total'])
                        ->setUnitPrice($item['item']['unitPrice'])
                        ->setCleaningItemOption($cleaningItemOption);

                    $schedule->addScheduleItem($scheduleItems);
                }

                if ($promotionCoupon) {
                    $used = $promotionCoupon->getUsed() ? $promotionCoupon->getUsed() + 1 : 1;
                    $promotionCoupon->setUsed($used);
                    $em->persist($promotionCoupon);

                    $schedule->setPromotionCoupon($promotionCoupon);
                }

                $em->persist($schedule);
                $em->flush();

                $this->dispatcher->dispatch(ScheduleEvents::SCHEDULE_CREATE_COMPLETED, new GenericEvent($schedule));

                $this->clearSession($request);

                return $this->redirectToRoute('site_schedule_success');
            }

            return $this->render('site/schedule/step-2.html.twig', [
                'form' => $scheduleForm->createView(),
                'hasPhoneNumber' => $hasPhoneNumber,
                'schedule' => $schedule
            ]);
        }

        return $this->redirectToRoute('site_schedule_step_1');
    }

    /**
     * @Route("/restart", name="restart")
     * @Method("GET")
     * @param Request $request
     * @return RedirectResponse
     */
    public function restart(Request $request)
    {
        $this->clearSession($request);
        return $this->redirectToRoute('site_schedule_step_1');
    }

    private function clearSession(Request $request)
    {
        $request->getSession()->remove('checkout');
        $request->getSession()->remove('zipCode');
        $request->getSession()->remove('phoneNumber');
        $request->getSession()->remove('newPhoneNumber');
        $request->getSession()->remove('couponCode');
    }

    /**
     * @Route("/success", name="success")
     * @Method("GET")
     */
    public function scheduleSuccess()
    {
        return $this->render('site/schedule/success.html.twig');
    }

    /**
     * @Route("/find-zip-code", name="find_zip_code")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findZipCode(Request $request)
    {
        $data = [];

        if ($request->isXmlHttpRequest()) {
            $zipCode = $request->request->get('zip_code', null);
            if ($zipCode) {
                $findZipCode = $this->getDoctrine()->getRepository(ZipCode::class)->findOneyCustomByDescription($zipCode);
                if ($findZipCode) {
                    if ($request->getSession()->has('zipCode')) {
                        $request->getSession()->remove('zipCode');
                    }
                    $request->getSession()->set('zipCode', $zipCode);

                    $data['success'] = 'Done';
                    $status = 200;
                } else {
                    $data['error'] = 'Not found';
                    $status = 404;
                }
            } else {
                $data['error'] = 'Parameter not found';
                $status = 404;
            }
        } else {
            $data['error'] = 'Invalid request';
            $status = 400;
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/find-customer", name="find_customer")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function findCustomer(Request $request)
    {
        $data = [];

        if ($request->isXmlHttpRequest()) {

            $phoneNumber = $request->request->get('phone_number', null);

            if ($phoneNumber) {
                if ($findCustomer = $this->getDoctrine()->getRepository(Customer::class)->findOneBy(['phoneNumber' => $phoneNumber])) {

                    if ($request->getSession()->has('phoneNumber')) {
                        $request->getSession()->remove('phoneNumber');
                    }
                    $request->getSession()->set('phoneNumber', $phoneNumber);

                    $data['success'] = 'Done';
                    $status = 200;
                } else {
                    $request->getSession()->set('newPhoneNumber', $phoneNumber);
                    $data['error'] = 'Not found';
                    $status = 404;
                }
            } else {
                $data['error'] = 'Parameter not found';
                $status = 404;
            }
        } else {
            $data['error'] = 'Invalid request';
            $status = 400;
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/find-coupon-code", name="find_coupon_code")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findCouponCode(Request $request)
    {
        $data = [];

        if ($request->isXmlHttpRequest()) {

            $couponCode = $request->request->get('coupon_code', null);

            if ($couponCode) {

                if ($request->getSession()->has('couponCode') && $request->getSession()->get('couponCode') == $couponCode) {
                    $data['error'] = 'This coupon code is already in use';
                    $status = 400;
                } else {
                    if ($findPromotionCoupon = $this->getDoctrine()->getRepository(PromotionCoupon::class)->findByCodeCustom($couponCode)) {

                        if ($request->getSession()->has('couponCode')) {
                            $request->getSession()->remove('couponCode');
                        }
                        $request->getSession()->set('couponCode', $couponCode);

                        $this->flashBag->newMessage(
                            FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                            'Promotional coupon has been apllied successfully'
                        );

                        $data['success'] = 'Done';
                        $status = 200;
                    } else {
                        $data['error'] = 'No promotional coupon are found with this code';
                        $status = 404;
                    }
                }
            } else {
                $data['error'] = 'Parameter not found';
                $status = 404;
            }
        } else {
            $data['error'] = 'Invalid request';
            $status = 400;
        }

        return new JsonResponse($data, $status);
    }

    /**
     * @Route("/check-availability", name="check_availability")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAvailability(Request $request)
    {
        $data = [];
        if ($request->isXmlHttpRequest()) {

            $date = $request->request->get('date', null);
            $startTime = $request->request->get('start_time', null);
            $endTime = $request->request->get('end_time', null);

            if ($date && $startTime && $endTime) {

                $startDate = \DateTime::createFromFormat('m/d/Y H:i', $date . $startTime)->format('m/d/Y H:i');
                $endDate = \DateTime::createFromFormat('m/d/Y H:i', $date . $endTime)->format('m/d/Y H:i');

                if ($startDate && $endDate) {

                    /** @var Schedule[]|null $check */
                    $schedulesNotAvailable = $this->getDoctrine()->getRepository(Schedule::class)->findByDateStartEnd($startDate, $endDate);

                    if (count($schedulesNotAvailable) > 0) {
                        $data['notavailable'] = 'true';
                        $status = 200;
                    } else {
                        $data['success'] = 'true';
                        $status = 200;
                    }
                } else {
                    $data['error'] = 'Invalid request parameters';
                    $status = 400;
                }
            } else {
                $data['error'] = 'Request parameters not found';
                $status = 400;
            }
        } else {
            $data['error'] = 'Invalid request';
            $status = 400;
        }

        return new JsonResponse($data, $status);
    }
}