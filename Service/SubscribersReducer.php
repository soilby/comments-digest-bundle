<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 0.17
 */

namespace Soil\CommentsDigestBundle\Service;



class SubscribersReducer {

    protected $reducers = [];

    public function reduce($subscriptions)  {

        foreach ($this->reducers as $reducer)   {
            $subscriptions = $reducer->reduce($subscriptions);
        }

        return $subscriptions;

    }

    public function addReducer($reducer)    {
        $this->reducers[] = $reducer;
    }
} 