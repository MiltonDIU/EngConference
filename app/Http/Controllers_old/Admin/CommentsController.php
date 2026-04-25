<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCommentRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class CommentsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('comment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Comment::with(['post', 'user', 'parent'])->select(sprintf('%s.*', (new Comment)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'comment_show';
                $editGate      = 'comment_edit';
                $deleteGate    = 'comment_delete';
                $crudRoutePart = 'comments';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('comment', function ($row) {
                return $row->comment ? $row->comment : '';
            });
            $table->addColumn('post_title', function ($row) {
                return $row->post ? $row->post->title : '';
            });

            $table->addColumn('user_name', function ($row) {
                return $row->user ? $row->user->name : '';
            });

            $table->editColumn('is_active', function ($row) {
                return $row->is_active ? Comment::IS_ACTIVE_SELECT[$row->is_active] : '';
            });
            $table->addColumn('parent_comment', function ($row) {
                return $row->parent ? $row->parent->comment : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'post', 'user', 'parent']);

            return $table->make(true);
        }

        return view('admin.comments.index');
    }

    public function create()
    {
        abort_if(Gate::denies('comment_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $posts = Post::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $parents = Comment::pluck('comment', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.comments.create', compact('parents', 'posts', 'users'));
    }

    public function store(StoreCommentRequest $request)
    {
        $comment = Comment::create($request->all());

        return redirect()->route('admin.comments.index');
    }

    public function edit(Comment $comment)
    {
        abort_if(Gate::denies('comment_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $posts = Post::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $parents = Comment::pluck('comment', 'id')->prepend(trans('global.pleaseSelect'), '');

        $comment->load('post', 'user', 'parent');

        return view('admin.comments.edit', compact('comment', 'parents', 'posts', 'users'));
    }

    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $comment->update($request->all());

        return redirect()->route('admin.comments.index');
    }

    public function show(Comment $comment)
    {
        abort_if(Gate::denies('comment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $comment->load('post', 'user', 'parent');

        return view('admin.comments.show', compact('comment'));
    }

    public function destroy(Comment $comment)
    {
        abort_if(Gate::denies('comment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $comment->delete();

        return back();
    }

    public function massDestroy(MassDestroyCommentRequest $request)
    {
        $comments = Comment::find(request('ids'));
        foreach ($comments as $comment) {
            $comment->delete();
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }
    public function newComments(Request $request)
    {
        $data['comment'] = $request->input('comment');
        $data['post_id'] = $request->input('post_id');
//        $data['parent_id'] = $request->input('parent_id');
        $data['user_id'] = auth()->user()->id;
        $data['is_active'] ='1';
        $comment = Comment::create($data);
        $parent_id = $comment->parent_id;
        $formattedCreatedAt = Carbon::parse($comment->created_at)->format('d F, Y h:i A');
        $responseData['name'] = $comment->user->name;
        $responseData['comment'] = $comment->comment;
        $responseData['post_id'] = $comment->post_id;
        $responseData['parent_id'] = $comment->parent_id;
        $responseData['user_id'] = $comment->user_id;
        $responseData['created_at'] =$formattedCreatedAt;
        $responseData['id'] =$comment->id;
        $responseData['message'] = 'Your comments has been submitted and waiting for approval';
        return response()->json(['responseData' => $responseData]);
    }

    public function submitReply(Request $request)
    {
        $data['comment'] = $request->input('comment');
        $data['post_id'] = $request->input('post_id');
        $data['parent_id'] = $request->input('parent_id');
        $data['user_id'] = auth()->user()->id;
        $data['is_active'] ='1';
        $comment = Comment::create($data);
        $parent_id = $comment->parent_id;
        $formattedCreatedAt = Carbon::parse($comment->created_at)->format('d F, Y h:i A');
        $responseData['name'] = $comment->user->name;
        $responseData['comment'] = $comment->comment;
        $responseData['post_id'] = $comment->post_id;
        $responseData['parent_id'] = $comment->parent_id;
        $responseData['user_id'] = $comment->user_id;
        $responseData['created_at'] =$formattedCreatedAt;
        $responseData['id'] =$comment->id;
        $responseData['message'] = 'Your comments has been submitted and waiting for approval';

        return response()->json(['responseData' => $responseData]);
//        return redirect()->back();
    }
    public function like(Request $request)
    {
        $post_id = $request->input('post_id');
        $comment_id = $request->input('comment_id');
        $user_id = auth()->user()->id; // Assuming you're using authentication

        // Check if the user has already liked this comment
        $existingLike = Like::where([
            'post_id' => $post_id,
            'comment_id' => $comment_id,
            'user_id' => $user_id,
        ])->first();

        $totalLike = Like::where('comment_id', $comment_id)->count();

        if (!$existingLike) {
            // Add a new like
            Like::create([
                'post_id' => $post_id,
                'comment_id' => $comment_id,
                'user_id' => $user_id,
            ]);

            return response()->json(['success' => true, 'totalLike' => $totalLike+1]);
        }

        return response()->json(['success' => false, 'totalLike' => $totalLike]);



       // return 'hello bangladesh';
//        $data['comment_id'] = $request->input('comment_id');
//        $data['post_id'] = $request->input('post_id');
//        $data['user_id'] = auth()->user()->id;
//        $like = Like::create($data);
//        $responseData['message'] = 'Your Like has been succeeded!';
//        $responseData['total'] = Like::where('comment_id',$data['comment_id'])->count();
//        return response()->json(['responseData' => $responseData]);
//        return redirect()->back();
    }
}
