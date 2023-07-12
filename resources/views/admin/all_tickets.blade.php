<x-app-layout>
    <div class="max-w-xl mx-auto p-4 sm:p-12 lg:p-14">
        <div class="panel-heading">
            <i class="fa fa-ticket"> All Tickets</i>
        </div>
        <div class="panel-body">
            @if($tickets->isEmpty())
                <p>You have not created any tickets.</p>
            @else
                <table id="display">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        
                        
                            <tr>
                                <td>
                                   {{$ticket->category->name}}
                                </td>
                                <td>
                                    <a href="{{ url('tickets/' . $ticket->id) }}">
                                        #{{ $ticket->id }} - {{ $ticket->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($ticket->status == "Open")
                                        <span class="label label-success">{{ $ticket->status }}</span>
                                    @else
                                        <span class="label label-danger">{{ $ticket->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $ticket->updated_at }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $tickets->render() }}
            @endif
        </div>
    </div>
</x-app-layout>