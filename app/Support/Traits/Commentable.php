<?php

namespace App\Support\Traits;

use App\Models\Comment;

/**
 * Trait Commentable
 *
 * This trait provides functionality for associating comments with a model.
 *
 * @package App\Traits
 */
trait Commentable
{
    /**
     * Get all comments associated with the model, ordered by ID in descending order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->orderBy('id', 'desc');
    }

    /**
     * Get the last comment associated with the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function lastComment()
    {
        return $this->morphOne(Comment::class, 'commentable')->latestOfMany();
    }

    /**
     * Store a new comment associated with the model.
     *
     * @param string|null $comment The comment body.
     * @return void
     */
    private function storeComment($comment)
    {
        if (!$comment) {
            return;
        }

        $this->comments()->save(
            new Comment([
                'body' => $comment,
                'user_id' => request()->user()->id,
            ])
        );
    }
}
