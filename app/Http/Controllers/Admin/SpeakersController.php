<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroySpeakerRequest;
use App\Http\Requests\StoreSpeakerRequest;
use App\Http\Requests\UpdateSpeakerRequest;
use App\Models\GuestCategory;
use App\Models\Speaker;
use App\Models\SpeakerType;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SpeakersController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('speaker_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $speakers = Speaker::all();

        return view('admin.speakers.index', compact('speakers'));
    }

    public function create()
    {
        abort_if(Gate::denies('speaker_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $speakerTypes = SpeakerType::where('publication_status',1)->get();
        $guestCategories = GuestCategory::where('publication_status',1)->get();
        return view('admin.speakers.create',compact('speakerTypes','guestCategories'));
    }

    public function store(StoreSpeakerRequest $request)
    {

        $speaker = Speaker::create($request->all());
        $gueatCategories = $request->input('guest_category_id');
        if ($request->input('photo', false)) {
            $speaker->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }
        if (!empty($gueatCategories)) {
            $speaker->syncGuestCategory($gueatCategories);
        }
        return redirect()->route('admin.speakers.index');
    }

    public function edit(Speaker $speaker)
    {
        abort_if(Gate::denies('speaker_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $speakerTypes = SpeakerType::where('publication_status',1)->get();
        $guestCategories = GuestCategory::where('publication_status',1)->get();
        $speakerGuestCategories = DB::table("guest_category_speaker")->where("guest_category_speaker.speaker_id",$speaker->id)
            ->pluck('guest_category_speaker.guest_category_id','guest_category_speaker.guest_category_id')
            ->all();
        return view('admin.speakers.edit', compact('speaker','speakerTypes','guestCategories','speakerGuestCategories'));
    }

    public function update(UpdateSpeakerRequest $request, Speaker $speaker)
    {
        $speaker->update($request->all());
        $gueatCategories = $request->input('guest_category_id');
        if ($request->input('photo', false)) {
            if (!$speaker->photo || $request->input('photo') !== $speaker->photo->file_name) {
                $speaker->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }
        } elseif ($speaker->photo) {
            $speaker->photo->delete();
        }
        if (!empty($gueatCategories)) {
            $speaker->syncGuestCategory($gueatCategories);
        }
        return redirect()->route('admin.speakers.index');
    }

    public function show(Speaker $speaker)
    {
        abort_if(Gate::denies('speaker_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.speakers.show', compact('speaker'));
    }

    public function destroy(Speaker $speaker)
    {
        abort_if(Gate::denies('speaker_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $speaker->delete();

        return back();
    }

    public function massDestroy(MassDestroySpeakerRequest $request)
    {
        Speaker::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
