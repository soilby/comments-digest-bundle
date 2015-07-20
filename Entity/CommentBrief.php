<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.7.15
 * Time: 0.19
 */

namespace Soil\CommentsDigestBundle\Entity;


class CommentBrief {

    protected $comment;

    protected $creationDate;

    protected $author;

    protected $entity;

    protected $entityAuthor;

    protected $parent;

    protected $parentAuthor;

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
    public function getEntityAuthor()
    {
        return $this->entityAuthor;
    }

    /**
     * @param mixed $entityAuthor
     */
    public function setEntityAuthor($entityAuthor)
    {
        $this->entityAuthor = $entityAuthor;
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
    public function getParentAuthor()
    {
        return $this->parentAuthor;
    }

    /**
     * @param mixed $parentAuthor
     */
    public function setParentAuthor($parentAuthor)
    {
        $this->parentAuthor = $parentAuthor;
    }



}