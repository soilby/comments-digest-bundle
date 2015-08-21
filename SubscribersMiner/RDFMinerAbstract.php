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

    protected $defaultSubscriptionPeriod;

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

    /**
     * @return mixed
     */
    public function getDefaultSubscriptionPeriod()
    {
        return $this->defaultSubscriptionPeriod;
    }

    /**
     * @param mixed $defaultSubscriptionPeriod
     */
    public function setDefaultSubscriptionPeriod($defaultSubscriptionPeriod)
    {
        $this->defaultSubscriptionPeriod = $defaultSubscriptionPeriod;
    }


    public function getFilterForPeriod($period) {
        if ($this->getDefaultSubscriptionPeriod() == $period) {
            return <<<PERIOD

FILTER (!bound(?subscription) || ?period = $period) .

PERIOD;
        }
        else    {
            return <<<PERIOD
FILTER (?period = $period) .
PERIOD;
        }
    }


} 