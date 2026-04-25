<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user()->roles->contains(3);
        $loged = Auth::user();
        if ($user === true){
            $coupons = Coupon::where('user_id',$loged->id)->orderBy('expire_date','desc')->get();
        }else{
            $coupons = Coupon::orderBy('user_id','asc')->orderBy('expire_date','desc')->get();
        }
        return view('admin.coupon.show-coupon',['coupons'=>$coupons]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.coupon.create-coupon');
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
            'title' => 'required',
            'value' => 'required',
//            'email' => 'required',
        ]);
        $randomNum=substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyzABCDEFGHIJKLMNOPQRSTVWXYZ"), 0, 8);
        $name = $request->title;
        $user = Auth::user();
        $coupon = new Coupon();
        $coupon->title  = $name;
        $coupon->value = $request->value;
        $coupon->expire_date = $request->expire_date;
        $coupon->email = $request->email;
        $coupon->user_id  = $user->id;
        $coupon->publication_status = $request->publication_status;
        $coupon->use_status = '0';
        $coupon->save();
        return redirect('/admin/coupon')->with('message','Coupon Create Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupon $coupon)
    {

        return view('admin.coupon.edit-coupon',compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'title' => 'required',
            'value' => 'required',
//            'email' => 'required',
        ]);
        $data =   $request->all();
        $data['']='0';
        $coupon->update($data);
        return redirect('/admin/coupon')->with('message','Coupon Create Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        abort_if(Gate::denies('coupon_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $coupon->delete();
        return back();
    }

    public function massDestroy(MassDestroyCouponRequest $request)
    {
        Coupon::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
