<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 22.7.15
 * Time: 2.17
 */

namespace Soil\CommentsDigestBundle\SubscribersMiner;

use EasyRdf\Literal;
use EasyRdf\Resource;
use Soil\CommentsDigestBundle\Entity\CommentBrief;
use Soil\CommentsDigestBundle\Service\BriefPropertySetter;
use Soil\CommentsDigestBundle\Service\SubscribersMiner;

class EntityAuthorsMiner extends RDFMinerAbstract {



    public function mine(\DateTime $fromDate, $period)  {
        $fromDate = (new Literal\DateTime($fromDate))->dumpValue('text');

        $periodFilter = $this->getFilterForPeriod($period);

        $query = <<<QUERY

select ?comment ?creationDate ?author ?entity ?subscriber

where {
     ?comment    a tal:Comment .
     
     ?comment tal:author ?author .

     ?comment tal:relatedObject ?entity .

     ?entity tal:author ?subscriber .

     ?comment tal:creationDate ?creationDate .

     FILTER (?creationDate > $fromDate ) .

     OPTIONAL {
        ?subscriber tal:subscriptionApplied ?subscription .
        ?subscription tal:subscriptionPeriod ?period .
        ?subscription tal:subscriptionType ?subscriptionType .

     }

     FILTER (!bound(?subscription) || ?subscriptionType = tal:SubscriptionMyEntities) .
$periodFilter
    }
QUERY;
        //FILTER (?subscriber = <http://www.talaka.by/user/132> ) .

        echo $query;
        $result = $this->endpoint->query($query);

        foreach ($result as $element)   {
            $brief = new CommentBrief();

            foreach ($element as $field => $value) {
                $this->briefPropertySetter->setBriefProperty($brief, $field, $value);
            }

            yield $brief;
        }
    }

}