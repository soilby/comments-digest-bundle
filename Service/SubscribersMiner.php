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
     * @param \DateTime $date
     * @return CommentBriefIndex
     */
    public function mine(\DateTime $date)  {

        $subscriptions = new CommentBriefIndex($this->ackService);
        $subscriptions->setLogger($this->logger);

        foreach ($this->miners as $miner)   {

            foreach ($miner->mine($date) as $element)   {
                $subscriptions->add(get_class($miner), $element);
            }
        }

        return $subscriptions;


    }

    public function addMiner($miner)    {
        $this->miners[] = $miner;
    }

}
