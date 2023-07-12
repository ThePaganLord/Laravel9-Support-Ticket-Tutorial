<x-app-layout>
    <div class="max-w-xl mx-auto p-4 sm:p-12 lg:p-14">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b>Title: </b>#{{ $ticket->id }} - {{ $ticket->title }}
                </div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="ticket-info">
                        <p><b>Category: </b>{{ $ticket->category->name }}</p>
                        <p>
                            @if ($ticket->status === 'Open')
                            <b>Status:</b> <span class="label label-success">{{ $ticket->status }}</span>
                            @else
                            <b>Status:</b> <span class="label label-danger">{{ $ticket->status }}</span>
                            @endif
                        </p>
                        <p><b>Created on:</b> {{ $ticket->created_at->diffForHumans() }}</p>
                        <p><b>Created by:</b> {{ $ticket->user->name }}</p>
                    </div>
                    <hr>
                    <div class="ticket-info">
                        <p><b>Details:</b></p>
                        <p>{{ $ticket->message }}</p>
                    </div>
                </div>
            </div>
            <hr>
            @include('tickets.comments')
            <hr>
            @include('tickets.reply')
        </div>
    </div>
</x-app-layout>