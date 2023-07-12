<div class="panel panel-default">
    <div class="panel-heading">Add reply</div>
    <div class="panel-body">
        <div class="comment-form">
            @if(auth()->user()->is_admin == 1)
                <form action="{{ url('admincomment') }}" method="POST" class="form">
            @else                    
                <form action="{{ url('comment') }}" method="POST" class="form">
            @endif
                {!! csrf_field() !!}
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
                    <textarea rows="10" id="comment" class="form-control" name="comment"></textarea>
                    @if ($errors->has('comment'))
                        <span class="help-block">
                           <strong>{{ $errors->first('comment') }}</strong>
                        </span>
                    @endif
                </div>
                @if(auth()->user()->is_admin)
                    <div class="form-group">
                        <hr>
                        @include('admin.status')
                        <br>
                    </div>
                @endif
                <div class="form-group">
                    <button type="submit" class="button">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>