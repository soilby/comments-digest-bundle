<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 22.7.15
 * Time: 12.16
 */

namespace Soil\CommentsDigestBundle\Entity;


use Soil\CommentsDigestBundle\Entity\CommentBrief;

class CommentBriefIndex {

    protected $checkSumIndex = [];
    protected $checkSumMinerIndex = [];

    protected $bySubscriberIndex = [];


    public function add($minerKey, CommentBrief $commentBrief) {
        $checkSum = $commentBrief->getCheckSum();
        if (!array_key_exists($checkSum, $this->checkSumIndex))  {
            $this->checkSumIndex[$checkSum] = $commentBrief;
            $this->checkSumMinerIndex[$checkSum] = $minerKey;

            $subscriber = $commentBrief->getSubscriber();
            if (!array_key_exists($subscriber, $this->bySubscriberIndex)) $this->bySubscriberIndex[$subscriber] = [$minerKey => []];
            if (!array_key_exists($minerKey, $this->bySubscriberIndex[$subscriber])) $this->bySubscriberIndex[$subscriber][$minerKey] = [];

            $this->bySubscriberIndex[$subscriber][$minerKey][] = $commentBrief;
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







} 