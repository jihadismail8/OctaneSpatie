<div>
    <div class="px-4 mt-8 space-y-4">
        <form method="get">
            <input class="w-full p-2 border border-gray-300 border-solid md:w-1/4" type="text" placeholder="Search Users" wire:model="ObjectClass"/>
            <input class="w-full p-2 border border-gray-300 border-solid md:w-1/4" type="text" placeholder="Search Users" wire:model="field"/>
            <input class="w-full p-2 border border-gray-300 border-solid md:w-1/4" type="text" placeholder="Search Users" wire:model="Value"/>
        </form>
        <div wire:loading>Searching users...</div>
        <div wire:loading.remove>
        <!-- 
            notice that $term is available as a public 
            variable, even though it's not part of the 
            data array 
        -->
        @if ($Value == "")
            <div class="text-sm text-gray-500">
                Enter a Value to search for users.
            </div>
        @else
            @if(!isset($users))
                <div class="text-sm text-gray-500">
                    No matching result was found.
                </div>
            @else
                @foreach($users as $user)
                    <div>
                        <h3 class="text-lg text-gray-900 text-bold">{{$user}}</h3>
                    </div>
                @endforeach
            @endif
        @endif
        </div>
    </div>
</div>
