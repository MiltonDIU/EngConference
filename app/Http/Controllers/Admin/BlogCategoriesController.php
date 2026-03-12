<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyBlogCategoryRequest;
use App\Http\Requests\StoreBlogCategoryRequest;
use App\Http\Requests\UpdateBlogCategoryRequest;
use App\Models\BlogCategory;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BlogCategoriesController extends Controller
{
    use MediaUploadingTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('blog_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BlogCategory::with(['parent'])->select(sprintf('%s.*', (new BlogCategory)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'blog_category_show';
                $editGate      = 'blog_category_edit';
                $deleteGate    = 'blog_category_delete';
                $crudRoutePart = 'blog-categories';

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
            $table->editColumn('title', function ($row) {
                return $row->title ? $row->title : '';
            });
            $table->editColumn('slug', function ($row) {
                return $row->slug ? $row->slug : '';
            });
            $table->editColumn('feature_image', function ($row) {
                if ($photo = $row->feature_image) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('is_active', function ($row) {
                return $row->is_active ? BlogCategory::IS_ACTIVE_SELECT[$row->is_active] : '';
            });
            $table->editColumn('summary', function ($row) {
                return $row->summary ? $row->summary : '';
            });
            $table->addColumn('parent_title', function ($row) {
                return $row->parent ? $row->parent->title : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'feature_image', 'parent']);

            return $table->make(true);
        }

        return view('admin.blogCategories.index');
    }

    public function create()
    {
        abort_if(Gate::denies('blog_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parents = BlogCategory::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.blogCategories.create', compact('parents'));
    }

    public function store(StoreBlogCategoryRequest $request)
    {
        $blogCategory = BlogCategory::create($request->all());

        if ($request->input('feature_image', false)) {
            $blogCategory->addMedia(storage_path('tmp/uploads/' . basename($request->input('feature_image'))))->toMediaCollection('feature_image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $blogCategory->id]);
        }

        return redirect()->route('admin.blog-categories.index');
    }

    public function edit(BlogCategory $blogCategory)
    {
        abort_if(Gate::denies('blog_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parents = BlogCategory::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');

        $blogCategory->load('parent');

        return view('admin.blogCategories.edit', compact('blogCategory', 'parents'));
    }

    public function update(UpdateBlogCategoryRequest $request, BlogCategory $blogCategory)
    {
        $blogCategory->update($request->all());

        if ($request->input('feature_image', false)) {
            if (! $blogCategory->feature_image || $request->input('feature_image') !== $blogCategory->feature_image->file_name) {
                if ($blogCategory->feature_image) {
                    $blogCategory->feature_image->delete();
                }
                $blogCategory->addMedia(storage_path('tmp/uploads/' . basename($request->input('feature_image'))))->toMediaCollection('feature_image');
            }
        } elseif ($blogCategory->feature_image) {
            $blogCategory->feature_image->delete();
        }

        return redirect()->route('admin.blog-categories.index');
    }

    public function show(BlogCategory $blogCategory)
    {
        abort_if(Gate::denies('blog_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $blogCategory->load('parent');

        return view('admin.blogCategories.show', compact('blogCategory'));
    }

    public function destroy(BlogCategory $blogCategory)
    {
        abort_if(Gate::denies('blog_category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $blogCategory->delete();

        return back();
    }

    public function massDestroy(MassDestroyBlogCategoryRequest $request)
    {
        $blogCategories = BlogCategory::find(request('ids'));

        foreach ($blogCategories as $blogCategory) {
            $blogCategory->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('blog_category_create') && Gate::denies('blog_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new BlogCategory();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
