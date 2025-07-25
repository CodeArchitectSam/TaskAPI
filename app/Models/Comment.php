<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'task_id',
        'content',
        'author_name'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
