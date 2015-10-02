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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CommentsAutoDigest extends Command    {


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
            ->setName('comments:auto-digest')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->logger->addInfo('Config info:');
        foreach ($this->configInfo as $name => $value)  {
            $this->logger->addInfo($name . ': ' . $value);
        }

        $availablePeriods = [
//            24
            4, 8, 24, 168
        ];

        foreach ($availablePeriods as $period)  {
            if ($period === 168)    {
                if (date('D') !== 'Tue') continue;
                if (date('H') < 17) continue;
            }
            $subscribersIndex = $this->subscribersMiner->mine($period);

            $this->sentNotifications($subscribersIndex->getBySubscriberIndex());
        }



    }

    protected function sentNotifications($byUserIndex) {

        foreach ($byUserIndex as $userURI => $groupedComments)  {

            try {
                $this->notifyService->notify('CommentsDigestNotification', $userURI, [
                    'groupedComments' => $groupedComments
                ]);
                sleep(1);
                exit("FIN");
            }
            catch(\Exception $e)    {
                echo 'Problem with notification for ' . $userURI;
                var_dump((string) $e);
                EXIT("EXCEPTION");
            }
        }
    }



    public function setLogger($logger)  {
        $this->logger = $logger;
    }


    public function setConfigInfo($name, $value)    {
        $this->configInfo[$name] = $value;
    }

} 