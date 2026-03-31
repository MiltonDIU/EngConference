<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitteeType;
use Illuminate\Http\Request;

class CommitteeTypeController extends Controller
{
    public function index()
    {
        $committeeTypes = CommitteeType::all();
        return view('admin.committee_types.index', compact('committeeTypes'));
    }

    public function create()
    {
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
        $committeeType->delete();

        return redirect()->route('admin.committee-types.index')->with('success', 'Committee Type deleted successfully.');
    }

    public function massDestroy(Request $request)
    {
        CommitteeType::whereIn('id', request('ids'))->delete();
        return response(null, 204);
    }
}
