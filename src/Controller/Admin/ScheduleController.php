<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\Customer;
use App\Entity\Schedule;
use App\Event\FlashBagEvents;
use App\Form\Model\SubmitActionsType;
use App\Form\ScheduleType;
use App\Util\FlashBag;
use App\Util\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * ScheduleController constructor.
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

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($schedule);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

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
}
