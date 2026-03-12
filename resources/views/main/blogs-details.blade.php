@extends('layouts.main')
@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>Blogs</h3>
                        <p>{{ $blog->title }}</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="content blogs margin-top-40">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 col-sm-12">
                        <div class="row blog-box-row no-margin-left no-margin-right no-padding no-background">
                            @if($blog->feature_image)
                                <div class="blog-image-box">
                                    <img src="{{ $blog->feature_image->getUrl() }}" alt="">
                                </div>
                            @endif
                            <div class="blog-title">
                                <a href="{{ route('blogDetails',[$blog->id,$blog->slug]) }}">   <h2><strong>
                                            {{ $blog->title }}
                                        </strong></h2></a>
                            </div>
                            <div class="blog-date-div">
                                <span class="blog-date">  <i class="fa fa-calendar"></i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('d F, Y h:i A') }}</span>
                                <span>  <i class="fa fa-eye"></i>Views:  {{ $blog->views }}</span>
                                {{--                                <span>  <i class="fa fa-share"></i>Share: 02</span>--}}
                            </div>
                            <div class="blog-content-div">
                                <p>   {!! App\Providers\AppServiceProvider::renderOembed($blog->details) !!} </p>
                            </div>
                        </div>
                        <div class="row blog-box-row no-margin-left no-margin-right no-padding no-background">
                            <h5 style="line-height: 20px; margin: 0px!important; font-weight: bold;"> Tags:</h5>

                            @foreach($blog->tags as $tag)
                                <a href="{{ route('tags',[$tag->id,$tag->slug]) }}">
                                    <span class="tag-list">   {{ $tag->name }}</span>
                                </a>
                            @endforeach
                        </div>


                        <div class="row blog-box-row no-margin-left no-margin-right no-padding no-background">


                            @auth
                                <div class="col-md-12 line">

                                    <form id="commentForm" data-url="{{ route('newComments') }}">
                                        @csrf
                                        <div class="row form-group {{ $errors->has('comments') ? 'has-error' : '' }}">
                                            <div class="col-md-12 post-comments-container">
                                                <label class="comment-label">Share your opinion</label>
                                                <textarea name="comment" rows="5" class="form-control post-comments"></textarea>
                                                <input type="hidden" name="post_id" value="{{ $blog->id }}">
                                                <input type="hidden" name="parent_id" value="null">
                                                <button class="send-button" id="submitComment">&#10148;</button>
                                                @if($errors->has('comments'))
                                                    <p class="help-block">
                                                        {{ $errors->first('comments') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </form>

                                </div>

                                <div class="col-md-12">
                                    <div class="comment-history" id="comment-history">
                                        @foreach($comments as $comment)
                                            @include('main.partials.reply-form', ['comment' => $comment])
                                        @endforeach
                                    </div>
                                </div>
                            @endauth

                            @guest
                                <!-- Content to show when user is not authenticated -->
                                <p>Share your opinion.   <a href="{{ route('login') }}">Login</a></p>

                            @endguest




                        </div>

                    </div>
                    <div class="col-md-4  col-lg-4 col-sm-12 no-background">
                        <div class="row">
                            <div class="blog-section-title">
                                <h1><strong>Top Articles</strong></h1>
                            </div>

                            @foreach($populers as $blog)
                                <div class="row sidebar-row">
                                    <div class="col-sm-12 col-md-5 col-lg-5 ">
                                        <div class="blog-image-box">
                                            @if($blog->feature_image)
                                                <img src="{{ $blog->feature_image->getUrl()  }}" alt="">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-7 col-lg-7 ">
                                        <div class="blog-sidebar-title">
                                            <a href="{{ route('blogDetails',[$blog->id,$blog->slug]) }}">   <h2>{{ $blog->title??"" }}</h2></a>
                                        </div>
                                        <div class="blog-date-div">
                                            <span class="blog-date">  <i class="fa fa-calendar"></i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('d F, Y h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="blog-section-title margin-top-20 ">
                                <h1><strong>Browse by Category</strong></h1>
                            </div>

                            @foreach($blogCategories as $blogCategory)
                                <div class=" col-sm-12 col-md-6 col-lg-6  browse-category-row">
                                    <div class="blog-image-category-box">
                                        <img  src="{{ $blogCategory->feature_image->getUrl('thumb') }}" alt="">
                                    </div>
                                    <div class="blog-image-category-content d-flex align-items-center">
                                        <a href="{{ route('blogsCategory',[$blogCategory->id,$blogCategory->slug]) }}">
                                            <span><strong>{{ $blogCategory->title }} ({{ ($blogCategory->post!=null)?count($blogCategory->post):0 }})</strong></span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </main>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ url('css/blog.css') }}">
@endpush

@push('script')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('commentForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission behavior

                // Get the form data
                const formData = new FormData(event.target);
                const url = event.target.getAttribute('data-url');

                // Create an XMLHttpRequest object
                const xhr = new XMLHttpRequest();

                // Configure the request
                xhr.open('POST', url, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                // Handle the response
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        const response = JSON.parse(xhr.responseText);
                        // Log the response to the console
                        console.log(response.responseData.message);

                        // Create a new comment element with the received data
                        const newComment = document.createElement('div');
                        newComment.className = 'comment';
                        newComment.innerHTML = `
                    <div class="who-comment">${response.responseData.name}</div>
                    <div class="comment-metadata"><strong>${response.responseData.created_at}</strong></div>
                    <p>${response.responseData.comment}</p>
                    <span class="like-button">Like</span>
                    <span class="reply-button" data-comment-id="${response.responseData.id}" onclick="toggleReplyForm(this)">Reply</span>
                    <form class="reply-form" data-url="{{ route('submitReply') }}" style="display: none">
                        @csrf
                        <div class="row form-group {{ $errors->has('comment') ? 'has-error' : '' }}">
                            <div class="col-md-12 post-comments-container">
                                <textarea name="comment" rows="2" class="form-control post-comments"></textarea>
                                <input type="hidden" name="post_id" value="${response.responseData.post_id}">
                                <input type="hidden" name="parent_id" value="${response.responseData.id}">
                                <button class="send-button" onclick="submitReplyForm(this)">&#10148;</button>
                                @if($errors->has('comment'))
                        <p class="help-block">
{{ $errors->first('comment') }}
                        </p>
@endif
                        </div>
                    </div>
                </form>
`;

                        // Append the new comment to the comment-history container
                        const commentHistory = document.getElementById('comment-history');
                        commentHistory.insertBefore(newComment, commentHistory.firstChild);

                        // Clear the reply form
                        const replyForm = event.target;
                        const replyTextarea = replyForm.querySelector('textarea[name="comment"]');
                        replyTextarea.value = '';

                    } else {
                        console.error('Error:', xhr.statusText);
                    }
                };

                xhr.onerror = function() {
                    console.error('Request failed');
                };

                // Send the request
                xhr.send(formData);
            });
        });

    </script>



    <script>


        document.addEventListener('DOMContentLoaded', function() {
            // Attach an event listener to dynamically generated reply buttons
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('send-button')) {
                    // If the clicked element is a send-button, call the submitReplyForm function
                    submitReplyForm(event.target);
                }
            });


            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('send-button')) {
                    var postId = $(this).data('post-id');
                    var commentId = $(this).data('comment-id');
                    console.log(postId);
                }
            });



            // Function to submit reply form via AJAX
            function submitReplyForm(button) {
                const replyForm = button.closest('.reply-form');
                const formData = new FormData(replyForm);
                const url = replyForm.getAttribute('data-url');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', url, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        // Handle the response, update the comment history, etc.
                        const response = JSON.parse(xhr.responseText);
                        // Update comment history, refresh, or other actions
                        var replies_id= "replies"+response.responseData.parent_id;
                        //window.location.reload();




                        // Create a new comment element with the received data
                        const newComment = document.createElement('div');
                        newComment.className = 'comment';
                        newComment.innerHTML = `
                    <div class="who-comment">${response.responseData.name}</div>
                    <div class="comment-metadata"><strong>${response.responseData.created_at}</strong></div>
                    <p>${response.responseData.comment}</p>
                    <span class="like-button">Like</span>
                    <span class="reply-button" data-comment-id="${response.responseData.parent_id}" onclick="toggleReplyForm(this)">Reply</span>
                    <form class="reply-form" data-url="{{ route('submitReply') }}" style="display: none">
                        @csrf
                        <div class="row form-group {{ $errors->has('comment') ? 'has-error' : '' }}">
                            <div class="col-md-12 post-comments-container">
                                <textarea name="comment" rows="2" class="form-control post-comments"></textarea>
                                <input type="hidden" name="post_id" value="${response.responseData.post_id}">
                                <input type="hidden" name="parent_id" value="${response.responseData.parent_id}">
                                <button class="send-button" onclick="submitReplyForm(this)">&#10148;</button>
                                @if($errors->has('comment'))
                        <p class="help-block">
{{ $errors->first('comment') }}
                        </p>
@endif
                        </div>
                    </div>
                </form>
`;

                        // Append the new comment to the comment-history container
                        const commentHistory = document.getElementById(replies_id);
                        commentHistory.insertBefore(newComment, commentHistory.firstChild);
                        // commentHistory.insertBefore(newComment, commentHistory.lastChild);

                        // Clear the reply form
                        const replyForm = event.target;
                        const replyTextarea = replyForm.querySelector('textarea[name="comment"]');
                        replyTextarea.value = '';
                    } else {
                        console.error('Error:', xhr.statusText);
                    }
                };

                xhr.onerror = function() {
                    console.error('Request failed');
                };

                xhr.send(formData);

                // Prevent the default click behavior
                event.preventDefault();
            }
        });





        function toggleReplyForm(replyButton) {
            var replyForm = replyButton.nextElementSibling;
            if (replyForm.classList.contains("reply-form")) {
                if (replyForm.style.display === "none") {
                    replyForm.style.display = "block";
                } else {
                    replyForm.style.display = "none";
                }
            }
        }
    </script>

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.like-button').click(function() {
                var postId = $(this).data('post-id');
                var commentId = $(this).data('comment-id');

                var likeCountElements = $('.like-count[data-post-id="' + postId + '"][data-comment-id="' + commentId + '"]');
                var likeButton = $(this);
                $.ajax({
                    type: 'POST',
                    url: '/likes', // Replace with your route URL
                    data: {
                        post_id: postId,
                        comment_id: commentId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        // Update UI or perform other actions
                        if (data.success) {
                            // Update total like count for each matching element
                            likeCountElements.each(function() {
                                $(this).text('( ' + data.totalLike + ' )');
                                $(this).addClass('already-like');
                                likeButton.addClass('already-like');
                            });
                        }
                   //  console.log(data.totalLike);

                    }
                });
            });
        });
    </script>



@endpush
