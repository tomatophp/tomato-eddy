<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Widget extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $label,
        public string $counter = '0',
        public ?string $by = null,
        public string $text = 'text-success-400',
        public ?string $style = null,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.widget');
    }
}
