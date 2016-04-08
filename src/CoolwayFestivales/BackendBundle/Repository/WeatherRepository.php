<?php

namespace CoolwayFestivales\BackendBundle\Repository;

use CoolwayFestivales\BackendBundle\Entity\Weather;
use Doctrine\ORM\EntityRepository;

class WeatherRepository extends EntityRepository
{
    public function setForecastInfo($feast, $lat, $long)
    {
        $client_key  = 'ffbe1396bbafa620';
        $json_string = file_get_contents("http://api.wunderground.com/api/$client_key/forecast10day/lang:SP/q/$lat,$long.json");
        $parsed_json = json_decode($json_string);
        $em =  $this->getEntityManager();

        if (isset($parsed_json->{'forecast'}))
        {
            $this->wipeOutWeather($feast->getId());

            $oNow = new \DateTime("now");
            $_10Dias = $parsed_json->{'forecast'}->{'simpleforecast'}->{'forecastday'};

            foreach ($_10Dias as $dia)
            {
                $sFecha = $dia->{'date'}->{'year'}.'-'.$dia->{'date'}->{'month'}.'-'.$dia->{'date'}->{'day'};
                $oFecha = new \DateTime($sFecha);
                //
                $obj = new Weather();

                $obj->setFeast        ($feast);
                $obj->setForecastDate ($oFecha);
                $obj->setWeekday      ($dia->{'date'}->{'weekday'});
                $obj->setMinTemp      ($dia->{'low'}->{'celsius'});
                $obj->setMaxTemp      ($dia->{'high'}->{'celsius'});
                $obj->setConditionDay ($dia->{'conditions'});
                $obj->setConditionIcon($dia->{'icon_url'});
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