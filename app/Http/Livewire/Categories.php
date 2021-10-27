<?php


namespace App\Http\Livewire;

use App\Models\Category as Model;
use Livewire\Component;

use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;

class Categories extends Component
{
    public $contents;
    public int $wechatBotId;
    public $categories;

    public function mount()
    {
        $this->editing = $this->makeBlankModel();
    }

    public function render()
    {
        return view('livewire.categories', [
            'models' => $this->rows,
            ])->layout('layouts.admin');
    }

    use WithPerPagePagination, WithSorting, WithBulkActions,  WithCachedRows;
    public $showEditModal = false;
    public $model = Model::class;
    public $filters = [
        'search' => '',
    ];
    public Model $editing;

    protected $queryString = ['sorts'];

    // protected $listeners = ['refreshPrograms' => '$refresh'];

    public function rules()
    {
        return [
            'editing.name' => 'required|min:4'
        ];
    }

    public function makeBlankModel()
    {
        return Model::make();
    }

    public function create()
    {
        $this->useCachedRows();
        if ($this->editing->getKey()) $this->editing = $this->makeBlankModel();
        $this->showEditModal = true;
    }


    public function edit(Model $model)
    {
        $this->useCachedRows();

        if ($this->editing->isNot($model)) $this->editing = $model;
    
        $this->showEditModal = true;
    }

    public function save()
    {
        $this->validate();
        $this->editing->save();

        $this->showEditModal = false;

        $this->dispatchBrowserEvent('notify', 'Saved!');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $query = Model::query()
            ->when($this->filters['search'], fn($query, $search) => $query->where('name', 'like', '%' . $search . '%'));

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function sync()
    {
        
    }

}