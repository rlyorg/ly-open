<div class="flex-col space-y-4">

    <div class="w-full mb-6 pt-3">
        <div class="flex flex-row items-center justify-between mb-4">
            <div class="flex flex-col">
                <div class="text-xs  font-light text-grey-500">
                    List users with role
                </div>
                <div class="text-xl font-bold capitalize">{{$role->name}}</div>
            </div>
        </div>
    </div>
    
    <!-- Top Bar -->
    <div class="flex justify-between">
        <div class="w-2/4 flex space-x-4 relative">
            <x-input.text wire:model="filters.search" placeholder="Search ..." class="pl-10 pr-5 appearance-none h-10 w-full rounded-full text-sm focus:outline-none" />
            <button type="submit" class="absolute top-0 mt-3 left-0 ml-4">
                <x-icon.search />
            </button>
        </div>

        <div class="space-x-2 flex items-center">
            <x-input.group borderless paddingless for="perPage" label="Per Page">
                <x-input.select wire:model="perPage" id="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </x-input.select>
            </x-input.group>

            <x-dropdown label="Bulk Actions">
                <x-dropdown.item type="button" wire:click="$toggle('showDeleteModal')"
                    class="flex items-center space-x-2">
                    <x-icon.trash class="text-cool-gray-400" /> <span>Invoke</span>
                </x-dropdown.item>
            </x-dropdown>
        </div>
    </div>

    <!-- Table -->
    <x-table>
        <x-slot name="head">
            <x-table.heading class="pr-0 w-8"><x-input.checkbox wire:model="selectPage" /></x-table.heading>
            <x-table.heading sortable multi-column wire:click="sortBy('name')" :direction="$sorts['name'] ?? null" class="w-full">Name</x-table.heading>
            <x-table.heading sortable multi-column wire:click="sortBy('email')" :direction="$sorts['email'] ?? null">Email</x-table.heading>
            <x-table.heading sortable multi-column wire:click="sortBy('status')" :direction="$sorts['status'] ?? null">Status</x-table.heading>
            <x-table.heading sortable multi-column wire:click="sortBy('date')" :direction="$sorts['date'] ?? null">Created_at</x-table.heading>
            <x-table.heading >Role</x-table.heading>
            <x-table.heading />
        </x-slot>

        <x-slot name="body">
            @if ($selectPage)
                <x-table.row class="bg-cool-gray-200" wire:key="row-message">
                    <x-table.cell colspan="6">
                        @unless($selectAll)
                            <div>
                                <span>You have selected <strong>{{ $models->count() }}</strong> users, do you want to select
                                    all <strong>{{ $models->total() }}</strong>?</span>
                                <x-button.link wire:click="selectAll" class="ml-1 text-blue-600">Select All</x-button.link>
                            </div>
                        @else
                            <span>You are currently selecting all <strong>{{ $models->total() }}</strong> users.</span>
                @endif
                </x-table.cell>
                </x-table.row>
                @endif

                @forelse ($models as $user)
                    <x-table.row wire:loading.class.delay="opacity-50" wire:key="row-{{ $user->id }}">
                        <x-table.cell class="pr-0">
                            <x-input.checkbox wire:model="selected" value="{{ $user->id }}" />
                        </x-table.cell>

                        <x-table.cell>
                            <span href="#" class="inline-flex space-x-2 truncate text-sm leading-5">
                                <x-icon.cash class="text-cool-gray-400" />

                                <p class="text-cool-gray-600 truncate">
                                    {{ $user->name }}
                                </p>
                            </span>
                        </x-table.cell>

                        <x-table.cell>
                            <span class="text-cool-gray-900 font-medium">{{ $user->email }} </span>
                        </x-table.cell>

                        <x-table.cell>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-{{ $user->status_color }}-100 text-{{ $user->status_color }}-800 capitalize">
                                {{ $user->active }}
                            </span>
                        </x-table.cell>

                        <x-table.cell>
                            {{ $user->created_date_for_humans }}
                        </x-table.cell>

                        <x-table.cell>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-{{ $user->status_color }}-100 text-{{ $user->status_color }}-800 capitalize">
                                {{ $role->name }}
                            </span>
                        </x-table.cell>


                        <x-table.cell>
                            <x-button.link wire:click="edit({{ $user->id }})">Invoke</x-button.link>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="6">
                            <div class="flex justify-center items-center space-x-2">
                                <x-icon.inbox class="h-8 w-8 text-cool-gray-400" />
                                <span class="font-medium py-8 text-cool-gray-400 text-xl">No items found...</span>
                            </div>
                        </x-table.cell>
                    </x-table.row>
                @endforelse
        </x-slot>
    </x-table>

    <div>
        {{ $models->links() }}
    </div>

    <!-- Delete Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.confirmation wire:model.defer="showDeleteModal">
            <x-slot name="title">Delete</x-slot>

            <x-slot name="content">
                <div class="py-8 text-cool-gray-700">Are you sure you? This action is irreversible.</div>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$set('showDeleteModal', false)">Cancel</x-button.secondary>

                <x-button.primary type="submit">Delete</x-button.primary>
            </x-slot>
        </x-modal.confirmation>
    </form>
</div>
