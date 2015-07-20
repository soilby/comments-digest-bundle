<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 0.34
 */

namespace Soil\CommentsDigestBundle\SubscribersMiner;



use Soil\CommentsDigestBundle\Entity\CommentBrief;

class EntityOwnersMiner {

    public function mine(CommentBrief $commentBrief)    {
        return [$commentBrief->getEntityAuthor()];
    }
} 