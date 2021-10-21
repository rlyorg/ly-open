<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;

class Users extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions, WithCachedRows;

    public $showDeleteModal = false;
    public $showEditModal = false;
    public $model = User::class;
    public $filters = [
        'search' => '',
    ];
    public User $editing;

    protected $queryString = ['sorts'];

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function rules()
    {
        return [
            'editing.name' => 'required|min:3',
            'editing.email' => 'required|email',
            'editing.password' => 'nullable',
        ];
    }

    public function mount()
    {
        $this->editing = $this->makeBlankModel();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function exportSelected()
    {
        return response()->streamDownload(function () {
            echo $this->selectedRowsQuery->toCsv();
        }, $this->editing->getTable() . '.csv');
    }

    public function deleteSelected()
    {
        $deleteCount = $this->selectedRowsQuery->count();

        $this->selectedRowsQuery->delete();

        $this->showDeleteModal = false;

        $this->notify('You\'ve deleted ' . $deleteCount . ' users');
    }

    public function makeBlankModel()
    {
        return User::make(['date' => now(), 'status' => 'success']);
    }

    public function create()
    {
        $this->useCachedRows();

        if ($this->editing->getKey()) $this->editing = $this->makeBlankModel();

        $this->showEditModal = true;
    }

    public function edit(User $user)
    {
        $this->useCachedRows();

        if ($this->editing->isNot($user)) $this->editing = $user;

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
        $query = User::query()->with('meta')
            ->when($this->filters['search'], fn($query, $search) => $query->where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%'));

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
        return view('livewire.users', [
            'models' => $this->rows,
        ])->layout('layouts.admin');
    }
}
