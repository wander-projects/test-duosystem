<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Activity;
use AppBundle\Form\ActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityController extends Controller
{
    /**
     * @Route("/", name="activity_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine();

        $activities = $em->getRepository(Activity::class)->findAll();

        return $this->render('activity/index.html.twig', array(
            'activities' => $activities,
        ));
    }

    /**
     * @Route("/new", options={"expose"=true}, name="activity_new")
     * @Method({"GET"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $activity = new Activity();

        $form = $this->addCreateForm($activity);

        return $this->render('activity/new.html.twig', array(
            'activity' => $activity,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/new", name="activity_add")
     * @Method({"POST"})
     */
    public function addAction(Request $request)
    {
        $activity = new Activity();
        $form = $this->addCreateForm($activity);
        $form->handleRequest($request);

        dump($form->getData());die();

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($activity);
            $em->flush();

            return new JsonResponse(array('message' => 'Success!'), 200);
        }

        $response = new JsonResponse(
            array(
                'message' => 'Error',
                'form' => $this->render('activity/form.html.twig',
                    array(
                        'activity' => $activity,
                        'form' => $form->createView(),
                    ))), 400);

        return $response;
    }

    /**
     * @Route("/{id}/edit", options={"expose"=true}, name="activity_edit")
     * @Method({"GET"})
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine();
        $activity = $em->getRepository(Activity::class)->find($id);

        if (!$activity) {
            throw $this->createNotFoundException('Unable to find owner entity.');
        }

        $form = $this->editCreateForm($activity, $id);

        return $this->render('activity/edit.html.twig', array(
            'activity' => $activity,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/{id}/edit", name="activity_edit_ajax")
     * @Method({"GET", "PUT"})
     */
    public function editAjaxAction(Request $request, $id)
    {
        $em = $this->getDoctrine();
        $activity = $em->getRepository(Activity::class)->find($id);

        if (!$activity) {
            throw $this->createNotFoundException('Unable to find owner entity.');
        }

        $form = $this->editCreateForm($activity, $id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return new JsonResponse(array('message' => 'Success!'), 200);
        }

        $response = new JsonResponse(
            array(
                'message' => 'Error',
                'form' => $this->render('activity/edit.html.twig',
                    array(
                        'activity' => $activity,
                        'form' => $form->createView(),
                    ))), 400);

        return $response;
    }

    /**
     * @param Activity $activity
     * @return \Symfony\Component\Form\Form
     */
    private function addCreateForm(Activity $activity)
    {
        $form = $this->createForm(ActivityType::class, $activity,
            array(
                'action' => $this->generateUrl('activity_add'),
                'method' => 'POST',
            ));

        return $form;
    }

    private function editCreateForm(Activity $activity, $id)
    {
        $form = $this->createForm(ActivityType::class, $activity,
            array(
                'action' => $this->generateUrl('activity_edit_ajax', ['id' => $id]),
                'method' => 'PUT',
            ));

        return $form;
    }

    /**
     * @Route("/search", name="search")
     */
    public function searchAction(Request $request)
    {
        $find = $request->query->get('find');
        $findStatus = $request->query->get('findStatus');

        if ($find == 2 and $findStatus == 0) {
            $activities = $this->getDoctrine()
                ->getRepository(Activity::class)->findAll();
        } else {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery('
                SELECT a
                FROM AppBundle:Activity a
                WHERE (a.situation LIKE :find OR a.status LIKE :findStatus)
            ')->setParameter('find', '%'.$find.'%')
            ->setParameter('findStatus', '%'.$findStatus.'%');
            $activities = $query->getResult();
        }

        if (!$activities) {
            $this->addFlash('notice',
                'Nenhum resultado encontrado para pesquisa: '.$find);
            return $this->redirectToRoute('activity_index');
        }

        return $this->render('activity/index.html.twig',
            array('activities' => $activities, 'find' => $find, 'findStatus' => $findStatus)
        );
    }
}
