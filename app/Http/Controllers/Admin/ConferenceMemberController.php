<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Committee;
use App\Models\ConferenceMember;
use Illuminate\Http\Request;

class ConferenceMemberController extends Controller
{
    public function index()
    {
        $members = ConferenceMember::all();
        return view('admin.conference_members.index', compact('members'));
    }

    public function create()
    {
        $committees = $this->getHierarchicalCommittees();
        return view('admin.conference_members.create', compact('committees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'committees' => 'nullable|array',
            'committees.*.id' => 'required|exists:committees,id',
            'committees.*.role' => 'nullable|string|max:255',
            'committees.*.level' => 'nullable|string|max:255',
            'committees.*.sort_order' => 'nullable|integer',
        ]);

        $member = ConferenceMember::create($request->all());

        if ($request->has('committees')) {
            $syncData = [];
            foreach ($request->committees as $c) {
                $syncData[$c['id']] = [
                    'role' => $c['role'],
                    'level' => $c['level'],
                    'sort_order' => $c['sort_order'] ?? 0,
                ];
            }
            $member->committees()->sync($syncData);
        }

        return redirect()->route('admin.conference-members.index')->with('success', 'Member created successfully.');
    }

    public function edit(ConferenceMember $conferenceMember)
    {
        $committees = $this->getHierarchicalCommittees();
        $conferenceMember->load('committees');
        
        return view('admin.conference_members.edit', compact('conferenceMember', 'committees'));
    }

    public function update(Request $request, ConferenceMember $conferenceMember)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'committees' => 'nullable|array',
            'committees.*.id' => 'required|exists:committees,id',
            'committees.*.role' => 'nullable|string|max:255',
            'committees.*.level' => 'nullable|string|max:255',
            'committees.*.sort_order' => 'nullable|integer',
        ]);

        $conferenceMember->update($request->all());

        if ($request->has('committees')) {
            $syncData = [];
            foreach ($request->committees as $c) {
                $syncData[$c['id']] = [
                    'role' => $c['role'],
                    'level' => $c['level'],
                    'sort_order' => $c['sort_order'] ?? 0,
                ];
            }
            $conferenceMember->committees()->sync($syncData);
        } else {
            $conferenceMember->committees()->sync([]);
        }

        return redirect()->route('admin.conference-members.index')->with('success', 'Member updated successfully.');
    }

    public function destroy(ConferenceMember $conferenceMember)
    {
        $conferenceMember->delete();

        return redirect()->route('admin.conference-members.index')->with('success', 'Member deleted successfully.');
    }

    public function massDestroy(Request $request)
    {
        ConferenceMember::whereIn('id', request('ids'))->delete();
        return response(null, 204);
    }

    private function getHierarchicalCommittees()
    {
        $committeeTypes = \App\Models\CommitteeType::with(['committees' => function($query) {
            $query->orderBy('parent_id', 'asc')->orderBy('sort_order', 'asc');
        }])->get();

        $groupedList = [];
        foreach ($committeeTypes as $type) {
            $options = [];
            
            // Get root committees for this type
            $roots = $type->committees->where('parent_id', 0);
            
            foreach ($roots as $root) {
                $options[$root->id] = $root->name;
                
                // Get sub-committees for this root
                $subs = $type->committees->where('parent_id', $root->id);
                foreach ($subs as $sub) {
                    $options[$sub->id] = '-- ' . $sub->name;
                }
            }
            
            if (!empty($options)) {
                $groupedList[$type->name] = $options;
            }
        }
        
        return $groupedList;
    }
}
