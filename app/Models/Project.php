<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($project) {
            $project->tasks->each(function ($task) {
                $task->delete();
            });
        });

    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function addTask($attributes)
    {
        $attributes += ['user_id' => auth()->id()];

        return $this->tasks()->create($attributes);
    }

}
