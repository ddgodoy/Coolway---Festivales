<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use CoolwayFestivales\BackendBundle\Entity\Weather;
use Doctrine\ORM\EntityRepository;

class WeatherRepository extends EntityRepository
{
    public function setForecastInfo($feast, $lat, $long)
    {
        //$json_string = file_get_contents("http://api.wunderground.com/api/$client_key/forecast10day/lang:SP/q/$lat,$long.json");

        $client_key  = 'ffbe1396bbafa620';
        $json_string = file_get_contents("http://api.wunderground.com/api/$client_key/forecast10day/q/$lat,$long.json");
        $parsed_json = json_decode($json_string);
        $em =  $this->getEntityManager();

        if (isset($parsed_json->{'forecast'}))
        {
            $this->wipeOutWeather($feast->getId());

            $oNow = new \DateTime("now");
            $_10Dias = $parsed_json->{'forecast'}->{'simpleforecast'}->{'forecastday'};

            foreach ($_10Dias as $dia)
            {
                switch ($dia->{'icon'})
                {
                    case 'sunny':
                    case 'clear':
                    case 'mostlysunny':
                    case 'partlysunny':
                        $condicion = 'Soleado';
                        $url_icono = 'http://icons.wxug.com/i/c/k/clear.gif';
                        break;
                    case 'cloudy':
                    case 'partlycloudy':
                    case 'mostlycloudy':
                    case 'chancerain':
                        $condicion = 'Nublado';
                        $url_icono = 'http://icons.wxug.com/i/c/k/cloudy.gif';
                        break;
                    case 'rain':
                    case 'fog':
                    case 'hazy':
                    case 'sleet':
                    case 'chancesleet':
                        $condicion = 'Lluvia';
                        $url_icono = 'http://icons.wxug.com/i/c/k/sleet.gif';
                        break;
                    case 'tstorms':
                    case 'snow':
                    case 'chanceflurries':
                    case 'flurries':
                    case 'chancetstorms':
                    case 'chancesnow':
                        $condicion = 'Tormenta';
                        $url_icono = 'http://icons.wxug.com/i/c/k/tstorms.gif';
                        break;
                    default:
                        $condicion = 'Soleado';
                        $url_icono = 'http://icons.wxug.com/i/c/k/clear.gif';
                }
                $sFecha = $dia->{'date'}->{'year'}.'-'.$dia->{'date'}->{'month'}.'-'.$dia->{'date'}->{'day'};
                $oFecha = new \DateTime($sFecha);
                //
                $obj = new Weather();

                $obj->setFeast        ($feast);
                $obj->setForecastDate ($oFecha);
                $obj->setWeekday      ($dia->{'date'}->{'weekday'});
                $obj->setMinTemp      ($dia->{'low'}->{'celsius'});
                $obj->setMaxTemp      ($dia->{'high'}->{'celsius'});
                $obj->setConditionDay ($condicion);
                $obj->setConditionIcon($url_icono);
                $obj->setHumidity     ($dia->{'avehumidity'});
                $obj->setLoadDate     ($oNow);

                $em->persist($obj);
                $em->flush();
            }
        }
    }
    //
    public function wipeOutWeather($feast_id)
    {
        $q = $this->getEntityManager()->createQuery("DELETE FROM BackendBundle:Weather w WHERE w.feast = $feast_id");
        $q->execute();
    }

} // end class