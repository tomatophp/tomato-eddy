<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use Illuminate\Support\Str;
use TomatoPHP\TomatoEddy\Events\DeploymentUpdated;
use TomatoPHP\TomatoEddy\Jobs\InstallSiteCaddyfile;
use TomatoPHP\TomatoEddy\Jobs\InstallWordpressCron;
use TomatoPHP\TomatoEddy\Models\Deployment;
use TomatoPHP\TomatoEddy\Enums\Models\DeploymentStatus;
use TomatoPHP\TomatoEddy\Models\Site;
use TomatoPHP\TomatoEddy\Enums\Models\SiteType;
use TomatoPHP\TomatoEddy\Models\Task as TaskModel;
use TomatoPHP\TomatoEddy\Rules\Sha1;
use Illuminate\Http\Request;

class DeploySite extends Task implements HasCallbacks
{
    protected int $timeout = 600;

    public Site $site;

    public function __construct(public Deployment $deployment, public array $environmentVariables = [])
    {
        $this->site = $deployment->site;
    }

    public function onOutputUpdated(string $output): void
    {
        event(new DeploymentUpdated($this->deployment));
    }

    protected function onTimeout(TaskModel $task, Request $request)
    {
        $this->deployment->forceFill(['status' => DeploymentStatus::Timeout])->save();
        $this->deployment->notifyUserAboutFailedDeployment();
    }

    protected function onFailed(TaskModel $task, Request $request)
    {
        $this->deployment->forceFill(['status' => DeploymentStatus::Failed])->save();
        $this->deployment->notifyUserAboutFailedDeployment();
    }

    protected function onFinished(TaskModel $task, Request $request)
    {
        $this->deployment->forceFill(['status' => DeploymentStatus::Finished])->save();

        if (! $this->site->installed_at) {
            dispatch(new InstallSiteCaddyfile($this->site, $this->deployment->user));

            if ($this->site->type === SiteType::Wordpress) {
                dispatch(new InstallWordpressCron($this->site));
            }
        }
    }

    protected function onCustomCallback(TaskModel $task, Request $request)
    {
        $data = $request->validate([
            'git_hash' => ['nullable', 'string', new Sha1],
        ]);

        if ($gitHash = $data['git_hash'] ?? null) {
            $this->deployment->forceFill(['git_hash' => $gitHash])->save();
        }
    }

    public function getViewData(): array
    {
        return [
            'logsDirectory' => $this->site->getLogsDirectory(),
            'repositoryDirectory' => "{$this->site->path}/repository",
            'env' => array_merge(
                $this->site->generateEnvironmentVariables(),
                $this->environmentVariables
            ),
        ];
    }
}
