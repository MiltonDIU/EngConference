<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use App\Models\CommitteeType;
use App\Models\ConferenceMember;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommitteeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('committee_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $committees = Committee::with(['committeeType', 'parent'])->get();
        return view('admin.committees.index', compact('committees'));
    }

    public function create()
    {
        abort_if(Gate::denies('committee_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $committeeTypes = CommitteeType::pluck('name', 'id');
        $parentCommittees = $this->getGroupedParentCommittees();
        $members = ConferenceMember::all();

        return view('admin.committees.create', compact('committeeTypes', 'parentCommittees', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'committee_type_id' => 'required|exists:committee_types,id',
            'parent_id' => 'nullable|integer',
            'section' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'members' => 'nullable|array',
            'members.*.id' => 'required|exists:conference_members,id',
            'members.*.role' => 'nullable|string|max:255',
            'members.*.level' => 'nullable|string|max:255',
            'members.*.sort_order' => 'nullable|integer',
        ]);

        $committee = Committee::create($request->all());

        if ($request->has('members')) {
            $syncData = [];
            foreach ($request->members as $m) {
                $syncData[$m['id']] = [
                    'role' => $m['role'],
                    'level' => $m['level'],
                    'sort_order' => $m['sort_order'] ?? 0,
                ];
            }
            $committee->members()->sync($syncData);
        }

        return redirect()->route('admin.committees.index')->with('success', 'Committee created successfully.');
    }

    public function edit(Committee $committee)
    {
        abort_if(Gate::denies('committee_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $committeeTypes = CommitteeType::pluck('name', 'id');
        $parentCommittees = $this->getGroupedParentCommittees($committee->id);
        $members = ConferenceMember::all();

        $committee->load('members');

        return view('admin.committees.edit', compact('committee', 'committeeTypes', 'parentCommittees', 'members'));
    }

    public function update(Request $request, Committee $committee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'committee_type_id' => 'required|exists:committee_types,id',
            'parent_id' => 'nullable|integer',
            'section' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'members' => 'nullable|array',
            'members.*.id' => 'required|exists:conference_members,id',
            'members.*.role' => 'nullable|string|max:255',
            'members.*.level' => 'nullable|string|max:255',
            'members.*.sort_order' => 'nullable|integer',
        ]);

        $committee->update($request->all());

        if ($request->has('members')) {
            $syncData = [];
            foreach ($request->members as $m) {
                $syncData[$m['id']] = [
                    'role' => $m['role'],
                    'level' => $m['level'],
                    'sort_order' => $m['sort_order'] ?? 0,
                ];
            }
            $committee->members()->sync($syncData);
        } else {
            $committee->members()->sync([]);
        }

        return redirect()->route('admin.committees.index')->with('success', 'Committee updated successfully.');
    }

    public function destroy(Committee $committee)
    {
        abort_if(Gate::denies('committee_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $committee->delete();

        return redirect()->route('admin.committees.index')->with('success', 'Committee deleted successfully.');
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('committee_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Committee::whereIn('id', request('ids'))->delete();
        return response(null, 204);
    }

    private function getGroupedParentCommittees($excludeId = null)
    {
        $committeeTypes = CommitteeType::with(['committees' => function($query) use ($excludeId) {
            $query->where('parent_id', 0)->orderBy('sort_order', 'asc');
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }])->get();

        $groupedList = [];
        foreach ($committeeTypes as $type) {
            $options = [];
            foreach ($type->committees as $committee) {
                $options[$committee->id] = $committee->name;
            }
            if (!empty($options)) {
                $groupedList[$type->name] = $options;
            }
        }
        return $groupedList;
    }
}
