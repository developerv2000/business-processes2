<?php

namespace App\Support\Abstracts;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Abstract Class CommentableModel
 *
 * This abstract class provides a base for models that need to have comments and must implement a title.
 * It includes methods to handle comments and enforces the implementation of a title.
 *
 * @package App\Support\Abstracts
 */
abstract class CommentableModel extends Model
{
    use HasFactory;

    /**
     * Get the title of the model.
     *
     * Each model extending this abstract class must implement this method to provide a title.
     *
     * @return string
     */
    abstract public function getTitle(): string;

    /**
     * Get all comments associated with the model, ordered by ID in descending order.
     *
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->orderBy('id', 'desc');
    }

    /**
     * Get the last comment associated with the model.
     *
     * @return MorphOne
     */
    public function lastComment(): MorphOne
    {
        return $this->morphOne(Comment::class, 'commentable')->latestOfMany();
    }

    /**
     * Store a new comment associated with the model.
     *
     * @param string|null $comment The comment body.
     * @return void
     */
    public function storeComment(?string $comment): void
    {
        if (!$comment) {
            return;
        }

        $this->comments()->create([
            'body' => $comment,
            'user_id' => request()->user()->id,
        ]);
    }
}
