<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyEventActivityRequest;
use App\Http\Requests\StoreEventActivityRequest;
use App\Http\Requests\UpdateEventActivityRequest;
use App\Models\EventActivity;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class EventActivitiesController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('event_activity_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $eventActivities = EventActivity::with(['media'])->get();

        return view('admin.eventActivities.index', compact('eventActivities'));
    }

    public function create()
    {
        abort_if(Gate::denies('event_activity_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.eventActivities.create');
    }

    public function store(StoreEventActivityRequest $request)
    {
        $eventActivity = EventActivity::create($request->all());

        if ($request->input('feature_image', false)) {
            $eventActivity->addMedia(storage_path('tmp/uploads/' . basename($request->input('feature_image'))))->toMediaCollection('feature_image');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $eventActivity->id]);
        }

        return redirect()->route('admin.event-activities.index');
    }

    public function edit(EventActivity $eventActivity)
    {
        abort_if(Gate::denies('event_activity_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.eventActivities.edit', compact('eventActivity'));
    }

    public function update(UpdateEventActivityRequest $request, EventActivity $eventActivity)
    {
        $eventActivity->update($request->all());

        if ($request->input('feature_image', false)) {
            if (! $eventActivity->feature_image || $request->input('feature_image') !== $eventActivity->feature_image->file_name) {
                if ($eventActivity->feature_image) {
                    $eventActivity->feature_image->delete();
                }
                $eventActivity->addMedia(storage_path('tmp/uploads/' . basename($request->input('feature_image'))))->toMediaCollection('feature_image');
            }
        } elseif ($eventActivity->feature_image) {
            $eventActivity->feature_image->delete();
        }

        return redirect()->route('admin.event-activities.index');
    }

    public function show(EventActivity $eventActivity)
    {
        abort_if(Gate::denies('event_activity_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.eventActivities.show', compact('eventActivity'));
    }

    public function destroy(EventActivity $eventActivity)
    {
        abort_if(Gate::denies('event_activity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $eventActivity->delete();

        return back();
    }

    public function massDestroy(MassDestroyEventActivityRequest $request)
    {
        $eventActivities = EventActivity::find(request('ids'));

        foreach ($eventActivities as $eventActivity) {
            $eventActivity->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('event_activity_create') && Gate::denies('event_activity_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new EventActivity();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
