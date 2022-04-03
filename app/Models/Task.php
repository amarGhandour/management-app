<?php

namespace App\Models;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    protected $touches = ['project'];

    protected static $recordableEvents = ['created', 'deleted'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function complete()
    {
        $this->update(['completed' => true]);
        $this->recordsActivity('completed');
    }

    public function incomplete()
    {
        $this->update(['completed' => false]);
        $this->recordsActivity('uncompleted');
    }

}
