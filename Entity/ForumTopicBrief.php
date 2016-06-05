<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 0.19
 */

namespace Soil\CommentsDigestBundle\Entity;

use Soil\CommentsDigestBundle\Annotation\Entity;

class ForumTopicBrief extends CommentBrief {


    public function getCheckSum()   {
        return $this->subscriber . '-' . $this->entity->getOrigin();
    }


}