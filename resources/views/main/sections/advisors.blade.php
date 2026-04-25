<section id="advisors"  class="wow section-with-bg">
    <div class="container wow fadeInUp">
        <div class="section-header">
            <h2>Academic Advisory Boards</h2>
            <p>Our esteemed board members provide strategic guidance and academic oversight.</p>
        </div>

        @php
            $filteredBoards = $advisoryBoards->filter(function($board) {
                return $board->members->count() > 0;
            });
        @endphp

        @if($filteredBoards->count() > 0)
            <ul class="nav nav-tabs custom-tabs mb-5 justify-content-center" id="advisorTabs" role="tablist">
                @foreach($filteredBoards as $board)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                           id="tab-{{ $board->id }}"
                           data-toggle="tab"
                           href="#content-{{ $board->id }}"
                           role="tab"
                           aria-controls="content-{{ $board->id }}"
                           aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                           {{ $board->name }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content custom-tab-content" id="advisorTabsContent">
                @foreach($filteredBoards as $board)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                         id="content-{{ $board->id }}"
                         role="tabpanel"
                         aria-labelledby="tab-{{ $board->id }}">

                        <div class="advisor-list-container mt-4">
                            <ul class="list-group list-group-flush shadow-sm rounded">
                                @foreach($board->members as $member)
                                    <li class="list-group-item advisor-list-item d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3">
                                        <div class="member-info">
                                            <h5 class="mb-1 font-weight-bold" style="color: #3971AE;">{{ $member->name }}</h5>
                                            <p class="mb-0 text-dark small">
                                                <span class="font-weight-bold">{{ $member->pivot->role }}</span> 
                                                {{ $member->designation }}, {{ $member->institution }}
                                            </p>
                                        </div>
                                        @if($member->profile_url)
                                            <div class="member-action mt-2 mt-md-0">
                                                <a href="{{ $member->profile_url }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill" style="border-color: #3971AE; color: #3971AE;">View Profile</a>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Previous Grid Design (Preserved for recovery) --}}
                        {{--
                        <div class="row mt-4">
                            @foreach($board->members as $member)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="advisor-item p-3 border rounded shadow-sm h-100 bg-white">
                                        <h4 class="mb-1 text-primary">{{ $member->name }}</h4>
                                        <p class="mb-1 font-weight-bold">{{ $member->pivot->role }}</p>
                                        <p class="mb-1 small text-muted">{{ $member->designation }}</p>
                                        <p class="mb-0 small text-muted">{{ $member->institution }}</p>
                                        @if($member->profile_url)
                                            <a href="{{ $member->profile_url }}" target="_blank" class="btn btn-sm btn-link pl-0 mt-2">View Profile</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        --}}

                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center">Will be updated soon.</p>
        @endif
    </div>
</section>

<style>
    .custom-tabs .nav-link {
        color: #0e1b4d;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 10px 25px;
        transition: all 0.3s ease;
    }
    .custom-tabs .nav-link:hover, .custom-tabs .nav-link.active {
        color: #3971AE;
        border-bottom-color: #3971AE;
        background: transparent;
    }
    .advisor-list-item {
        border-left: 5px solid transparent;
        transition: all 0.3s ease;
    }
    .advisor-list-item:hover {
        border-left-color: #3971AE;
        background-color: #f8f9fa;
        padding-left: 20px !important;
    }
    .member-action .btn-outline-primary:hover {
        background-color: #3971AE;
        color: #fff !important;
    }
    #advisors{ padding: 60px 0; }
</style>
