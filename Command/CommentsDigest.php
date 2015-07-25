<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 18.7.15
 * Time: 16.01
 */

namespace Soil\CommentsDigestBundle\Command;


use EasyRdf\Literal;
use EasyRdf\Resource;
use Soil\CommentsDigestBundle\Entity\CommentBrief;
use Soil\CommentsDigestBundle\Model\CommentsModel;
use Soil\CommentsDigestBundle\Service\SubscribersReducer;
use Soil\CommentsDigestBundle\Service\CommentPromoter;
use Soil\CommentsDigestBundle\Service\SubscribersMiner;
use Soil\NotificationBundle\Service\Notification;
use Soil\RDFProcessorBundle\Service\EndpointClient;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommentsDigest extends Command    {


    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $configInfo = [];


    /**
     * @var CommentsModel
     */
    protected $commentsModel;

    /**
     * @var SubscribersMiner
     */
    protected $subscribersMiner;


    /**
     * @var Notification
     */
    protected $notifyService;

    /**
     * @var SubscribersReducer
     */
    protected $subscribersReducer;

    public function __construct($commentsModel, $subscribersMiner, $subscribersReducer, $notifyService)   {

        $this->commentsModel = $commentsModel;
        $this->subscribersMiner = $subscribersMiner;
        $this->subscribersReducer = $subscribersReducer;
        $this->notifyService = $notifyService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('comments:digest')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fromDate = new \DateTime('today 22:00');



        $subscribersIndex = $this->subscribersMiner->mine($fromDate);

//        $forUser = $subscribersIndex->getForSubscriber("http://www.talaka.by/user/8785");

        $byUserIndex = $subscribersIndex->getBySubscriberIndex();

        foreach ($byUserIndex as $userURI => $groupedComments)  {

            $processed = $this->notifyService->notify('CommentsDigestNotification', $userURI, [
                'groupedComments' => $groupedComments
            ]);

        }


//        $subscribersList = $this->subscribersReducer->reduce($subscribersList);

    }



    public function setLogger($logger)  {
        $this->logger = $logger;
    }


    public function setConfigInfo($name, $value)    {
        $this->configInfo[$name] = $value;
    }

} 