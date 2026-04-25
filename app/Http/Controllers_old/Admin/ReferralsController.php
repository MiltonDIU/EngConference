<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyReferralRequest;
use App\Http\Requests\StoreReferralRequest;
use App\Http\Requests\UpdateReferralRequest;
use App\Models\Coupon;
use App\Models\Referral;
use App\Models\ReferralVisitor;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class ReferralsController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('referral_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $referrals = Referral::with(['media'])->get();

        return view('admin.referrals.index', compact('referrals'));
    }

    public function create()
    {
        abort_if(Gate::denies('referral_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $coupons  = Coupon::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.referrals.create',compact('coupons'));
    }

    public function store(StoreReferralRequest $request)
    {
        $data = $request->all();

        //$data['identification'] = hash('crc32b', $data['email'] . time());
        $data['identification'] = hash('crc32b', $data['email'] . microtime() . random_bytes(16)).time();
        $referral = Referral::create($data);

        if ($request->input('avatar', false)) {
            $referral->addMedia(storage_path('tmp/uploads/' . basename($request->input('avatar'))))->toMediaCollection('avatar');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $referral->id]);
        }

        return redirect()->route('admin.referrals.index');
    }

    public function edit(Referral $referral)
    {
        abort_if(Gate::denies('referral_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $coupons  = Coupon::pluck('title', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.referrals.edit', compact('referral','coupons'));
    }

    public function update(UpdateReferralRequest $request, Referral $referral)
    {
        $referral->update($request->all());

        if ($request->input('avatar', false)) {
            if (! $referral->avatar || $request->input('avatar') !== $referral->avatar->file_name) {
                if ($referral->avatar) {
                    $referral->avatar->delete();
                }
                $referral->addMedia(storage_path('tmp/uploads/' . basename($request->input('avatar'))))->toMediaCollection('avatar');
            }
        } elseif ($referral->avatar) {
            $referral->avatar->delete();
        }

        return redirect()->route('admin.referrals.index');
    }

    public function show(Referral $referral)
    {

        abort_if(Gate::denies('referral_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $total = count($referral->referralVisitor??[]);
        $totalRegistrations = $referral->referralVisitor()->where('user_id','!=',null)->get();
        $paid = 0;
        foreach ($totalRegistrations as $key=> $totalRegistration){
            $profile = $totalRegistration->user->profile;
            if($profile->payment_status == '1'){
                $paid++;
            }
        }



        $totalRegistrationCount = count($totalRegistrations);

        $unpaid = $totalRegistrationCount - $paid;
        $profiles = array();
        array_push($profiles, ['country' => 'Impression', 'value' => $total]);
        array_push($profiles, ['country' => 'Registraion', 'value' => $totalRegistrationCount]);
        array_push($profiles, ['country' => 'Paid', 'value' => $paid]);
        array_push($profiles, ['country' => 'Unpaid', 'value' => $unpaid]);
        return view('admin.referrals.show', compact('referral','total','profiles'));
    }

    public function destroy(Referral $referral)
    {
        abort_if(Gate::denies('referral_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $referral->delete();

        return back();
    }

    public function massDestroy(MassDestroyReferralRequest $request)
    {
        $referrals = Referral::find(request('ids'));

        foreach ($referrals as $referral) {
            $referral->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('referral_create') && Gate::denies('referral_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Referral();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
