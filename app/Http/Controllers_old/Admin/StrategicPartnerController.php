<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyStrategicRequest;
use App\Http\Requests\StoreStrategicRequest;
use App\Http\Requests\UpdateStrategicRequest;
use App\Models\StrategicPartner;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StrategicPartnerController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('sponsor_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $strategics = StrategicPartner::all();

        return view('admin.strategic-partner.index', compact('strategics'));
    }

    public function create()
    {
        abort_if(Gate::denies('strategic_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.strategic-partner.create');
    }

    public function store(StoreStrategicRequest $request)
    {
        $strategic = StrategicPartner::create($request->all());

        if ($request->input('logo', false)) {
            $strategic->addMedia(storage_path('tmp/uploads/' . $request->input('logo')))->toMediaCollection('logo');
        }

        return redirect()->route('admin.strategics.index');
    }

    public function edit(StrategicPartner $strategic)
    {
        abort_if(Gate::denies('sponsor_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.strategic-partner.edit', compact('strategic'));
    }

    public function update(UpdateStrategicRequest $request, StrategicPartner $strategic)
    {
        $strategic->update($request->all());

        if ($request->input('logo', false)) {
            if (!$strategic->logo || $request->input('logo') !== $strategic->logo->file_name) {
                $strategic->addMedia(storage_path('tmp/uploads/' . $request->input('logo')))->toMediaCollection('logo');
            }
        } elseif ($strategic->logo) {
            $strategic->logo->delete();
        }

        return redirect()->route('admin.strategics.index');
    }

    public function show(StrategicPartner $strategic)
    {
        abort_if(Gate::denies('sponsors_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.strategic-partner.show', compact('strategic'));
    }

    public function destroy(StrategicPartner $strategic)
    {
        abort_if(Gate::denies('strategic_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $strategic->delete();

        return back();
    }

    public function massDestroy(MassDestroyStrategicRequest $request)
    {
        StrategicPartner::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
