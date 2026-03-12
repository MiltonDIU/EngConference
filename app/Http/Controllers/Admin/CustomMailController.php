<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomMailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mails = CustomMail::all();
        return view('admin.custom-mail.show-custom-mail',['mails' => $mails]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.custom-mail.create-custom-mail');
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
            'subject' => 'required|string',
            'mail_body' => 'required',
            'publication_status' => 'required'
        ]);
        $user = Auth::user();
        $mail = new CustomMail();
        $mail->subject = $request->subject;
        $mail->mail_body = $request->mail_body;
        $mail->user_id = $user->id;
        $mail->publication_status = $request->publication_status;
        $mail->save();
        return redirect('admin/custom-mail')->with('message','Custom Mail create successfully');
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
    public function edit(CustomMail $customMail)
    {
        return view('admin.custom-mail.create-custom-mail',compact('customMail'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomMail $customMail)
    {
        $request->validate([
            'subject' => 'required|string',
            'mail_body' => 'required',
            'publication_status' => 'required'
        ]);
        $user = Auth::user();
        $data =   $request->all();
        $data['user_id'] = $user->id;
        $customMail->update($data);
        return redirect('/admin/custom-mail')->with('message','Custom Mail Update Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomMail $customMail)
    {
        $customMail->delete();
        return back()->with('message','Custom Email delete successfull');
    }
}
