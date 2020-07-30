<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\WeatherType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        $form = $this->createForm(WeatherType::class, null, [
            'action' => $this->generateUrl('load-weather'),
            'method' => 'POST'
        ]);
        return $this->render('homepage/index.twig', [
            'form' => $form->createView()
        ]);
    }
}