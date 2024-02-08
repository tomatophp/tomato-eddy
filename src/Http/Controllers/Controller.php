<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use TomatoPHP\TomatoAdmin\Models\Team;
use TomatoPHP\TomatoEddy\Models\ActivityLog;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    public function user()
    {
        return auth('web')->user();
    }

    /**
     * Get the current team of the authenticated user.
     */
    protected function team(): Team
    {
        if(Team::count() === 0) {
            return $this->user()->ownedTeams()->create([
                'name' => 'Default Team',
                'personal_team' => true,
            ]);
        }

        return $this->user()->currentTeam;
    }

    /**
     * Log an activity.
     */
    public function logActivity(string $description, Model $subject = null): ActivityLog
    {
        return ActivityLog::create([
            'team_id' => $this->team()->id,
            'user_id' => $this->user()->id,
            'subject_id' => $subject ? $subject->getKey() : null,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'description' => $description,
        ]);
    }


}
