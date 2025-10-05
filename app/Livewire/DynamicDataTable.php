<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Item;

class DynamicDataTable extends Component
{
    use WithPagination;

    // Props / Config that can be passed from outside (via mount)
    public array $columns = [];        // e.g. ['id','name','price']
    public array $filterConfig = [];   // e.g. ['name'=>'text','price'=>'number','created_at'=>'date']
    public array $filters = [];        // holds filter values e.g. ['name'=>'foo']
    public string $showSoftDeleted = 'without'; // 'without'|'with'|'only'
    public bool $showAddForm = false;  // control add-new form visibility
    public array $newItem = [
        'name' => '',
        'description' => '',
        'price' => ''
    ];
    public int $perPage = 10;

    // ensure filters and soft delete selection show up in the URL
    protected $queryString = [
        'filters',
        'showSoftDeleted' => ['except' => 'without'],
        'page' // WithPagination uses page
    ];

    // Accept props via mount (props-driven)
    public function mount(
        $columns = ['id', 'name', 'description', 'price', 'created_at'],
        $filterConfig = [],           // map field=>type: text | number | date
        $showSoftDeleted = 'without',
        $showAddForm = false
    ) {
        $this->columns = $columns;
        $this->filterConfig = $filterConfig;
        $this->showSoftDeleted = $showSoftDeleted;
        $this->showAddForm = $showAddForm;

        // Initialize filter values (so they appear in query string structure)
        foreach (array_keys($filterConfig) as $field) {
            $this->filters[$field] = $this->filters[$field] ?? null;
        }
    }

    // reset pagination when filters change
    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function updatingShowSoftDeleted()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'newItem.name' => 'required|string|max:255',
            'newItem.description' => 'nullable|string',
            'newItem.price' => 'required|numeric|min:0',
        ];
    }

    public function addItem()
    {
        $this->validate();

        Item::create($this->newItem);

        session()->flash('success', 'Item added successfully.');

        // reset the form
        $this->newItem = ['name'=>'', 'description'=>'', 'price'=>''];
        // reload table
        $this->resetPage();
    }

    // soft-delete an item
    public function deleteItem($id)
    {
        $item = Item::find($id);
        if ($item) {
            $item->delete();
            session()->flash('success', 'Item moved to trash.');
        }
    }

    // restore soft deleted
    public function restoreItem($id)
    {
        $item = Item::withTrashed()->find($id);
        if ($item) {
            $item->restore();
            session()->flash('success', 'Item restored.');
        }
    }

    // permanently delete
    public function forceDeleteItem($id)
    {
        $item = Item::withTrashed()->find($id);
        if ($item) {
            $item->forceDelete();
            session()->flash('success', 'Item permanently deleted.');
        }
    }

    public function render()
    {
        $query = Item::query();

        // soft-delete handling
        if ($this->showSoftDeleted === 'only') {
            $query->onlyTrashed();
        } elseif ($this->showSoftDeleted === 'with') {
            $query->withTrashed();
        }

        // apply filters according to types declared in $filterConfig
        foreach ($this->filters as $field => $value) {
            if ($value === null || $value === '') continue;
            $type = $this->filterConfig[$field] ?? 'text';

            if ($type === 'text') {
                $query->where($field, 'like', "%{$value}%");
            } elseif ($type === 'number') {
                // allow range like "10-50" or a single number
                if (str_contains($value, '-')) {
                    [$min, $max] = explode('-', $value);
                    $query->whereBetween($field, [(float)$min, (float)$max]);
                } else {
                    $query->where($field, (float)$value);
                }
            } elseif ($type === 'date') {
                $query->whereDate($field, $value);
            }
        }

        $items = $query->orderByDesc('id')->paginate($this->perPage);

        return view('livewire.dynamic-data-table', compact('items'));
    }
}
