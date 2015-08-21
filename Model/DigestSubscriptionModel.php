<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 17.8.15
 * Time: 16.12
 */

namespace Soil\CommentsDigestBundle\Model;


use Doctrine\ORM\Query;
use EasyRdf\RdfNamespace;

class DigestSubscriptionModel {

    /**
     * @var \EasyRdf\Sparql\Client
     */
    protected $endpoint;

    /**
     * @var array
     */
    protected $namespaces;

    public function __construct($endpoint, $namespaces)    {
        $this->endpoint = $endpoint;
        $this->namespaces = $namespaces;

        foreach ($namespaces as $namespace => $uri) {
            \EasyRdf\RdfNamespace::set($namespace, $uri);
        }
    }

    public function isSubscribed($agentURI) {
        $query = <<<QUERY
PREFIX tal:<http://semantic.talaka.by/ns/talaka.owl#>
PREFIX xsd:<http://www.w3.org/2001/XMLSchema#>


    SELECT ?type ?period
    WHERE {
        <$agentURI> tal:subscriptionApplied ?subscription .
        ?subscription tal:subscriptionType ?type .
        ?subscription tal:subscriptionPeriod ?period .
    }
QUERY;

        $subscriptions = [];
        try {
            $result = $this->endpoint->query($query);

            foreach ($result as $resource)  {
                $subscriptions[] = [
                    'uri' => $resource->type->getURI(),
                    'period' => $resource->period->getValue()
                ];
            }

        }
        catch (\EasyRdf\Http\Exception $e)  {
            echo $e->getBody();
        }

        return $subscriptions;
    }



    public function subscribeAgent($agentURI, array $subscriptions)  {

        $graphIRI = 'tal:SubscriptionGraph';
        $graphURI = RdfNamespace::expand($graphIRI);

        $query = <<<QUERY
DELETE WHERE {
    GRAPH <$graphURI>   {
        <$agentURI> tal:subscriptionApplied ?nodes
    }
};

QUERY;

        $insertQuery = '';
        $index = 0;
        foreach ($subscriptions as $subscription)   {
            $bnode = '_:b' . ++$index;

            $subscriptionURI = $subscription['uri'];
            $subscriptionPeriod = $subscription['period'];

            $tripleBlock = <<<TRIPLEBLOCK
            <$agentURI> tal:subscriptionApplied $bnode .
            $bnode tal:subscriptionType <$subscriptionURI> .
            $bnode tal:subscriptionPeriod $subscriptionPeriod .
TRIPLEBLOCK;

            $insertQuery .= $tripleBlock;
        }
        $query .= <<<INSERT
INSERT DATA {
    GRAPH <$graphURI> {
        $insertQuery
    }
};
INSERT;


        echo $query;

        try {
            $this->endpoint->update($query);
        }
        catch (\EasyRdf\Http\Exception $e)  {
            echo $e->getBody();
        }

    }

    public function subscribe($agentURI, $digestSubscriptionURI, $period)  {

        $index = 1;

        $query = <<<INSERT
            <$agentURI> tal:subscriptionApplied _:b$index .
            _:b$index tal:subscriptionType <$digestSubscriptionURI> .
            _:b$index tal:subscriptionPeriod $period .
INSERT;

        $graphIRI = 'tal:SubscriptionGraph';
        $graphURI = RdfNamespace::expand($graphIRI);

        echo $query;

        try {
            $this->endpoint->insert($query, $graphURI);
        }
        catch (\EasyRdf\Http\Exception $e)  {
            echo $e->getBody();
        }
    }


} 