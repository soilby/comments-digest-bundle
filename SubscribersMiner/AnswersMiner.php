<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 0.34
 */

namespace Soil\CommentsDigestBundle\SubscribersMiner;



use Soil\CommentsDigestBundle\Entity\CommentBrief;

class AnswersMiner {

    public function mine(CommentBrief $commentBrief)    {
        if ($commentBrief->getParentAuthor())   {
            return [$commentBrief->getParentAuthor()];
        }
        else    {
            return [];
        }
    }
} 