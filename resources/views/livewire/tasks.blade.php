<div class="max-w-lg mx-auto mt-10">
    <h2 class="text-2xl font-bold mb-4">📝 To-Do List</h2>

    <form wire:submit.prevent="addTask">
        <input type="text" wire:model="title" placeholder="Enter task..."
            class="border rounded px-3 py-2 w-full">
        @error('title') <span class="text-red-500">{{ $message }}</span> @enderror
        <button class="bg-blue-500 text-white px-4 py-2 mt-2 rounded">Add</button>
    </form>

    <ul class="mt-4">
        @foreach($tasks as $task)
            <li class="flex justify-between items-center border-b py-2">
                <span>{{ $task->title }}</span>
                <button wire:click="deleteTask({{ $task->id }})" class="text-red-500">Delete</button>
            </li>
        @endforeach
    </ul>
</div>
