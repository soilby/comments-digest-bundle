<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 0.17
 */

namespace Soil\CommentsDigestBundle\Service;



use EasyRdf\Literal;
use EasyRdf\Resource;
use Monolog\Logger;
use Soil\AckService\Service\Ack;
use Soil\CommentsDigestBundle\Entity\CommentBrief;
use Soil\CommentsDigestBundle\Entity\CommentBriefIndex;
use Soil\DiscoverBundle\Service\Resolver;

class SubscribersMiner {

    protected $miners = [];

    protected $minerSwitch = [];

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Ack
     */
    protected $ackService;

    /**
     * @param Ack $ackService
     */
    public function setAckService($ackService)
    {
        $this->ackService = $ackService;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }






    /**
     * @param int $period
     * @return CommentBriefIndex
     */
    public function mine($period)  {

        $fromDate = new \DateTime('-' . $period . ' hour');
        $this->logger->addInfo('Gather comments since ' . $fromDate->format('Y-m-d H:i:s'));


        $subscriptions = new CommentBriefIndex($this->ackService);
        $subscriptions->setLogger($this->logger);

        foreach ($this->miners as $miner)   {
            $minerClass = get_class($miner);

            if (!$this->isMinerEnabled($minerClass)) continue;

            foreach ($miner->mine($fromDate, $period) as $element)   {
                $subscriptions->add($minerClass, $element);
            }
        }

        return $subscriptions;


    }

    public function disableAllMiners()  {
        foreach ($this->miners as $miner)   {
            $minerClass = get_class($miner);
            $this->enableMiner($minerClass, false);
        }
    }

    public function enableMiner($minerClass, $enable = true)    {
        $this->minerSwitch[$minerClass] = $enable;
    }

    public function isMinerEnabled($minerClass) {
        if (!array_key_exists($minerClass, $this->minerSwitch))  {
            return true;
        }
        else {
            return $this->minerSwitch[$minerClass];
        }
    }

    public function addMiner($miner)    {
        $this->miners[] = $miner;
    }

}
