<?php


namespace App\Http\Livewire;

use App\Models\Item as Model;
use Livewire\Component;

use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;

class Items extends Component
{
    public $contents;
    public int $wechatBotId;

    public function mount()
    {
        $this->editing = $this->makeBlankModel();
        // $this->sorts = ['updated_at'=>'desc']; //默认排序
    }

    public function render()
    {
        return view('livewire.items', [
            'models' => $this->rows,
            ])->layout('layouts.admin');
    }

    use WithPerPagePagination, WithSorting, WithCachedRows;
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
            'editing.alias' => 'required|min:2',
            'editing.name' => 'required|min:2',
            'editing.category_id' => 'required|integer', //default value.
            // $table->timestamp('begin_at')->default('2021-01-01 00:00:00');
            // $table->timestamp('end_at')->nullable();
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
        $query = Model::query()->with(['program'])
            ->when($this->filters['search'], function($query, $search){
                if(preg_match('/^[a-z]{2,}\d{6}$/', $search, $matches)){ //ee120201
                    return $query->where('alias', 'like', '%' . $search . '%');
                }
                return Model::search($search);
            });
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
        Artisan::call('ly:update');
    }
}