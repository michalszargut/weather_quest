<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\WeatherType;
use App\Helper\WeatherData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WeatherController
 * @package App\Controller
 */
class WeatherController extends AbstractController
{
    /**
     * @var WeatherData
     */
    private $weatherData;

    /**
     * WeatherController constructor.
     * @param WeatherData $weatherData
     */
    public function __construct(WeatherData $weatherData)
    {
        $this->weatherData = $weatherData;
    }

    /**
     * @Route("/weather/load", name="load-weather")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadWeather(Request $request)
    {
        $form = $this->createForm(WeatherType::class);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $weather = $this->weatherData
                    ->addParam('q', $data['city'])
                    ->addParam('units', ($data['unit'] === 'mph' ? "imperial" : "metric"))
                    ->addParam('lang', $request->getLocale())
                    ->setSpeedUnit($data['unit'])
                    ->getWeatherData();
                return $this->render('weather/load.twig',[
                    'weather' => $weather,
                    'city' => $data['city']
                ]);
            }
        }
        $this->addFlash('error', 'invalid_parameters');
        return $this->redirectToRoute('homepage');
    }
}