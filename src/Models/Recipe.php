<?php

namespace TomatoPHP\TomatoEddy\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $user
 * @property string $type
 * @property mixed $script
 * @property string $view
 * @property string $created_at
 * @property string $updated_at
 */
class Recipe extends Model
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
    protected $fillable = ['name', 'description', 'user', 'type', 'script', 'view', 'created_at', 'updated_at'];

    public function logs()
    {
        return $this->hasMany(RecipesServerLog::class);
    }
}
