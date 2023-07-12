<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        $categories = Category::all();
        $tickets = Ticket::with('user')->latest()->paginate(10);
        return view('tickets.index', compact('tickets'), compact('categories'));
    }
    
    public function create(): View  
    {
        //return response ('create');
        $tickets = Ticket::with('user')->latest()->get();
        $categories = Category::all();
        return view('tickets.create', compact('tickets'), compact('categories'));
        
    }
    
    public function store(Request $request): RedirectResponse
    {
        //return response('store');
        $validated=$request->validate([
            'title' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'message' => 'required'
        ]);
        $ticket = new Ticket([
            'title' => $request->input('title'),
            'user_id' => auth()->user()->id,
            'category_id' => $request->input('category'),
            'priority' => $request->input('priority'),
            'message' => $request->input('message'),
            'status' => "Open"
        ]);
        $ticket->save();
        return redirect(route('tickets.index'));
    }
    
    public function userTickets()
    {
        // Set the relationships in Ticket and Category Models.
        // Then read using ::with and include user relationship and category relationship
        $tickets = Ticket::with('user', 'category')->where('user_id', auth()->user()->id)
            ->paginate(10);
        return view('tickets.user_tickets', compact('tickets'));
    }
    public function show($ticket_id): View
    {
        $ticket = Ticket::with('user', 'category','comment')->where('id', $ticket_id)->firstOrfail();
        $categories = Category::all();
        $statuses = Status::all();
        return view('tickets.show_ticket', compact('ticket'), compact('statuses'));
    }
    
    public function adminTickets()
    {
        // Set the relationships in Ticket and Category Models.
        // Then read using ::with and include user relationship and category relationship
        $tickets = Ticket::paginate(10);
        return view('admin.all_tickets', compact('tickets'));
    }

}
