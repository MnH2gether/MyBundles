<?php

namespace TBStatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use TBStatBundle\Tools\TBTelemetryClientDailyStat;
use TBStatBundle\Entity\MySQL\TBTelemetryStat;


class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        
        $TBTelemetryDailyStat = new TBTelemetryClientDailyStat($this->container,'things_board_telemetry_pgsql');
//        $TBTelemetryCli->addEntity('1e7d500d0b441c0b6a7134646a8fbab', 'doubleval', array( intval(time()/60/60/24)-1 ) );
//        $TBTelemetryCli->addEntity('1e7d500d0b441c0b6a7134646a8fbab', 'doubleval', array( intval(time()/60/60/24)-2, intval(time()/60/60/24)-3 ) );
        $statDay = intval(time()/60/60/24)-1;
        $TBTelemetryDailyStat->addEntity('1e7d500d0b441c0b6a7134646a8fbab', 'doubleval', $statDay );
        $result = $TBTelemetryDailyStat->run();
        
        $eTBTelemetryStat = $this->getDoctrine()->getManager('house_keeper_telemetry_data_mysql');
        
        $repo = $eTBTelemetryStat->getRepository(TBTelemetryStat::class);
        $statRow = $repo->findBy(array('day_ts'=>$statDay));
        if ( count($statRow) > 0)
            $statRow = $statRow[0];
         else $statRow = new TBTelemetryStat();
                
        if ( count($result) > 0 ) {
            $statRow->setDayTs( $result[0]['epoch_day'] )
                    ->setTbId($result[0]['entity_id'])
                    ->setTbName($result[0]['entity_type'])
                    ->setValueName($result[0]['field_name'])
                    ->setValue($result[0]['field_value']);

            $eTBTelemetryStat->persist($statRow);
            $eTBTelemetryStat->flush();
        }

        $repoRes= $repo->findAll();
        
        print ( implode("</br>\r\n",$repoRes)); 
        
        return $this->render('TBStatBundle:Default:index.html.twig');
    }
}
