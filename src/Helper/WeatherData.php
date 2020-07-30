<?php
declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class WeatherData
 * @package App\Helper
 */
class WeatherData
{
    /**
     * Openweathermap API url
     */
    const OPENWEATHERMAP_API_URL = 'http://api.openweathermap.org/data/2.5/forecast?';
    /**
     * @var array
     */
    private $queryPrams = [];

    /**
     * @var string
     */
    private $speedUnit = "";

    /**
     * WeatherData constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->queryPrams['appid'] = $container->getParameter('appid');
    }

    /**
     * @param string $city
     * @param string $unit
     * @return array|null
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Exception
     */
    public function getWeatherData()
    {
        $this->validateQueryParams();
        $httpRequest = HttpClient::create();
        $response = $httpRequest->request('GET', self::OPENWEATHERMAP_API_URL . http_build_query($this->queryPrams));
        return $this->prepareData($response->toArray(true));
    }

    /**
     * @throws \Exception
     */
    private function validateQueryParams(): void
    {
        if (!key_exists('q',$this->queryPrams)) {
            throw new \Exception('Parameter with key "q" doesn\'t exist in array');
        }
        if (!key_exists('lang',$this->queryPrams)) {
            throw new \Exception('Parameter with key "lang" doesn\'t exist in array');
        }
        if (!key_exists('appid',$this->queryPrams)) {
            throw new \Exception('Parameter with key "appid" doesn\'t exist in array');
        }
        if (!key_exists('units',$this->queryPrams)) {
            throw new \Exception('Parameter with key "units" doesn\'t exist in array');
        }
    }
    /**
     * @param array $response
     * @return array
     */
    public function prepareData(array $response): array
    {
        $weather = [
            'temp' => [
                'current_temp' => current($response['list'])['main']['temp'],
                'temp_unit' => ($this->queryPrams['units'] === 'mph') ? "°F" : "°C",
                'sunrise' => $response['city']['sunrise'],
                'five_days_ave_temp' => $this->calculateAverageOfPassedDays($response['list'])
            ]
        ];
        $response = current($response['list']);
        $weather['wind'] = [
            'speed' => ($this->queryPrams['units'] === "km/h") ? ($response['wind']['speed'] * 3.6) : $response['wind']['speed'],
            'unit' => $this->speedUnit,
            'direction' => $this->convertDegreesToWindDirection($response['wind']['deg']),
            'deg' => $response['wind']['deg']
        ];
        $weather['weather'] = current($response['weather']);

        return $weather;
    }

    /**
     * @param $degrees
     * @return string
     */
    private function convertDegreesToWindDirection(float $degrees): string
    {
        $directions = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N');
        return $directions[round((($degrees %= 360) < 0 ? $degrees + 360 : $degrees) / 45) % 8];
    }

    /**
     * @param array $days
     * @return float|int
     */
    private function calculateAverageOfPassedDays(array $days): float
    {
        $temperatureSum = 0;
        $daysSum = 0;
        foreach ($days as $day) {
            $temperatureSum += $day['main']['temp'];
            $daysSum++;
        }
        return $temperatureSum / $daysSum;
    }

    /**
     * @param string $key
     * @param string $value
     * @return WeatherData
     */
    public function addParam(string $key, string $value): self
    {
        $this->queryPrams[$key] = $value;

        return $this;
    }

    /**
     * @param string $speedUnit
     */
    public function setSpeedUnit(string $speedUnit): self
    {
        $this->speedUnit = $speedUnit;

        return $this;
    }
}