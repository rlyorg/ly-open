<x-base-layout>
    <div id="wrapper" class="flex">
        <div class="main w-full bg-grey-50 text-grey-900 dark:bg-grey-900 dark:text-white flex flex-col h-screen">
            <div class="navbar navbar-1 border-b p-4">
                <div class="navbar-inner w-full flex items-center justify-start">
                    <button class="mx-4" onclick="window.history.go(-1);">
                        <x-icon.bars />
                    </button>
                </div>
            </div>

            <div class="w-full p-4 flex-1 overflow-y-auto">
                {{ $slot }}
            </div>
        </div>

        <x-notification />
    </div>
</x-base-layout>