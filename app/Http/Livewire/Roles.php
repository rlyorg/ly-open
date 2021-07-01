<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Carbon;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;

class Roles extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions, WithCachedRows;

    public $showDeleteModal = false;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $header = 'Roles';
    public $filters = [
        'search' => '',
    ];
    public Role $editing;
    public Array $permissons;

    protected $queryString = ['sorts'];

    protected $listeners = ['refreshUsers' => '$refresh'];

    //todo add unique rules
    public function rules()
    {
        return [
            'editing.name' => 'required|min:2',
        ];
    }

    public function mount()
    {
        $this->resetPermissons();
        $this->editing = $this->makeBlankModel();
    }

    public function resetPermissons(){
        $this->permissons =  array_fill_keys(Permission::pluck('name')->toArray(),false);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function deleteSelected()
    {
        $deleteCount = $this->selectedRowsQuery->count();

        $this->selectedRowsQuery->delete();

        $this->showDeleteModal = false;

        $this->notify('You\'ve deleted ' . $deleteCount);
    }

    public function makeBlankModel()
    {
        return Role::make();
    }

    public function create()
    {
        $this->useCachedRows();

        if ($this->editing->getKey()) $this->editing = $this->makeBlankModel();

        $this->showCreateModal = true;
    }


    public function edit(Role $role)
    {
        $this->useCachedRows();

        if ($this->editing->isNot($role)) $this->editing = $role;

        $this->resetPermissons();
        foreach ($role->permissions as $permission) {
            $this->permissons[$permission->name] = true;
        }
        $this->showEditModal = true;
    }

    public function savePermissons()
    {
        foreach ($this->permissons as $name => $value) {
            if($value){
                $this->editing->givePermissionTo($name);
            }
        }
        $this->showEditModal = false;
        
        $this->dispatchBrowserEvent('notify', 'Saved!');
    }


    public function save()
    {
        $this->validate();

        $this->editing->save();

        $this->showCreateModal = false;

        $this->dispatchBrowserEvent('notify', 'Saved!');
    }
    

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $query = Role::query()
            ->when($this->filters['search'], fn($query, $search) => $query->where('name', 'like', '%' . $search . '%'));

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function render()
    {
        return view('livewire.roles', [
            'models' => $this->rows,
            ])->layout('layouts.admin');
    }
}
