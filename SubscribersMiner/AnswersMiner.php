<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 0.34
 */

namespace Soil\CommentsDigestBundle\SubscribersMiner;

use Soil\CommentsDigestBundle\Entity\CommentBrief;
use EasyRdf\Literal;

class AnswersMiner extends RDFMinerAbstract {

    public function mine(\DateTime $fromDate, $period)  {
        $fromDate = (new Literal\DateTime($fromDate))->dumpValue('text');


        $periodFilter = $this->getFilterForPeriod($period);

        $query = <<<QUERY

select ?comment ?creationDate ?parent ?entity ?subscriber

where {
     ?comment    a tal:Comment .
     ?comment tal:relatedObject ?entity .
     ?comment tal:parent ?parent .
     ?parent tal:author ?subscriber .


     ?comment tal:creationDate ?creationDate .

     FILTER (?creationDate > $fromDate ) .

     OPTIONAL {
        ?subscriber tal:subscriptionApplied ?subscription .
        ?subscription tal:subscriptionPeriod ?period .
        ?subscription tal:subscriptionType ?subscriptionType .

     }

     FILTER (!bound(?subscription) || ?subscriptionType = tal:SubscriptionAnswers) .
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