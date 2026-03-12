<section id="schedule" class="section-with-bg">
    <div class="container wow fadeInUp">
        <div class="section-header">
            <h2>Event Schedule</h2>
            <p>Here is our event schedule</p>
        </div>

        <ul class="nav nav-tabs" role="tablist">
            @foreach($schedules as $key => $day)
                <li class="nav-item">
                    <a class="nav-link{{ $key === 1 ? ' active' : '' }}" href="#day-{{ $key }}" role="tab" data-toggle="tab">
                        {{--              @if( $key==1)--}}
                        {{--              {{ 'Technical Track'}}--}}
                        {{--              @endif--}}
                        {{--             @if( $key==2)--}}
                        {{--              {{ 'Business Track'}}--}}
                        {{--                @endif--}}
                        {{--                  @if( $key==3)--}}
                        {{--              {{ 'Career Track'}}--}}
                        {{--                @endif--}}
                        Day {{ $key }}
                    </a>
                </li>
            @endforeach
        </ul>

        <h3 class="sub-heading">
            Join us on November 18-19, 2023, at AI Connect Bangladesh Summit 2023! This two-day event will feature immersive workshops on AI applications across industries and hands-on AI technology exploration and exhibition.
{{--            --}}
{{--            The event is rescheduled for unavoidable circumstances. A new date will be announced soon. Stay Tuned!--}}
        </h3>

        <div class="tab-content row justify-content-center">
            @foreach($schedules as $key => $day)
                <div role="tabpanel" class="col-lg-9 tab-pane fade{{ $key === 1 ? ' show active' : '' }}" id="day-{{ $key }}">
                    @foreach($day as $schedule)
                        <div class="row schedule-item">
                            <div class="col-md-2"><time>{{ \Carbon\Carbon::parse($schedule->start_time)->format("h:i A") }}</time></div>
                            <div class="col-md-10">


                                {{--                @if($schedule->speaker)--}}
                                {{--                  <div class="speaker">--}}
                                {{--                    <img src="{{ $schedule->speaker->photo!=null?$schedule->speaker->photo->getUrl():'' }}" alt="{{ $schedule->speaker->name }}">--}}
                                {{--                  </div>--}}
                                {{--                @endif--}}
                                {{--                <h4>--}}
                                {{--                   @if($schedule->is_workshop==1)--}}
                                {{--                       <a href="{{ route('scheduleDetails',[$schedule->id,$schedule->title]) }}"> {{ $schedule->title }} (Workshop)</a>--}}

                                {{--                       @else--}}
                                {{--                        {{ $schedule->title }}--}}
                                {{--                   @endif--}}

                                {{--                       @if($schedule->speakers->count() > 0)--}}
                                {{--                           @foreach($schedule->speakers as $key => $item)--}}
                                {{--                               <span style="margin: 2px">{{ $item->name??"" }}</span>--}}
                                {{--                           @endforeach--}}
                                {{--                       @else--}}
                                {{--                           <span>{{ $schedule->speaker->name ?? '' }}</span>--}}

                                {{--                       @endif--}}


                                {{--                    @if($schedule->speaker)<span>{{ $schedule->speaker->name }}</span>@endif--}}
                                {{--                </h4>--}}
                                {{--                <p>{{ $schedule->subtitle }}</p>--}}
                                {{--                  --}}
                                {{--                 --}}



                                @if($schedule->speakers->count() >0)

                                    @if($schedule->speakers->count()==1)

                                        @foreach($schedule->speakers as $key => $speaker)
                                            <div class="speaker">
                                                <img src="{{ $speaker->photo!=null?$speaker->photo->getUrl():'' }}">
                                            </div>

                                            <h4>
                                                @if($schedule->is_workshop==1)
                                                    <a href="{{ route('scheduleDetails',[$schedule->id,$schedule->title]) }}"> {{ $schedule->title }} (Workshop)</a>
                                                @else
                                                    {{ $schedule->title }}
                                                @endif
                                                <span>{{ $speaker->name ?? '' }}</span>
                                            </h4>

                                            <p>   {{ $schedule->subtitle }}</p>
                                        @endforeach

                                    @else
                                        <div class="speaker">
                                            <img src="{{ url('img/default-speaker.jpg') }}">
                                        </div>

                                        <h4>
                                            @if($schedule->is_workshop==1)
                                                <a href="{{ route('scheduleDetails',[$schedule->id,$schedule->title]) }}"> {{ $schedule->title }} (Workshop)</a>
                                            @else
                                                {{ $schedule->title }}
                                            @endif
                                                @foreach($schedule->speakers as $key => $speaker)
                                            <span>{{ $speaker->name ?? '' }}</span>
                                                    @endforeach

                                        </h4>

                                        <p>   {{ $schedule->subtitle }}</p>


                                    @endif




                                @else

                                    @if($schedule->speaker)
                                        <div class="speaker">
                                            <img src="{{ $schedule->speaker->photo!=null?$schedule->speaker->photo->getUrl():'' }}">
                                        </div>

                                        <h4>
                                            @if($schedule->is_workshop==1)
                                                <a href="{{ route('scheduleDetails',[$schedule->id,$schedule->title]) }}"> {{ $schedule->title }} (Workshop)</a>
                                            @else
                                                {{ $schedule->title }}
                                            @endif
                                            <span>{{ $schedule->speaker->name ?? '' }}</span>
                                        </h4>

                                        <p>   {{ $schedule->subtitle }}</p>

                                    @endif
                                @endif


                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>
