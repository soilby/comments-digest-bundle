<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 0.19
 */

namespace Soil\CommentsDigestBundle\Entity;

use Soil\CommentsDigestBundle\Annotation\Entity;

class CommentBrief {

    /**
     * @var
     * @Entity("Soil\DiscoverBundle\Entity\Comment")
     */
    protected $comment;

    protected $creationDate;

    /**
     * @var
     * @Entity("Soil\DiscoverBundle\Entity\Agent")
     */
    protected $author;


    /**
     * @var
     * @Entity
     */
    protected $entity;


    /**
     * @var
     * @Entity("Soil\DiscoverBundle\Entity\Comment")
     */
    protected $parent;

    protected $subscriber;

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param mixed $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param mixed $subscriber
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;
    }


    public function getCheckSum()   {
        return $this->subscriber . '-' . is_object($this->comment) ? $this->comment->getOrigin() : $this->comment;
    }


}