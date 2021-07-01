<div class="flex-col space-y-4">
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
                <x-input.select wire:model="perPage" id="perPage" class="rounded-md">
                    <option value="50">50</option>
                    <option value="100">100</option>
                </x-input.select>
            </x-input.group>
            
            <x-button.primary wire:click="create"><x-icon.plus /> New </x-button.primary>
        </div>
    </div>

    <!-- Table -->
    <x-table>
        <x-slot name="head">
            <x-table.heading multi-column >Program</x-table.heading>
            <x-table.heading multi-column 
                sortable
                wire:click="sortBy('alias')" :direction="$sorts['alias'] ?? null"
                >Alias</x-table.heading>
            <x-table.heading multi-column class="w-full">Description</x-table.heading>
            <x-table.heading multi-column 
                sortable
                wire:click="sortBy('play_at')" :direction="$sorts['play_at'] ?? null">Play_at</x-table.heading>
            <x-table.heading multi-column 
                sortable 
                wire:click="sortBy('content_id')" :direction="$sorts['content_id'] ?? null">
                Content</x-table.heading>
            <x-table.heading />
        </x-slot>

        <x-slot name="body">

                @forelse ($models as $model)
                    <x-table.row wire:loading.class.delay="opacity-50" wire:key="row-{{ $model->id }}">

                        <x-table.cell>
                            <span class="text-cool-gray-900 font-medium">{{ $model->program->name }} </span>
                        </x-table.cell>

                        <x-table.cell>
                            <span class="text-cool-gray-900 font-medium">{{ $model->alias }} </span>
                        </x-table.cell>

                        <x-table.cell>
                            <span class="text-cool-gray-900 font-medium">{{ $model->description }} </span>
                        </x-table.cell>

                        <x-table.cell>
                            {{ $model->getDate() }}
                        </x-table.cell>

                        <x-table.cell>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-{{ $model->status_color }}-100 text-{{ $model->status_color }}-800 capitalize">
                                {{ $model->content_id }}
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
        <x-modal.dialog wire:model.defer="showEditModal">
            <x-slot name="title">Edit</x-slot>

            <x-slot name="content">
                <x-input.group for="name" label="Name" :error="$errors->first('editing.name')">
                    <x-input.text wire:model="editing.name" id="name" placeholder="Name" />
                </x-input.group>

                <x-input.group for="email" label="Email" :error="$errors->first('editing.email')">
                    <x-input.text wire:model="editing.email" id="email" />
                </x-input.group>

                <x-input.group for="password" label="Password" :error="$errors->first('editing.password')">
                    <x-input.text wire:model.lazy="editing.password" id="password" />
                </x-input.group>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$set('showEditModal', false)">Cancel</x-button.secondary>

                <x-button.primary type="submit">Save</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>
</div>
