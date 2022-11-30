@php
    // dd($model->id);
    if (isset($approved) and $approved == true) {
        $comments = $model->approvedComments;
    } else {
        $comments = $model->comments;
    }
@endphp
    @include('comments::_modal')
@auth
    @include('comments::_form')
@elseif(Config::get('comments.guest_commenting') == true)
    @include('comments::_form', [
        'guest_commenting' => true,
    ])
@else
    <div class="w-full border p-3 mb-10">
        <div class="grid gap-4">
            <h5 class="w-full text- text-xl border-b pb-3">@lang('comments::comments.authentication_required')</h5>
            <p class="text-warning">@lang('comments::comments.you_must_login_to_post_a_comment')</p>
            <div>
                <a href="{{ route('login') }}" class="bg-sky-500 px-4 py-2 rounded">@lang('comments::comments.log_in')</a>
            </div>
        </div>
    </div>
@endauth

@if ($comments->count() < 1)
    <div class="bg-orange-300 mb-6 p-2">@lang('comments::comments.there_are_no_comments')</div>
@endif

<div class="p-8">
    @php
        $comments = $comments->sortBy('created_at');
        
        if (isset($perPage)) {
            $page = request()->query('page', 1) - 1;
        
            $parentComments = $comments->where('child_id', '');
        
            $slicedParentComments = $parentComments->slice($page * $perPage, $perPage);
        
            $m = Config::get('comments.model'); // This has to be done like this, otherwise it will complain.
            $modelKeyName = (new $m())->getKeyName(); // This defaults to 'id' if not changed.
        
            $slicedParentCommentsIds = $slicedParentComments->pluck($modelKeyName)->toArray();
        
            // Remove parent Comments from comments.
            $comments = $comments->where('child_id', '!=', '');
        
            $grouped_comments = new \Illuminate\Pagination\LengthAwarePaginator($slicedParentComments->merge($comments)->groupBy('child_id'), $parentComments->count(), $perPage);
        
            $grouped_comments->withPath(request()->url());
        } else {
            $grouped_comments = $comments->groupBy('child_id');
        }
    @endphp
    @foreach ($grouped_comments as $comment_id => $comments)
        {{-- Process parent nodes --}}
        @if ($comment_id == '')
            @foreach ($comments as $comment)
                @include('comments::_comment', [
                    'comment' => $comment,
                    'grouped_comments' => $grouped_comments,
                    'maxIndentationLevel' => $maxIndentationLevel ?? 2,
                ])
            @endforeach
        @endif
    @endforeach
</div>

@isset($perPage)
    {{ $grouped_comments->links('pagination::default') }}
@endisset


<script>
    function enter(data) {
        if (event.keyCode === 13) {

            $('form#' + data).submit();
        }
    }
</script>
