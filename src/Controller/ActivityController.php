<?php
/**
 * Created by PhpStorm.
 * User: wanderlei
 * Date: 31/01/18
 * Time: 17:02
 */

namespace App\Controller;

use App\Entity\Activity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ActivityController extends Controller
{
    /**
     * @Route("/activity", name="activity")
     * @Method("GET")
     */
    public function index()
    {
        $em = $this->getDoctrine();

        $activities = $em->getRepository(Activity::class)->findAll();

        return $this->render('activity/index.html.twig', array(
            'activities' => $activities,
        ));
    }
}