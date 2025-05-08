<?php

namespace App\Model;

use Nette;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

final class ForumCommentFacade
{
    public function __construct(
        private Explorer $database,
    ) {}

    public function add(int $postId, int $authorId, string $content): void
    {
        $this->database->table('forum_comment')->insert([
            'post_id' => $postId,
            'author_id' => $authorId,
            'content' => $content,
        ]);
    }

    public function getByPost(int $postId): Selection
    {
        return $this->database->table('forum_comment')
            ->where('post_id', $postId)
            ->order('created_at ASC');
    }
}
