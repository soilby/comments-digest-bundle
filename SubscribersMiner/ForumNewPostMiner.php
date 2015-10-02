<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 22.7.15
 * Time: 2.17
 */

namespace Soil\CommentsDigestBundle\SubscribersMiner;

use Buzz\Browser;
use Buzz\Client\Curl;
use Buzz\Message\Request;
use Buzz\Message\Response;
use EasyRdf\Literal;
use EasyRdf\Resource;
use Sensio\Bundle\BuzzBundle\SensioBuzzBundle;
use Soil\CommentsDigestBundle\Entity\CommentBrief;
use Soil\CommentsDigestBundle\Entity\ForumTopicBrief;
use Soil\CommentsDigestBundle\Service\BriefPropertySetter;
use Soil\CommentsDigestBundle\Service\SubscribersMiner;
use Soil\DiscoverBundle\Entity\Generic;
use Zend\Http\Client;

class ForumNewPostMiner extends RDFMinerAbstract {


    /**
     * @var Curl
     */
    protected $httpClient;

    public function mine(\DateTime $fromDate, $period)  {
//        $fromDate = (new Literal\DateTime($fromDate))->dumpValue('text');


        $query = <<<QUERY

select  ?subscriber ?subscriptionPeriod

where {
    ?subscriber tal:subscriptionApplied ?subscription .

    ?subscription tal:subscriptionType tal:SubscriptionForumPosts .
    ?subscription tal:subscriptionPeriod ?subscriptionPeriod .

}

QUERY;

//        echo $query;
//
//        $result = $this->endpoint->query($query);
//
//        $subscriptions = [];
//        foreach ($result as $element) {
//            $subscriptions[] = [
//                'user_uri' => $element->subscriber->getUri(),
//                'period' => $element->subscriptionPeriod->getValue()
//            ];
//        }

//        var_dump($subscriptions);


        $response = $this->httpClient->submit('http://www.talaka.by/forum/extensions/talaka_integration/get_new_topics.php', [
            'from' => $fromDate->getTimestamp()
        ], 'GET');

        $data = json_decode($response->getContent(), true);
        foreach ($data as $userURI => $userInfo)    {
//            var_dump($userURI);
//            var_dump($userInfo['userName']);
            $topics = $userInfo['topics'];



            foreach ($topics as $topicURI => $topicName)    {
                $brief = new ForumTopicBrief();
                $brief->setCreationDate($fromDate);
                $brief->setSubscriber($userURI);

                $entity = new Generic();
                $entity->setOrigin($topicURI);
                $entity->setRdfNamespace('talforumtopic');
                $entity->name = $topicName;

                $brief->setEntity($entity);

                yield $brief;
            }


//            foreach ($element as $field => $value) {
//                $this->briefPropertySetter->setBriefProperty($brief, $field, $value);
//            }



        }

    }

    /**
     * @param mixed $httpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }



}