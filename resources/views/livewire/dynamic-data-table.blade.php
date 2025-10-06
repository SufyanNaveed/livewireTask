<div class="p-4">
    <!-- Feedback -->
    @if (session()->has('success'))
        <div class="mb-4 text-green-700">{{ session('success') }}</div>
    @endif

    <!-- Column Selection UI -->
    <div class="mb-4">
        <label class="font-semibold">Select Columns:</label>
        <div class="flex flex-wrap gap-4 mt-2">
            @foreach($allAvailableColumns as $key => $label)
                <label class="flex items-center space-x-1">
                    <input type="checkbox"
                        value="{{ $key }}"
                        wire:model="columns"
                        class="rounded border-gray-300">
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Top controls: dynamic filters and soft delete toggle -->
    <div class="flex flex-wrap gap-3 mb-4">
        @foreach($filterConfig as $field => $type)
            <div>
                @if($type === 'date')
                    <input type="date" wire:model.debounce.300ms="filters.{{ $field }}" placeholder="{{ ucfirst($field) }}" class="border rounded px-2 py-1">
                @elseif($type === 'number')
                    <input type="text" wire:model.debounce.300ms="filters.{{ $field }}" placeholder="{{ ucfirst($field) }} (e.g. 10 or 10-50)" class="border rounded px-2 py-1">
                @else
                    <input type="text" wire:model.debounce.300ms="filters.{{ $field }}" placeholder="Filter {{ ucfirst($field) }}" class="border rounded px-2 py-1">
                @endif
            </div>
        @endforeach

        <div>
            <select wire:model="showSoftDeleted" class="border rounded px-2 py-1">
                <option value="without">Without Deleted</option>
                <option value="with">With Deleted</option>
                <option value="only">Only Deleted</option>
            </select>
        </div>

        <div>
            <select wire:model="perPage" class="border rounded px-2 py-1">
                <option value="5">5 / page</option>
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
            </select>
        </div>

        <div>
            <button wire:click="$toggle('showAddForm')" class="border px-3 py-1 rounded">
                {{ $showAddForm ? 'Close Add Form' : 'Add New Item' }}
            </button>
        </div>
    </div>

    <!-- Add new item inline form -->
    @if ($showAddForm)
        <div class="mb-4 border p-3 rounded">
            <div class="flex gap-2 items-end">
                <div>
                    <label class="block text-sm">Name</label>
                    <input type="text" wire:model.defer="newItem.name" class="border rounded px-2 py-1">
                    @error('newItem.name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm">Description</label>
                    <input type="text" wire:model.defer="newItem.description" class="border rounded px-2 py-1">
                    @error('newItem.description') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm">Price</label>
                    <input type="number" step="0.01" wire:model.defer="newItem.price" class="border rounded px-2 py-1">
                    @error('newItem.price') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button wire:click="addItem" class="bg-blue-600 text-white px-3 py-1 rounded">Add</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-auto border rounded">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    @foreach($columns as $col)
                        <th class="px-4 py-2 text-left">{{ \Illuminate\Support\Str::headline($col) }}</th>
                    @endforeach
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="border-t">
                        @foreach($columns as $col)
                            <td class="px-4 py-2">
                                @php
                                    // show formatted created_at for dates
                                    if ($col === 'created_at' && $item->$col) {
                                        echo \Carbon\Carbon::parse($item->$col)->toDateString();
                                    } else {
                                        echo \Illuminate\Support\Str::limit($item->$col, 80);
                                    }
                                @endphp
                            </td>
                        @endforeach

                        <td class="px-4 py-2">
                            @if($item->trashed())
                                <button wire:click="restoreItem({{ $item->id }})" class="px-2 py-1 border rounded text-sm mr-1">Restore</button>
                                <button wire:click="forceDeleteItem({{ $item->id }})" onclick="return confirm('Delete permanently?')" class="px-2 py-1 border rounded text-sm text-red-600">Delete Permanently</button>
                            @else
                                <button wire:click="deleteItem({{ $item->id }})" onclick="return confirm('Move to trash?')" class="px-2 py-1 border rounded text-sm text-red-600">Delete</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="px-4 py-4 text-center text-gray-600">No items found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
