<div class="flex-col space-y-4">
    <!-- Top Bar -->
    <div class="flex justify-between">
        <div class="w-2/4 flex space-x-4 relative">
            <x-input.text wire:model="filters.search" placeholder="Search by NAME..." class="pl-10 pr-5 appearance-none h-10 w-full rounded-full text-sm focus:outline-none" />
            <button type="submit" class="absolute top-0 mt-3 left-0 ml-4">
                <x-icon.search />
            </button>
        </div>

        <div class="space-x-2 flex items-center">
            <x-input.group borderless paddingless for="perPage" label="Per Page">
                <x-input.select wire:model="perPage" id="perPage" class="rounded-md">
                    <option value="50">50</option>
                    <option value="100">100</option>
                </x-input.select>
            </x-input.group>

            <x-button.primary wire:click="sync"><x-icon.sync /> Sync </x-button.primary>
            <x-button.primary wire:click="create"><x-icon.plus /> New </x-button.primary>
        </div>
    </div>

    <!-- Table -->
    <x-table>
        <x-slot name="head">
            <x-table.heading class="pr-0 w-8">
                <x-input.checkbox wire:model="selectPage" />
            </x-table.heading>
            <x-table.heading  multi-column
            sortable wire:click="sortBy('alias')" :direction="$sorts['alias'] ?? null"
            >Alias</x-table.heading>
            <x-table.heading  multi-column
            sortable wire:click="sortBy('name')" :direction="$sorts['name'] ?? null"
            >Name</x-table.heading>
            <x-table.heading  multi-column
            sortable wire:click="sortBy('category_id')" :direction="$sorts['category_id'] ?? null"
            >Category</x-table.heading>
            <x-table.heading  multi-column class="w-full">Brief</x-table.heading>
            <x-table.heading sortable multi-column wire:click="sortBy('begin_at')" :direction="$sorts['begin_at'] ?? null">
                Begin_at</x-table.heading>
            <x-table.heading sortable multi-column wire:click="sortBy('status')" :direction="$sorts['status'] ?? null">
                    Status</x-table.heading>
            <x-table.heading />
        </x-slot>

        <x-slot name="body">
            @if ($selectPage)
                <x-table.row class="bg-cool-gray-200" wire:key="row-message">
                    <x-table.cell colspan="8">
                        @unless($selectAll)
                            <div>
                                <span>You have selected <strong>{{ $models->count() }}</strong> items, do you want to select
                                    all <strong>{{ $models->total() }}</strong>?</span>
                                <x-button.link wire:click="selectAll" class="ml-1 text-blue-600">Select All</x-button.link>
                            </div>
                        @else
                            <span>You are currently selecting all <strong>{{ $models->total() }}</strong> items.</span>
                        @endif
                    </x-table.cell>
                </x-table.row>
                @endif

                @forelse ($models as $model)
                    <x-table.row wire:loading.class.delay="opacity-50" wire:key="row-{{ $model->id }}">
                        <x-table.cell class="pr-0">
                            <x-input.checkbox wire:model="selected" value="{{ $model->id }}" />
                        </x-table.cell>

                        <x-table.cell>
                            <p class="text-cool-gray-600 font-medium truncate">
                                {{ $model->alias }}
                            </p>
                        </x-table.cell>

                        <x-table.cell>
                                <p class="text-cool-gray-600 font-medium truncate">
                                    {{ $model->name }}
                                </p>
                        </x-table.cell>


                        <x-table.cell class="">
                            <p class="text-cool-gray-600 truncate">
                                {{ $model->category->name }}
                            </p>
                        </x-table.cell>

                        <x-table.cell>
                            <p class="text-cool-gray-600 truncate">
                                {{ $model->brief }}
                            </p>
                        </x-table.cell>


                        <x-table.cell>
                            {{ $model->created_date_for_humans }}
                        </x-table.cell>

                        <x-table.cell>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-{{ $model->status_color }}-100 text-{{ $model->status_color }}-800 capitalize">
                                {{ $model->active }}
                            </span>
                        </x-table.cell>

                        <x-table.cell>
                            <x-button.link wire:click="edit({{ $model->id }})">Edit</x-button.link>
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


    <!-- Save Modal -->
    <form wire:submit.prevent="save">
        <x-modal.dialog :maxWidth="'sm:max-w-lg'" wire:model.defer="showEditModal">
            <x-slot name="title">Edit</x-slot>

            <x-slot name="content">
                <x-input.group for="name" label="Name" :error="$errors->first('editing.name')">
                    <x-input.text wire:model="editing.name" placeholder="Name" />
                </x-input.group>

                <x-input.group for="alias" label="Alias" :error="$errors->first('editing.alias')">
                    <x-input.text wire:model="editing.alias" placeholder="Alias" />
                </x-input.group>

                <x-input.group for="category_id" label="Category" :error="$errors->first('editing.category_id')">

                    <x-input.select id="category_id" wire:model.defer="editing.category_id" class="mt-1 block w-full font-medium  text-gray-700" autocomplete="category_id" >
                     
                        @foreach ($categories as $id => $name)
                        <option value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </x-input.select>

                </x-input.group>

                <x-input.group for="brief" label="Brief" :error="$errors->first('editing.brief')">
                    <x-input.text wire:model="editing.brief" placeholder="brief" />
                </x-input.group>
 
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$set('showEditModal', false)">Cancel</x-button.secondary>

                <x-button.primary type="submit">Save</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>
</div>
