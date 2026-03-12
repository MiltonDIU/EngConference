<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyScheduleRequest;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Schedule;
use App\Models\Speaker;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schedules = Schedule::all();

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        abort_if(Gate::denies('schedule_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $speakers = Speaker::all()->pluck('name', 'id');

        return view('admin.schedules.create', compact('speakers'));
    }

    public function store(StoreScheduleRequest $request)
    {
    $data =  $request->only('day_number', 'start_time', 'title', 'subtitle', 'is_workshop','total_seat','event_session');
    if ($request->input('is_workshop')=='1'){
        $benefits = [];
        foreach ($request->input('benefit_title') as $key => $title) {
            $benefits[] = [
                'title' => $title,
                'link' => $request->input('benefit_link')[$key],
            ];
        }
        $benefits = json_encode($benefits);
        $tools = json_encode($request->input('tools'));
        $data['benefits'] = $benefits;
        $data['tools'] = $tools;
        $data['about'] = $request->input('about');

    }
        $speakers = $request->input('speaker_id');
        $schedule = Schedule::create($data);
        $schedule->speakers()->sync($speakers);
        return redirect()->route('admin.schedules.index');
    }

    public function edit(Schedule $schedule)
    {
        abort_if(Gate::denies('schedule_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $speakers = Speaker::all()->pluck('name', 'id');

        $schedule->load('speaker');

        return view('admin.schedules.edit', compact('speakers', 'schedule'));
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {

        $data =  $request->only('day_number', 'start_time', 'title', 'subtitle','event_session', 'is_workshop','total_seat','is_active');

        if ($request->input('is_workshop')=='1'){
            $benefits = [];
            foreach ($request->input('benefit_title') as $key => $title) {
                $benefits[] = [
                    'title' => $title,
                    'link' => $request->input('benefit_link')[$key],
                ];
            }
            $benefits = json_encode($benefits);
            $tools = json_encode($request->input('tools'));
            $data['benefits'] = $benefits;
            $data['tools'] = $tools;
            $data['about'] = $request->input('about');

        }
        $speakers = $request->input('speaker_id');
//        $schedule = Schedule::create($data);
        $schedule->update($data);
        $schedule->speakers()->sync($speakers);



        return redirect()->route('admin.schedules.index');
    }

    public function show(Schedule $schedule)
    {
        abort_if(Gate::denies('schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schedule->load('speaker');

        return view('admin.schedules.show', compact('schedule'));
    }

    public function destroy(Schedule $schedule)
    {
        abort_if(Gate::denies('schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $schedule->delete();

        return back();
    }

    public function massDestroy(MassDestroyScheduleRequest $request)
    {
        Schedule::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
