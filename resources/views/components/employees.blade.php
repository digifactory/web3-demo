@if($hasVoted)
    <div class="alert alert-info text-center">
        Je hebt gestemd.
    </div>
@endif
<div class="row">
    @foreach($employees as $employee)
        <div class="col-12 col-md-6 mt-3 position-relative">
            <img src="/images/{{ $employee->image_url }}" class="img-fluid"/>
            <div class="border-top p-2 text-center">
                @if($hasVoted)
                    {{ $employee->name }}: {{ $employee->votes_count }} {{ $employee->votes_count === 1 ? 'stem' : 'stemmen' }}
                @else
                    <form action="{{ route('castVote') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-success">Stem op {{ $employee->name }}</button>
                        </div>
                    </form>
                @endif
            </div>
            @if($hasVoted && $vote->employee_id === $employee->id)
                <div class="alert alert-success small position-absolute top-50 start-50">Bedankt!</div>
            @endif
        </div>
    @endforeach
</div>