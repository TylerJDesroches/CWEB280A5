<?php

/**
 * Stores a comment that is associated with an image.
 *
 * @version 1.0
 * @author cst243
 */
class Comment
{
    public $commentId;
    public $description;
    public $ranking;
    public $imageId;
    public $memberId;

    /**
     * Creates a new comment object
     * @param mixed $commentId the id of the comment
     * @param mixed $description what the comment says
     * @param mixed $ranking the ranking of the comment (upvote downvote)
     * @param mixed $imageId the id of the image that is being commented on
     * @param mixed $memberId the id of the member that made the comment
     */
    public function __construct($commentId, $description, $ranking, $imageId, $memberId)
    {
        $this->commentId = $commentId;
        $this->description = $description;
        $this->ranking = $ranking;
        $this->imageId = $imageId;
        $this->memberId = $memberId;
    }
}