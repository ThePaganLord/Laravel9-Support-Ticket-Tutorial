<div class="panel panel-default">
    <div class="panel-body">
        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
            <label for="category" class="col-md-4 control-label">Status</label>
            <div class="col-md-6">
                <select id="status" type="status" class="form-control" name="status">
                    <option value="">Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->name }}" @if ($ticket->status == $status->name) selected @endif >
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('category'))
                    <span class="help-block">
                        <strong>{{ $errors->first('status') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>