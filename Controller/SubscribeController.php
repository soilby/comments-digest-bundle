<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 17.8.15
 * Time: 16.07
 */

namespace Soil\CommentsDigestBundle\Controller;


use Soil\CommentsDigestBundle\Model\DigestSubscriptionModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Zend\View\Helper\Json;

class SubscribeController {

    /**
     * @var DigestSubscriptionModel
     */
    protected $digestSubscriptionModel;

    public function __construct($digestSubscriptionModel)   {
        $this->digestSubscriptionModel = $digestSubscriptionModel;
    }

    public function getStateAction($agentURI)   {
        try {
            $subscriptions = $this->digestSubscriptionModel->isSubscribed($agentURI);

            return new JsonResponse([
                'agent' => $agentURI,
                'subscriptions' => $subscriptions
            ]);
        }
        catch(\Exception $e)    {
            return new JsonResponse([
                'agent' => $agentURI,
                'subscriptions' => []
            ], 500);
        }
    }

    public function saveStateAction($agentURI, Request $request) {
        $content = $request->getContent();
        $data = json_decode($content, true);
        if (!$data) throw new \Exception("Request malformed");

        $this->digestSubscriptionModel->subscribeAgent($agentURI, $data);

        return new JsonResponse(['ok']);
    }

} 