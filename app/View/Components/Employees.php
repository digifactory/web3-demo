<?php

namespace App\View\Components;

use App\Models\Employee;
use App\Models\Vote;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Employees extends Component
{
    public Collection $employees;
    public bool $hasVoted;
    public ?Vote $vote;

    public function render()
    {
        $this->vote = Auth::user()->vote;
        $this->hasVoted = $this->vote !== null;

        $this->employees = Employee::query()
            ->withCount(['votes'])
            ->when($this->hasVoted, fn (Builder $q) => $q->orderBy('votes_count', 'desc'), fn (Builder $q) => $q->inRandomOrder())
            ->get();

        return view('components.employees');
    }
}
