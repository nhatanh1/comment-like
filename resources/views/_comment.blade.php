@inject('markdown', 'Parsedown')
@php
    // TODO: There should be a better place for this.
    $markdown->setSafeMode(true);
@endphp

<div id="comment-{{ $comment->getKey() }}" class="media flex">
    <img class="mr-3 rounded-full" src="https://as1.ftcdn.net/v2/jpg/03/46/83/96/1000_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg"
        alt="{{ $comment->commenter->name ?? $comment->guest_name }} Avatar" style="max-width: 50px; height: 50px">
    <div class="media-body grid gap-4 w-full">
        <div class="grid bg-slate-200 rounded-xl px-3 py-4 w-full gap-3">
            <h5 class="mt-0 mb-1 font-bold text-base">{{ $comment->commenter->name ?? $comment->guest_name }} <small
                    class="text-muted font-thin"> - {{ $comment->created_at->diffForHumans() }}</small></h5>
            <div style="white-space: pre-wrap;">{!! $markdown->line($comment->comment) !!}</div>
        </div>

        <div class="flex justify-between px-4">
            <div class="flex gap-3">
                @auth
                    <button class="rounded-2xl px-4 border-2 border-sky-400 hover:bg-slate-400 active:bg-sky-400"
                        id="like-{{ $comment->getKey() }}" value="0"
                        onclick="likeComment({{ $comment->getKey() }})">@lang('comments::comments.like')</button>
                @endauth
                @can('reply-to-comment', $comment)
                    <button data-toggle="modal" data-target="#reply-modal-{{ $comment->getKey() }}" id="reple-modal"
                        onclick="openModal('reply-modal-{{ $comment->getKey() }}')"
                        class="rounded-xl px-3  border-2 border-sky-400 hover:bg-sky-400">@lang('comments::comments.reply')</button>
                @endcan
                @can('edit-comment', $comment)
                    <button data-toggle="modal" data-target="#comment-modal-{{ $comment->getKey() }}" id="comment-modal"
                        onclick="openModal('comment-modal-{{ $comment->getKey() }}')"
                        class="rounded-xl px-3  border-2 border-sky-400 hover:bg-sky-400">@lang('comments::comments.edit')</button>
                @endcan
                @can('delete-comment', $comment)
                    <a href="{{ route('comments.destroy', $comment->getKey()) }}"
                        onclick="event.preventDefault();document.getElementById('comment-delete-form-{{ $comment->getKey() }}').submit();"
                        class="rounded-xl px-3  border-2 border-red-400 hover:bg-red-400">@lang('comments::comments.delete')</a>
                    <form id="comment-delete-form-{{ $comment->getKey() }}"
                        action="{{ route('comments.destroy', $comment->getKey()) }}" method="POST" style="display: none;">
                        @method('DELETE')
                        @csrf
                    </form>
                @endcan
            </div>

            <div class="-translate-y-7 bg-white border-2 border-sky-600 rounded-xl px-2 text-sky-600"
                data-bs-toggle="modal" data-bs-target="#exampleModalScrollable"
                onclick="userLike({{ $comment->getKey() }})">
                <span id="count-like-{{ $comment->getKey() }}"></span>
                <i class="fas fa-thumbs-up"></i>
            </div>
        </div>

        @can('edit-comment', $comment)
            <div class="w-full hidden" id="comment-modal-{{ $comment->getKey() }}"
                onkeypress="enter('comment-modal-{{ $comment->getKey() }}')" tabindex="-1" role="dialog">
                <form method="POST" action="{{ route('comments.update', $comment->getKey()) }} "
                    id="comment-modal-{{ $comment->getKey() }}">
                    @method('PUT')
                    @csrf
                    <div class="grid w-full gap-4 p-4">
                        <div class="modal-body">
                            <div class="form-group grid gap-2">
                                <label for="message">@lang('comments::comments.update_your_message_here')</label>
                                <textarea required class="form-control p-2 rounded bg-slate-200" name="message" rows="3">{{ $comment->comment }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" onclick="closeModal('comment-modal-{{ $comment->getKey() }}')"
                                class="btn btn-sm btn-outline-secondary text-uppercase uppercase border-2 py-1 px-3 rounded border-red-500 hover:bg-red-500"
                                data-dismiss="modal">@lang('comments::comments.cancel')</button>
                            <button type="submit" id="btn-edit-comment"
                                class="btn btn-sm btn-outline-success text-uppercase uppercase border-2 py-1 px-3 rounded border-sky-600 hover:bg-sky-600">@lang('comments::comments.update')</button>
                        </div>
                    </div>
                </form>
            </div>
        @endcan

        @can('reply-to-comment', $comment)
            <div class="hidden" id="reply-modal-{{ $comment->getKey() }}" tabindex="-1" role="dialog">
                <form method="POST" action="{{ route('comments.reply', $comment->getKey()) }}"
                    id="reply-modal-{{ $comment->getKey() }}">
                    @csrf
                    <div class="grid gap-4 lg:gap-4 rounded p-2">
                        {{-- <div class="modal-header flex justify-between">
                            <h5 class="modal-title font-bold text-lg">@lang('comments::comments.reply_to_comment')</h5>
                            <button type="button" class="close" data-dismiss="modal"
                                onclick="closeModal('reply-modal-{{ $comment->getKey() }}')">
                                <span>&times;</span>
                            </button>
                        </div> --}}
                        <div class="modal-body">
                            <div class="form-group grid gap-2">
                                <textarea required class="form-control bg-slate-200 rounded-lg p-4 border-2" name="message" rows="3"
                                    placeholder="@lang('comments::comments.reply_your_message_here')" onkeypress="enter('reply-modal-{{ $comment->getKey() }}')"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer text-right">
                            <button type="button" onclick="closeModal('reply-modal-{{ $comment->getKey() }}')"
                                class="btn btn-sm btn-outline-secondary text-uppercase uppercase border-2 border-red-500 px-3 py-1 rounded hover:bg-red-500"
                                data-dismiss="modal">@lang('comments::comments.cancel')</button>
                            <button type="submit"
                                class="btn btn-sm btn-outline-success text-uppercase uppercase border-2 border-sky-500 px-3 py-1 rounded hover:bg-sky-600">@lang('comments::comments.reply')</button>
                        </div>
                    </div>

                </form>

            </div>
        @endcan


        <br />{{-- Margin bottom --}}

        <?php
        if (!isset($indentationLevel)) {
            $indentationLevel = 1;
        } else {
            $indentationLevel++;
        }
        ?>

        {{-- Recursion for children --}}
        @if ($grouped_comments->has($comment->getKey()) && $indentationLevel <= $maxIndentationLevel)
            {{-- TODO: Don't repeat code. Extract to a new file and include it. --}}
            @foreach ($grouped_comments[$comment->getKey()] as $child)
                @include('comments::_comment', [
                    'comment' => $child,
                    'grouped_comments' => $grouped_comments,
                ])
            @endforeach
        @endif

    </div>
</div>

{{-- Recursion for children --}}
@if ($grouped_comments->has($comment->getKey()) && $indentationLevel > $maxIndentationLevel)
    {{-- TODO: Don't repeat code. Extract to a new file and include it. --}}
    @foreach ($grouped_comments[$comment->getKey()] as $child)
        @include('comments::_comment', [
            'comment' => $child,
            'grouped_comments' => $grouped_comments,
        ])
    @endforeach
@endif

<script>
    function likeComment(e) {
        if (document.getElementById('like-' + e).classList.contains('active')) {
            document.getElementById('like-' + e).classList.remove('active');
        } else {
            document.getElementById('like-' + e).classList.add('active');
        }
        // console.log(e);
        $.ajax({
            type: 'POST',
            url: '{{ route('like.store') }}',
            data: {
                comment_id: e,

                _token: "{{ csrf_token() }}",
            },
            success: function(data) {
                if (data > 0) {
                    document.getElementById('count-like-' + e).innerHTML = data;
                } else {
                    document.getElementById('count-like-' + e).innerHTML = '';
                }
            },
            error: function(error) {
                alert('loi like');
            }
        });
    }

    total_like();

    function total_like() {
        $.ajax({
            type: "GET",
            url: '{{ route('like.total', $comment->getKey()) }}',
            data: {},
            success: function(data) {
                if (data['count'] > 0) {
                    document.getElementById('count-like-' + data['comment_id']).innerHTML = data['count'];
                } else {
                    document.getElementById('count-like-' + data['comment_id']).innerHTML = '';
                }
            },
            error: function(error) {
                alert('loi dem like');
            }
        });
    }

    check_like();

    function check_like() {
        $.ajax({
            type: "GET",
            url: '{{ route('like.check', $comment->getKey()) }}',
            data: {},
            success: function(data) {
                if (data !== '') {
                    document.getElementById('like-' + data['comment_id']).classList.add('active');
                }
                // }
            },
            error: function(error) {
                alert('loi check');
            }
        });
    }

    function userLike(e) {
        $.ajax({
            type: 'GET',
            url: '/user-like/' + e,
            success: function(data) {
                var html = '';
                for (let index = 0; index < data.length; index++) {
                    const element = data[index];
                    html += '<div class="flex items-center mb-4">' +
                        '<img class="mr-3 rounded-full" src="https://as1.ftcdn.net/v2/jpg/03/46/83/96/1000_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg"' +
                        'alt=" Avatar" style="max-width: 50px; height: 50px">' +
                        '<h5 class="mt-0 mb-1 font-bold text-base">' + element['name'] + '</h5>' +
                        '</div>'
                }
                document.getElementById('modal-user-like').innerHTML = html;


            },
            error: function(error) {
                console.log(error);
            }
        });

    }

    function openModal(e) {
        document.getElementById(e).classList.remove("hidden");
    }

    function closeModal(e) {
        document.getElementById(e).classList.add("hidden");
    }
</script>
