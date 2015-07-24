<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 22.42
 */

namespace Soil\CommentsDigestBundle\SubscribersReducer;


use Soil\CommentsDigestBundle\Entity\CommentBrief;

class UnsubscribedReducer {

    protected $unsubscribed = [

    ];

    public function reduce(CommentBrief $commentBrief, $subscriptions)    {

        foreach ($subscriptions as $case => $userList)  {
            foreach ($userList as &$user)    {
                if (!array_key_exists($user, $this->unsubscribed))    {
                    unset($user);
                }
            }
        }

        return $subscriptions;

    }
} 