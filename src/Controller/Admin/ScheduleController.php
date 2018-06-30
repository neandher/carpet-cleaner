<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\Customer;
use App\Entity\Schedule;
use App\Event\FlashBagEvents;
use App\Event\ScheduleEvents;
use App\Form\Model\SubmitActionsType;
use App\Form\ScheduleType;
use App\Util\FlashBag;
use App\Util\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ScheduleController
 * @package App\Controller\Admin
 *
 * @Route("/schedule", name="admin_schedule_")
 */
class ScheduleController extends BaseController
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
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * ScheduleController constructor.
     * @param Pagination $pagination
     * @param FlashBag $flashBag
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(Pagination $pagination, FlashBag $flashBag, EventDispatcherInterface $dispatcher)
    {
        $this->pagination = $pagination;
        $this->flashBag = $flashBag;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $pagination = $this->pagination->handle($request, Schedule::class);

        /** @var Schedule[] $schedules */
        $schedules = $this->getDoctrine()->getRepository(Schedule::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($schedules as $schedule) {
            $deleteForms[$schedule->getId()] = $this->createDeleteForm($schedule)->createView();
        }

        $customers = $this->getDoctrine()->getRepository(Customer::class)->queryLatestForm()->getQuery()->getResult();

        return $this->render('admin/schedule/index.html.twig', [
            'schedules' => $schedules,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms,
            'customers' => $customers
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Schedule $schedule
     * @return Response
     */
    public function editAction(Schedule $schedule, Request $request)
    {
        $pagination = $this->pagination->handle($request, Schedule::class);

        $form = $this->createForm(ScheduleType::class, $schedule);

        $form
            ->add('send_email', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Send Client Email',
                'choices' => [
                    'Yes' => 'yes',
                    'No' => 'no'
                ]
            ])
            ->add('save_and_confirm', SubmitType::class, [
                'label' => 'Save and Confirm'
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->get('save_and_confirm')->isClicked()) {
                $schedule->setState(Schedule::STATE_CONFIRMED);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($schedule);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            if ($form->get('save_and_confirm')->isClicked()) {
                if ($form->get('send_email')->getData() == 'yes') {
                    $this->dispatcher->dispatch(ScheduleEvents::SCHEDULE_UPDATE_CONFIRM, new GenericEvent($schedule));
                }
            }

            return $this->redirectToRoute('admin_schedule_index', $pagination->getRouteParams());
        }

        return $this->render('admin/schedule/edit.html.twig', [
            'schedule' => $schedule,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Schedule $schedule
     * @return Response
     */
    public function deletAction(Request $request, Schedule $schedule)
    {
        $pagination = $this->pagination->handle($request, Schedule::class);

        $form = $this->createDeleteForm($schedule);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($schedule);
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

        return $this->redirectToRoute('admin_schedule_index', $pagination->getRouteParams());
    }

    /**
     * @param Schedule $schedule
     * @return FormInterface
     */
    private function createDeleteForm(Schedule $schedule)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_schedule_delete', ['id' => $schedule->getId()]))
            ->setMethod('DELETE')
            ->setData($schedule)
            ->getForm();
    }

    /**
     * @Route("/items", name="items")
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function getItems(Request $request)
    {
        $data = [];

        if ($request->isXmlHttpRequest()) {

            $scheduleId = $request->query->get('id', null);

            if ($scheduleId) {

                $encoder = new JsonEncoder();
                $normalizer = new ObjectNormalizer();
                $normalizer->setCircularReferenceHandler(function ($object) {
                    return $object->getId();
                });

                $serializer = new Serializer([$normalizer], [$encoder]);

                $data = $serializer->normalize(
                    $this->getDoctrine()->getRepository(Schedule::class)->findItemsById($scheduleId),
                    'json',
                    ['groups' => ['scheduleItems']]
                );

                $status = 200;
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
