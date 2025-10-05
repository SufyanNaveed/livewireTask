<?php 

namespace App\Livewire;

use Livewire\Component;
use App\Models\Task;

class Tasks extends Component
{
    public $tasks;
    public $title;

    public function mount()
    {
        $this->tasks = Task::all();
    }

    public function addTask()
    {
        $this->validate([
            'title' => 'required|min:3',
        ]);

        Task::create(['title' => $this->title]);

        $this->title = '';
        $this->tasks = Task::all();
    }

    public function deleteTask($id)
    {
        Task::find($id)?->delete();
        $this->tasks = Task::all();
    }

    public function render()
    {
        return view('livewire.tasks');
    }
}
