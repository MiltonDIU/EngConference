<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitteeType;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommitteeTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('committee_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $committeeTypes = CommitteeType::all();
        return view('admin.committee_types.index', compact('committeeTypes'));
    }

    public function create()
    {
        abort_if(Gate::denies('committee_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.committee_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:committee_types',
        ]);

        CommitteeType::create($request->all());

        return redirect()->route('admin.committee-types.index')->with('success', 'Committee Type created successfully.');
    }

    public function edit(CommitteeType $committeeType)
    {
        abort_if(Gate::denies('committee_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.committee_types.edit', compact('committeeType'));
    }

    public function update(Request $request, CommitteeType $committeeType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:committee_types,name,' . $committeeType->id,
        ]);

        $committeeType->update($request->all());

        return redirect()->route('admin.committee-types.index')->with('success', 'Committee Type updated successfully.');
    }

    public function destroy(CommitteeType $committeeType)
    {
        abort_if(Gate::denies('committee_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $committeeType->delete();

        return redirect()->route('admin.committee-types.index')->with('success', 'Committee Type deleted successfully.');
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('committee_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        CommitteeType::whereIn('id', request('ids'))->delete();
        return response(null, 204);
    }
}
