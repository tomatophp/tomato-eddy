<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use Illuminate\Support\Facades\Log;
use TomatoPHP\TomatoEddy\Enums\Models\TaskStatus;
use TomatoPHP\TomatoEddy\Enums\Tasks\CallbackType;
use TomatoPHP\TomatoEddy\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskWebhookController extends Controller
{
    /**
     * Throws an exception if the task is not pending.
     */
    private function verifyTaskIsPending(Task $task): void
    {
        abort_unless($task->status === TaskStatus::Pending, 422, 'Task is not pending.');
    }

    /**
     * Throws an exception if the team that belongs to the task doesn't have an active subscription.
     */
    private function verifyTeamSubscription(Task $task): void
    {
        $teamSubscriptionOptions = $task->server->team->subscriptionOptions();

        if ($teamSubscriptionOptions->mustVerifySubscription() && ! $teamSubscriptionOptions->onTrialOrIsSubscribed()) {
            abort(402, 'Your team must have an active subscription to perform this action.');
        }
    }

    /**
     * Mark the task as timed out and update the output in the background.
     */
    public function markAsTimedOut(Request $request, Task $task): Response
    {
        $this->verifyTaskIsPending($task);

        $task->update([
            'status' => TaskStatus::Timeout,
            'exit_code' => 124,
        ]);

        $task->updateOutputInBackground()->handleCallback($request, CallbackType::Timeout);

        return response()->make();
    }

    /**
     * Mark the task as failed and update the output in the background.
     */
    public function markAsFailed(Request $request, Task $task): Response
    {
        $request->validate([
            'exit_code' => 'required|integer|min:1|max:255',
        ]);

        $this->verifyTaskIsPending($task);

        $task->update([
            'status' => TaskStatus::Failed,
            'exit_code' => $request->input('exit_code'),
        ]);

        $task->updateOutputInBackground()->handleCallback($request, CallbackType::Failed);

        Log::error('Task failed', [
            'task' => $task->toArray(),
            'request' => $request->all(),
        ]);

        return response()->make();
    }

    /**
     * Mark the task as finished and update the output in the background.
     */
    public function markAsFinished(Request $request, Task $task): Response
    {
        $this->verifyTaskIsPending($task);

        $task->update([
            'status' => TaskStatus::Finished,
        ]);

        $task->updateOutputInBackground()->handleCallback($request, CallbackType::Finished);

        return response()->make();
    }

    /**
     * Handle the callback.
     */
    public function callback(Request $request, Task $task): Response
    {
        $this->verifyTaskIsPending($task);

        $task->handleCallback($request, CallbackType::Custom);

        return response()->make();
    }
}
