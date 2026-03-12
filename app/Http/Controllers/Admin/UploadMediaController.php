<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyUploadMediumRequest;
use App\Http\Requests\StoreUploadMediumRequest;
use App\Http\Requests\UpdateUploadMediumRequest;
use App\Models\UploadMedium;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class UploadMediaController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('upload_medium_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $uploadMedia = UploadMedium::with(['media'])->get();

        return view('admin.uploadMedia.index', compact('uploadMedia'));
    }

    public function create()
    {
        abort_if(Gate::denies('upload_medium_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.uploadMedia.create');
    }

    public function store(StoreUploadMediumRequest $request)
    {
        $uploadMedium = UploadMedium::create($request->all());

        foreach ($request->input('file_name', []) as $file) {
            $uploadMedium->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('file_name');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $uploadMedium->id]);
        }

        return redirect()->route('admin.upload-media.index');
    }

    public function edit(UploadMedium $uploadMedium)
    {
        abort_if(Gate::denies('upload_medium_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.uploadMedia.edit', compact('uploadMedium'));
    }

    public function update(UpdateUploadMediumRequest $request, UploadMedium $uploadMedium)
    {
        $uploadMedium->update($request->all());

        if (count($uploadMedium->file_name) > 0) {
            foreach ($uploadMedium->file_name as $media) {
                if (! in_array($media->file_name, $request->input('file_name', []))) {
                    $media->delete();
                }
            }
        }
        $media = $uploadMedium->file_name->pluck('file_name')->toArray();
        foreach ($request->input('file_name', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $uploadMedium->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('file_name');
            }
        }

        return redirect()->route('admin.upload-media.index');
    }

    public function show(UploadMedium $uploadMedium)
    {
        abort_if(Gate::denies('upload_medium_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.uploadMedia.show', compact('uploadMedium'));
    }

    public function destroy(UploadMedium $uploadMedium)
    {
        abort_if(Gate::denies('upload_medium_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $uploadMedium->delete();

        return back();
    }

    public function massDestroy(MassDestroyUploadMediumRequest $request)
    {
        $uploadMedia = UploadMedium::find(request('ids'));

        foreach ($uploadMedia as $uploadMedium) {
            $uploadMedium->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('upload_medium_create') && Gate::denies('upload_medium_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new UploadMedium();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
