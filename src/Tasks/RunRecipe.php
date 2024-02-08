<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use Illuminate\Support\Str;
use TomatoPHP\TomatoEddy\Models\Recipe;
use TomatoPHP\TomatoEddy\Models\Site;

class RunRecipe extends Task
{

    public function __construct(
        public Recipe $recipe,
    )
    {
    }

    /**
     * The command to run.
     */
    public function render(): string
    {
        return view($this->recipe->view);
    }
}
