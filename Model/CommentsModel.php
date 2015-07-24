<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 19.7.15
 * Time: 22.48
 */

namespace Soil\CommentsDigestBundle\Model;



use EasyRdf\Literal;

class CommentsModel {


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

    public function getCommentsForIdeaAuthors(\DateTime $fromDate) {




    }

    public function getComments(\DateTime $startDate, \DateTime $endDate)   {


        $startDate = (new Literal\DateTime($startDate))->dumpValue('text');
        $endDate = (new Literal\DateTime($endDate))->dumpValue('text');

        $query = <<<QUERY

    select ?comment ?author ?creationDate ?entity ?entityAuthor ?parent ?parentAuthor

    where {

     ?comment    a tal:Comment .
     ?comment tal:author ?author .

     ?comment tal:relatedObject ?entity .

     OPTIONAL {
        ?comment tal:parent ?parent .
        ?parent tal:author ?parentAuthor .
     } .
     OPTIONAL {
        ?entity tal:author ?entityAuthor .
     }.

     ?comment tal:creationDate ?creationDate .

    FILTER (?creationDate > $startDate && ?creationDate < $endDate) .

    }

QUERY;

        echo $query;


        $result = $this->endpoint->query($query);


        return $result;

    }
} 