<div class="grid p-4">
    <div class="card-body">
        @if($errors->has('commentable_type'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('commentable_type') }}
            </div>
        @endif
        @if($errors->has('commentable_id'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('commentable_id') }}
            </div>
        @endif
        <form method="POST" action="{{ route('comments.store') }}" id="comment">
            @csrf
            @honeypot
            <input type="hidden" name="commentable_type" value="\{{ get_class($model) }}" />
            <input type="hidden" name="commentable_id" value="{{ $model->getKey() }}" />

            {{-- Guest commenting --}}
            @if(isset($guest_commenting) and $guest_commenting == true)
                <div class="form-group">
                    <label for="message">@lang('comments::comments.enter_your_name_here')</label>
                    <input type="text" class="form-control @if($errors->has('guest_name')) is-invalid @endif" name="guest_name" />
                    @error('guest_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="message">@lang('comments::comments.enter_your_email_here')</label>
                    <input type="email" class="form-control @if($errors->has('guest_email')) is-invalid @endif" name="guest_email" />
                    @error('guest_email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

            <div class="grid p-4 gap-4">
                {{-- <label for="message" class="font-black text-xl pl-2 border-l-4 border-red-600">@lang('comments::comments.enter_your_message_here')</label> --}}
                <textarea class="p-3 rounded border-2 @if($errors->has('message')) is-invalid @endif" name="message" rows="3" placeholder="@lang('comments::comments.write_comments')" onkeypress="enter('comment')"></textarea>
                {{-- <div class="invalid-feedback">
                    @lang('comments::comments.your_message_is_required')
                </div> --}}
                {{-- <small class="form-text text-muted">@lang('comments::comments.markdown_cheatsheet', ['url' => 'https://help.github.com/articles/basic-writing-and-formatting-syntax'])</small> --}}
            </div>
            <div class=" pl-4">
                <button type="submit" class="uppercase bg-sky-500 rounded px-3 py-2">@lang('comments::comments.submit')</button>

            </div>
        </form>
    </div>
</div>
<br />