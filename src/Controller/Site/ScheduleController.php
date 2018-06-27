<?php

namespace App\Controller\Site;

use App\Entity\CleaningItem;
use App\Entity\CleaningItemCategory;
use App\Entity\CleaningItemOptions;
use App\Entity\Schedule;
use App\Entity\ScheduleItems;
use App\Entity\ZipCode;
use App\Event\FlashBagEvents;
use App\Form\ScheduleSiteType;
use App\Util\FlashBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * ScheduleController constructor.
     * @param FlashBag $flashBag
     */
    public function __construct(FlashBag $flashBag)
    {
        $this->flashBag = $flashBag;
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

                            $itemTotal = $cleaningItem->getAmount() * $item['quantity'];

                            /** @var CleaningItemOptions $cleaningItemOption |null */
                            $cleaningItemOption = null;

                            if (!empty($item['item_option'])) {
                                $cleaningItemOption = $this->getDoctrine()->getRepository(CleaningItemOptions::class)->findOneBy(['id' => $item['item_option']]);
                                if ($cleaningItemOption->getPercentage() > 0) {
                                    $itemTotal = (($cleaningItemOption->getPercentage() * $cleaningItem->getAmount()) + $cleaningItem->getAmount()) * $item['quantity'];
                                }
                            }

                            /** @var ZipCode $zipCode */
                            $zipCode = $this->getDoctrine()->getRepository(ZipCode::class)->findOneyCustomByDescription(
                                $request->getSession()->get('zipCode')
                            );

                            if ($zipCode->getPercentage() > 0) {
                                $itemTotal = ($zipCode->getPercentage() * $itemTotal) + $itemTotal;
                            }

                            $total += $itemTotal;

                            $checkout['items'][$item['id']]['item'] = [
                                'title' => $cleaningItem->getTitle(),
                                'categoryTitle' => $cleaningItem->getCleaningItemCategory()->getTitle(),
                                'quantity' => $item['quantity'],
                                'total' => round($itemTotal, 2)
                            ];
                            $checkout['items'][$item['id']]['item_option'] = !empty($item['item_option']) ? [
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

            $schedule = new Schedule();
            $scheduleForm = $this->createForm(ScheduleSiteType::class, $schedule);
            $scheduleForm->handleRequest($request);

            if ($scheduleForm->isSubmitted() && $scheduleForm->isValid()) {

                $schedule->setItemsTotal($checkout['total']);

                foreach ($checkout['items'] as $itemId => $item) {

                    $cleaningItem = $this->getDoctrine()->getRepository(CleaningItem::class)->findOneBy(['id' => $itemId]);

                    $scheduleItems = new ScheduleItems();
                    $scheduleItems
                        ->setQuantity($item['item']['quantity'])
                        ->setCleaningItem($cleaningItem)
                        ->setSchedule($schedule)
                        ->setTotal($item['item']['total'])
                        ->setUnitPrice($item['item_total']);

                    $schedule->addScheduleItem($scheduleItems);
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($schedule);
                $em->flush();

                $request->getSession()->remove('checkout');
                $request->getSession()->remove('zipCode');

                $this->flashBag->newMessage(
                    FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                    FlashBagEvents::MESSAGE_SUCCESS_INSERTED
                );

                return $this->redirectToRoute('site_schedule_success');
            }

            return $this->render('site/schedule/step-2.html.twig', [
                'form' => $scheduleForm->createView()
            ]);
        }

        return $this->redirectToRoute('site_schedule_step_1');
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
    public function getZipCodes(Request $request)
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
}