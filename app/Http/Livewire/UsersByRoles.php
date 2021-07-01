<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;

class UsersByRoles extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions, WithCachedRows;
    public Role $role;
    public $title = "List Users with Role: ";

    public $filters = [
        'search' => '',
    ];

    public function mount(Role $role)
    {
        $this->role = $role;
        $this->title .= $role->name;
    }


    public function getRowsProperty()
    {
        return User::when($this->filters['search'], fn($query, $search) => $query->where('name', 'like', '%' . $search . '%')->orWhere('email', 'like', '%' . $search . '%'))
            ->role($this->role->name)
            ->paginate($this->page);
    }


    public function render()
    {

        return view('livewire.users-by-roles', [
            'models' => $this->rows,
        ])->layout('layouts.admin');
    }
}
