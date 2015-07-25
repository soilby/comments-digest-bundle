<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 22.7.15
 * Time: 12.16
 */

namespace Soil\CommentsDigestBundle\Entity;


use Monolog\Logger;
use Soil\AckService\Service\Ack;
use Soil\CommentsDigestBundle\Entity\CommentBrief;

class CommentBriefIndex {


    /**
     * @var Ack
     */
    protected $ackService;

    protected $checkSumIndex = [];
    protected $checkSumMinerIndex = [];

    protected $bySubscriberIndex = [];

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct($ack)   {
        $this->ackService = $ack;
    }

    public function add($minerKey, CommentBrief $commentBrief) {
        $checkSum = $commentBrief->getCheckSum();

        if (!array_key_exists($checkSum, $this->checkSumIndex))  {
            $this->checkSumIndex[$checkSum] = $commentBrief;
            $this->checkSumMinerIndex[$checkSum] = $minerKey;

            $test = $this->ackService->test('comment_digest', $checkSum);
            $this->logger->addAlert('Checksum: ' . $checkSum);
            if (!$test) {
                $subscriber = $commentBrief->getSubscriber();
                if (!array_key_exists($subscriber, $this->bySubscriberIndex)) $this->bySubscriberIndex[$subscriber] = [$minerKey => []];
                if (!array_key_exists($minerKey, $this->bySubscriberIndex[$subscriber])) $this->bySubscriberIndex[$subscriber][$minerKey] = [];

                $this->bySubscriberIndex[$subscriber][$minerKey][] = $commentBrief;
            }
            else    {
                $this->logger->addAlert('Comment already notified for user');
                $this->logger->addAlert((is_object($commentBrief->getComment()) ? $commentBrief->getComment()->getOrigin() : $commentBrief->getComment()) );
                $this->logger->addAlert($commentBrief->getSubscriber());
            }
        }
    }

    public function getForSubscriber($subscriber)   {
        return array_key_exists($subscriber, $this->bySubscriberIndex) ? $this->bySubscriberIndex[$subscriber] : [];
    }


    /**
     * @return array
     */
    public function getCheckSumIndex()
    {
        return $this->checkSumIndex;
    }

    /**
     * @return array
     */
    public function getBySubscriberIndex()
    {
        return $this->bySubscriberIndex;
    }

    /**
     * @param mixed $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }







} 