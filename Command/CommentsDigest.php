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
            ->addArgument(
                'case',
                InputArgument::OPTIONAL,
                'Aggregation case, may be "my", "important" or "all"', 'all'
            )
            ->addArgument(
                'period',
                InputArgument::OPTIONAL,
                'Comments period', 4
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $case = $input->getArgument('case');

        switch ($case)  {
            case 'my':
                $this->subscribersMiner->disableAllMiners();
                $this->subscribersMiner->enableMiner('Soil\CommentsDigestBundle\SubscribersMiner\AnswersMiner');
                $this->subscribersMiner->enableMiner('Soil\CommentsDigestBundle\SubscribersMiner\EntityAuthorsMiner');

                break;

            case 'important':
                $this->subscribersMiner->disableAllMiners();
                $this->subscribersMiner->enableMiner('Soil\CommentsDigestBundle\SubscribersMiner\ImportantForMeEntitiesMiner');

                break;

            case 'all':
            default:
                $case = 'all';
        }



        $this->logger->addInfo('Aggregation case ' . $case);

        $subscribersIndex = $this->subscribersMiner->mine($input->getArgument('period'));

//        $forUser = $subscribersIndex->getForSubscriber("http://www.talaka.by/user/8785");

        $byUserIndex = $subscribersIndex->getBySubscriberIndex();
//        $userURI = 'http://www.talaka.by/user/12626';
//        $userIndex = $byUserIndex[$userURI];
//        var_dump($userIndex);
//
//        $this->notifyService->notify('CommentsDigestNotification', $userURI, [
//            'groupedComments' => $userIndex
//        ]);
//        exit();


        foreach ($byUserIndex as $userURI => $groupedComments)  {

            try {
                $this->notifyService->notify('CommentsDigestNotification', $userURI, [
                    'groupedComments' => $groupedComments
                ]);
                sleep(1);
            }
            catch(\Exception $e)    {
                echo 'Problem with notification for ' . $userURI;
                var_dump((string) $e);
            }
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