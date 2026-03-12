<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $domains = Domain::all();
        return view('admin.domain.show-domain', compact('domains'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.domain.create-domain');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'concern_name' => 'required',
            'domain_name' => 'required',
            'status' => 'required',
        ]);
        $user = Auth::user();
        $domain = new Domain();
        $domain->concern_name = $request->concern_name;
        $domain->domain_name = $request->domain_name;
        $domain->status = $request->status;
        $domain->user_id = $user->id;
        $domain->save();
        return redirect('admin/domain')->with('message','Domain create successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Domain $domain)
    {
        return view('admin.domain.edit-domain',compact('domain'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Domain $domain)
    {
        $request->validate([
            'concern_name' => 'required',
            'domain_name' => 'required',
            'status' => 'required',
        ]);
        $user = Auth::user();
        $data =   $request->all();
        $data['user_id'] = $user->id;
        $domain->update($data);
        return redirect('/admin/domain')->with('message','Coupon Update Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
