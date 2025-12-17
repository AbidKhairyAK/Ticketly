<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TicketApiController extends Controller
{
    public function index($email)
    {
        $tickets = Ticket::with('user')
            ->whereHas('user', fn ($q) => $q->where('email', $email))
            ->orderBy('created_at', 'desc')
            ->get();
            
        return $tickets;
    }


    public function search($email, $query)
    {
        $tickets = Ticket::with('user')
            ->whereHas('user', fn ($q) => $q->where('email', $email))
            ->where(function ($q) use ($query) {
                $q->whereLike('title', "%$query%")
                    ->orWhereLike('description', "%$query%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return $tickets;
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'priority' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $request->email,
                'email' => $request->email,
                'role' => 'employee',
                'password' => Hash::make(Str::random()),
            ]);
        }


        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->priority = $request->priority;
        $ticket->status = 'open';
        $ticket->user_id = $user->id;
        $ticket->save();

        return $ticket;
    }


    public function update(Request $request)
    {
        $request->validate([
            'ticket_id' => ['required', 'integer'],
            'email' => ['required', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'string'],
        ]);

        $ticket = Ticket::where('id', $request->ticket_id)
            ->whereHas('user', fn ($q) => $q->where('email', $request->email))
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->priority = $request->priority;

        $ticket->save();

        return $ticket;
    }


    public function close(Request $request)
    {
        $request->validate([
            'ticket_id' => ['required', 'integer'],
            'email' => ['required', 'string'],
        ]);

        $ticket = Ticket::where('id', $request->ticket_id)
            ->whereHas('user', fn ($q) => $q->where('email', $request->email))
            ->first();
        
        if (!$ticket) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->status = 'closed';
        $ticket->save();

        return $ticket;
    }


    public function show(Ticket $ticket)
    {
        return $ticket;
    }
}
