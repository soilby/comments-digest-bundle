<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 22.42
 */

namespace Soil\CommentsDigestBundle\SubscribersReducer;


use Soil\CommentsDigestBundle\Entity\CommentBrief;

class UniqueReducer {


    /**
     * @param CommentBrief[] $subscriptions
     *
     * @return array
     */
    public function reduce($subscriptions)    {
        $inverted = [];
        $filtered = [];

        foreach ($subscriptions as $case => $brief)  {
//            foreach ($userList as $user)    {


//                $user->

//            }
        }


        $origin = [];
        foreach ($inverted as $user => $case)   {
            if (!array_key_exists($case, $origin))   {
                $origin[$case] = [];
            }
            $origin[$case][] = $user;
        }

        return $origin;
    }
} 