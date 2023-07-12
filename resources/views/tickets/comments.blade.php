<div class="comments">
    @if(isset($ticket->comment))
        @foreach($ticket->comment as $comments)
            <div class="panel panel-default">
                <div class="panel panel-@if($ticket->user->id === $comments->user_id){{"default"}}@else{{"success"}}@endif">
                    <div class="panel panel-heading">

                        <span class="pull-right"><i><b>{{$comments->user->name}}</b> : {{ $comments->created_at }}</i></span>
                    </div>
                    <div class="panel panel-body">
                        {{ $comments->comment }}
                    </div>
                </div>
            </div>
            <hr>
        @endforeach
    @endif
</div>