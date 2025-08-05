<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('tasks'),
        ];
    }


    public function broadcastWith(): array
    {
        return [
            'task' => $this->task,
            'message' => 'New task: ' . $this->task->title,
            'user' => $this->task->user->full_name,
        ];
    }

    /**
     * Get the name of the event broadcast.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'TaskCreated';
    }
}
