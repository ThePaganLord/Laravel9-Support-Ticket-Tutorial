<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CommentController extends Controller
{
    public function postComment(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required'
        ]);
        $comment = Comment::create([
            'ticket_id' => $request->input('ticket_id'),
            'user_id' => auth()->user()->id,
            'comment' => $request->input('comment')
        ]);

        
        // Now read the ticket details and sent it back
        $ticket = Ticket::with('user', 'category','comment')->where('id', $request->input('ticket_id'))->firstOrfail();
        $statuses = Status::all();
        return view('tickets.show_ticket', compact('ticket'), compact('statuses'));
        
    }
    
    public function postAdminComment(Request $request)
    {

        $this->validate($request, [
            'comment' => 'required',
            'status' => 'required'
        ]);
        $comment = Comment::create([
            'ticket_id' => $request->input('ticket_id'),
            'user_id' => auth()->user()->id,
            'comment' => $request->input('comment')
        ]);
        
        // Get the ticket being updated...
        $ticket = Ticket::where('id', $request->ticket_id)->firstOrFail();
        // Set the ticket status to the Admin selected ticket status
        $ticket->status = $request->status;
        // Save the details!
        $ticket->save();

        
        // Now read the ticket details and sent it back
        $ticket = Ticket::with('user', 'category','comment')->where('id', $request->input('ticket_id'))->firstOrfail();
        $statuses = Status::all();
        return view('tickets.show_ticket', compact('ticket'), compact('statuses'));
        
    }
}
