@php
    // Day 1 date; each later day follows on the next calendar day
    $scheduleStartDate = !empty($settings['schedule_start_date']) ? \Carbon\Carbon::parse($settings['schedule_start_date']) : null;
    $firstDay = $schedules->keys()->first();
@endphp
<style>
    #schedule .schedule-item time { font-weight: 600; }
    #schedule .schedule-speakers { float: left; }
    #schedule .nav-tabs .schedule-day-date { display: block; font-weight: normal; font-size: 12px; opacity: 0.85; }
</style>
<section id="schedule" class="section-with-bg">
    <div class="container wow fadeInUp">
        <div class="section-header">
            <h2>Event Schedule</h2>
            <p>Official programme rundown</p>
        </div>

        <ul class="nav nav-tabs" role="tablist">
            @foreach($schedules as $key => $day)
                <li class="nav-item">
                    <a class="nav-link{{ $key === $firstDay ? ' active' : '' }}" href="#day-{{ $key }}" role="tab" data-toggle="tab">
                        Day {{ $key }}
                        @if($scheduleStartDate)
                            <small class="schedule-day-date">{{ $scheduleStartDate->copy()->addDays($key - 1)->format('l, j M') }}</small>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>

        <h3 class="sub-heading">All times in Bangladesh Standard Time (UTC+6)</h3>

        <div class="tab-content row justify-content-center">
            @foreach($schedules as $key => $day)
                <div role="tabpanel" class="col-lg-9 tab-pane fade{{ $key === $firstDay ? ' show active' : '' }}" id="day-{{ $key }}">
                    @foreach($day as $schedule)
                        @php
                            $itemSpeakers = $schedule->speakers->count() ? $schedule->speakers : collect([$schedule->speaker])->filter();
                        @endphp
                        <div class="row schedule-item">
                            <div class="col-md-3">
                                <time>
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                                    @if($schedule->end_time)
                                        - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                    @endif
                                </time>
                            </div>
                            <div class="col-md-9">
                                @if($itemSpeakers->count())
                                    <div class="schedule-speakers">
                                        @foreach($itemSpeakers as $speaker)
                                            <div class="speaker" title="{{ $speaker->name }}">
                                                <img src="{{ $speaker->photo ? $speaker->photo->getUrl() : url('img/default-speaker.jpg') }}" alt="{{ $speaker->name }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <h4>
                                    @if($schedule->is_workshop == 1)
                                        <a href="{{ route('scheduleDetails', [$schedule->id, $schedule->title]) }}">{{ $schedule->title }} (Workshop)</a>
                                    @else
                                        {{ $schedule->title }}
                                    @endif
                                </h4>

                                @if($schedule->subtitle)
                                    <p>{{ $schedule->subtitle }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>
