<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Notifications\TicketDeleted;
use App\Notifications\TicketStatusChanged;
use Illuminate\Auth\Access\Gate as AccessGate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::with('user')
            ->when(Auth::user()->role != 'admin', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })
            ->when(request()->has('priority'), function ($query) {
                $query->where('priority', request('priority'));
            })
            ->orderBy('created_at', 'desc')
            ->simplePaginate(5);

        return view('tickets.index', ['tickets' => $tickets]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Ticket::class);
        return view('tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Ticket::class);
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'string', 'max:255'],
        ]);

        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->priority = $request->priority;
        $ticket->user_id = Auth::user()->id;
        $ticket->save();
        return to_route('tickets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        return view('tickets.show', ['ticket' => $ticket, 'user' => Auth::user()]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        Gate::authorize('update', $ticket);
        return view('tickets.edit', ['ticket' => $ticket, 'user' => Auth::user()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'max:255'],
        ]);

        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->status = $request->status;
        $ticket->priority = $request->priority;
        $ticket->save();
        $user = $ticket->user;
        return redirect()->route('tickets.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);
        $user = $ticket->user;
        $ticket->delete();
        if (Auth::user()->isAdmin()) {
            $user->notify(new TicketDeleted($ticket));
        }
        return redirect()->route('tickets.index');
    }
}
