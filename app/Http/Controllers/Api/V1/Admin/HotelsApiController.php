<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Hotel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Http\Resources\Admin\HotelResource;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class HotelsApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('hotel_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new HotelResource(Hotel::all());
    }

    public function store(StoreHotelRequest $request)
    {
        $hotel = Hotel::create($request->all());

        if ($request->input('photo', false)) {
            $hotel->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        return (new HotelResource($hotel))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Hotel $hotel)
    {
        abort_if(Gate::denies('hotel_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new HotelResource($hotel);
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel)
    {
        $hotel->update($request->all());

        if ($request->input('photo', false)) {
            if (!$hotel->photo || $request->input('photo') !== $hotel->photo->file_name) {
                $hotel->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }
        } elseif ($hotel->photo) {
            $hotel->photo->delete();
        }

        return (new HotelResource($hotel))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Hotel $hotel)
    {
        abort_if(Gate::denies('hotel_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $hotel->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
    
            public function career()
    {
 $jobs = [];

    $response = Http::get('https://studio.skill.jobs/api/company-specific-jobs/49fe-8742-5bebbc26b382/?company_slug=daffodil-international-university-jRuBTdMx&limit=1000');

    if ($response->successful()) {
        $allJobs = $response->json('results');

        $jobs = collect($allJobs)->filter(function ($job) {
            $endDate = Carbon::parse($job['end_date']);
            return $endDate->isToday() || $endDate->isFuture();
        })->values(); // Re-index the collection
    }

    return response()->json([
        'status' => 'success',
        'total' => count($jobs),
        'results' => $jobs
    ]);
    }
}
