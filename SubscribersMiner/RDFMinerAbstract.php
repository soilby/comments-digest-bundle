<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 22.7.15
 * Time: 2.19
 */

namespace Soil\CommentsDigestBundle\SubscribersMiner;



use EasyRdf\Sparql\Client;
use Soil\CommentsDigestBundle\Service\BriefPropertySetter;

abstract class RDFMinerAbstract {


    /**
     * @var BriefPropertySetter
     */
    protected $briefPropertySetter;

    public function __construct($briefPropertySetter)   {
        $this->briefPropertySetter = $briefPropertySetter;
    }

    /**
     * @var Client
     */
    protected $endpoint;

    /**
     * @param Client $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }



} 