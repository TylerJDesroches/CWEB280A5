<?php

class CommentMember
{
    public $commentId;
    // The actual text within the comment
    public $description;
    public $ranking;
    public $alias;
    public $imagePath;

    public function __construct(Comment $comment, Member $member)
    {
        $this->commentId = $comment->commentId;
        $this->description = $comment->description;
        $this->ranking = $comment->ranking;
        $this->alias = $member->alias;
        $this->imagePath = $member->profileImgPath;
    }
}