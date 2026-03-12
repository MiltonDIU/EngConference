<div class="comment" id="comment{{$comment->id}}">

    <div class="who-comment">{{ $comment->user->name }}</div>
    <div class="comment-metadata"><strong>{{ $comment->created_at->format('d F, Y h:i A') }}</strong></div>
    <p>{!! $comment->comment !!}</p>
{{--    <span class="like-button">Like</span>--}}

    <span class="like-button {{ \App\Models\Like::likeCheck($comment->id)==true?'already-like' : '' }}"  data-post-id="{{ $comment->post_id }}" data-comment-id="{{ $comment->id }}">Like   <label class="like-count"
                                                                                                                                                                                                   data-post-id="{{ $comment->post_id }}"
                                                                                                                                                                                                   data-comment-id="{{ $comment->id }}">
        ({{ $comment->like != null ? count($comment->like) : 0 }})
    </label>
    </span>




    {{--    <span class="reply-button" onclick="toggleReplyForm(this)">Reply</span>--}}
    <span class="reply-button" data-comment-id="{{ $comment->id }}" onclick="toggleReplyForm(this)">Reply</span>
        <form class="reply-form" data-url="{{ route('submitReply') }}" style="display: none">
        @csrf
        <div class="row form-group {{ $errors->has('comment') ? 'has-error' : '' }}" >
            <div class="col-md-12 post-comments-container">
                <textarea name="comment" rows="2" class="form-control post-comments"></textarea>
                <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <button class="send-button" onclick="submitReplyForm(this)">&#10148;</button>
                @if($errors->has('comment'))
                    <p class="help-block">
                        {{ $errors->first('comment') }}
                    </p>
                @endif
            </div>
        </div>
    </form>
</div>
<div class="replies" id="replies{{$comment->id}}">
    @foreach ($comment->children as $reply)
        @include('main.partials.reply-form', ['comment' => $reply])
    @endforeach
</div>
