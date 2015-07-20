<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 0.17
 */

namespace Soil\CommentsDigestBundle\Service;



use Soil\CommentsDigestBundle\Entity\CommentBrief;

class CommentPromoter {

    protected $miners = [];

    public function promote(CommentBrief $commentBrief)  {

        $subscriptions = [];

        foreach ($this->miners as $miner)   {

            $part = $miner->mine($commentBrief);

            if ($part) {
                $subscriptions[get_class($miner)] = $part;
            }
        }

        var_dump($subscriptions);


    }

    public function addMiner($miner)    {
        $this->miners[] = $miner;
    }
} 