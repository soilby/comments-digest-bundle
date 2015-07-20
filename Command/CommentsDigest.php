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
use Soil\CommentsDigestBundle\Service\CommentPromoter;
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
     * @var CommentPromoter
     */
    protected $commentPromoter;

    public function __construct($commentsModel, $commentPromoter)   {

        $this->commentsModel = $commentsModel;
        $this->commentPromoter = $commentPromoter;

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
        $yesterday = new \DateTime('yesterday');
        $tomorrow = new \DateTime('tomorrow');
        $comments = $this->commentsModel->getComments($yesterday, $tomorrow);


        $reflection = new \ReflectionClass(CommentBrief::class);
//var_dump(count($comments));exit();


        foreach ($comments as $commentInfo) {
            $brief = new CommentBrief();

            foreach ($commentInfo as $field => $value)  {
                if ($reflection->hasProperty($field))   {
                    $property = $reflection->getProperty($field);
                    $property->setAccessible(true);

                    if ($value instanceof Resource) {
                        $value = $value->getUri();
                    }
                    elseif ($value instanceof Literal)  {
                        $value = $value->getValue();
                    }

                    $property->setValue($brief, $value);

                }
            }

            echo $brief->getComment();
            $this->commentPromoter->promote($brief);
        }

    }



    public function setLogger($logger)  {
        $this->logger = $logger;
    }


    public function setConfigInfo($name, $value)    {
        $this->configInfo[$name] = $value;
    }

} 