<?php

namespace TomatoPHP\TomatoEddy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $server_id
 * @property string $recipe_id
 * @property string $task_id
 * @property string $created_at
 * @property string $updated_at
 */
class RecipesServerLog extends Model
{
    use HasUlids;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var array
     */
    protected $fillable = ['server_id', 'recipe_id', 'task_id', 'created_at', 'updated_at'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
