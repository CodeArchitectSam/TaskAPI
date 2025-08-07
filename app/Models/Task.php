<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date'
    ];

    public function scopeFilterByStatus($query, $status)
    {
        if ($status) {
            $query->where('status', $status);
        }
    }

    public function scopeFilterByDueDate($query, $dueDate, $dueDateFrom, $dueDateTo)
    {
        if ($dueDate) {
            $query->where('due_date', $dueDate);
        } elseif ($dueDateFrom && $dueDateTo) {
            $query->whereBetween('due_date', [$dueDateFrom, $dueDateTo]);
        } elseif ($dueDateFrom) {
            $query->where('due_date', '>=', $dueDateFrom);
        } elseif ($dueDateTo) {
            $query->where('due_date', '<=', $dueDateTo);
        }
    }

    public function scopeApplySort($query, $sortBy, $sortOrder = 'asc')
    {
        return $sortBy ? $query->orderBy($sortBy, $sortOrder) : $query;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
