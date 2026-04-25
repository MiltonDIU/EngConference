<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Models\Faq;
use App\Models\Hotel;
use App\Models\Price;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\Venue;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Gate;
class EventsController extends Controller
{
    use MediaUploadingTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('events_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $events = Event::all();

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('events_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $speakers = Speaker::pluck('name','id');
        $hotels = Hotel::pluck('name','id');
        $venues = Venue::pluck('name','id');
        $sponsors = Sponsor::pluck('name','id');
        $faqs = Faq::pluck('question','id');
        $prices = Price::pluck('name','id');
        return view('admin.events.create',compact('speakers','hotels','venues','faqs','prices','sponsors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
$data = $request->all();

        $event = Event::create($data);

        if ($request->input('bg', false)) {
            $event->addMedia(storage_path('tmp/uploads/' . $request->input('bg')))->toMediaCollection('photo');
        }

        $speaker_ids = $request->input('speaker_ids');
        $event->sponsors()->attach($speaker_ids);

        $hotel_ids = $request->input('hotel_ids');
        $event->hotels()->attach($hotel_ids);

        $venue_ids = $request->input('venue_ids');
        $event->venues()->attach($venue_ids);

        $sponsor_ids = $request->input('sponsor_ids');
        $event->sponsors()->attach($sponsor_ids);

        $faq_ids = $request->input('faq_ids');
        $event->faqs()->attach($faq_ids);

        $price_ids = $request->input('price_ids');
        $event->prices()->attach($price_ids);

        return redirect()->route('admin.events.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        abort_if(Gate::denies('events_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {

        abort_if(Gate::denies('events_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $speakers = Speaker::pluck('name','id');
        $hotels = Hotel::pluck('name','id');
        $venues = Venue::pluck('name','id');
        $sponsors = Sponsor::pluck('name','id');
        $faqs = Faq::pluck('question','id');
        $prices = Price::pluck('name','id');
        return view('admin.events.edit',compact('event','speakers','hotels','venues','faqs','prices','sponsors'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $event->update($request->all());

        if ($request->input('bg', false)) {
            if (!$event->logo || $request->input('bg') !== $event->logo->file_name) {
                $event->addMedia(storage_path('tmp/uploads/' . $request->input('bg')))->toMediaCollection('photo');
            }
        } elseif ($event->photo) {
            $event->photo->delete();
        }

    $speaker_ids = $request->input('speaker_ids');
    $event->sponsors()->sync($speaker_ids);

    $hotel_ids = $request->input('hotel_ids');
    $event->hotels()->sync($hotel_ids);

    $venue_ids = $request->input('venue_ids');
    $event->venues()->sync($venue_ids);

    $sponsor_ids = $request->input('sponsor_ids');
    $event->sponsors()->sync($sponsor_ids);

    $faq_ids = $request->input('faq_ids');
    $event->faqs()->sync($faq_ids);

    $price_ids = $request->input('price_ids');
    $event->prices()->sync($price_ids);

        return redirect()->route('admin.events.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event  $event)
    {
        abort_if(Gate::denies('events_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $event->delete();

        return back();
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('events_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Event::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
