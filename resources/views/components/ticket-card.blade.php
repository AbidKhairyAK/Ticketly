@props(['ticket'])

<div class="flex flex-col bg-white shadow-sm hover:shadow-md p-4 border border-gray-300 rounded-lg transition-shadow duration-300">
    <a  href="{{ route('tickets.show', $ticket) }}" class="mb-2 font-bold text-gray-800 text-xl uppercase">{{ $ticket->title }}</a>
    <p class="mb-4 text-gray-600">{{ $ticket->description }}</p>

    <div class="flex justify-between items-center mb-4">
        <div class="flex space-x-2">
            @can('update', $ticket)
                <a href="{{ route('tickets.edit', $ticket) }}">
                    <button class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg font-bold text-white">Update</button>
                </a>
            @endcan

            @can('delete', $ticket)
                <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg font-bold text-white">Delete</button>
                </form>
            @endcan
        </div>

        <p class="text-gray-500 text-sm">{{ $ticket->created_at->diffForHumans() }}</p>
    </div>

    <div class="flex justify-between align-center">
        <p class="font-semibold text-gray-700">{{ $ticket->user->name }}</p>
        <p class="text-gray-700">{{ $ticket->status }}</p>
    </div>
</div>
