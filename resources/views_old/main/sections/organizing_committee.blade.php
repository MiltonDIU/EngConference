<section id="organizers" class="wow fadeInUp">
    <div class="container wow fadeInUp">
        <div class="section-header">
            <h2>Organizing Committees</h2>
            <p>Our dedicated teams working behind the scenes to make this conference a success.</p>
        </div>

        @php
            $filteredOrganizers = $conferenceCommittees->filter(function($board) {
                return $board->subCommittees->count() > 0;
            });
        @endphp

        @if($filteredOrganizers->count() > 0)
            <ul class="nav nav-tabs custom-tabs mb-5 justify-content-center" id="organizerTabs" role="tablist">
                @foreach($filteredOrganizers as $board)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                           id="org-tab-{{ $board->id }}"
                           data-toggle="tab"
                           href="#org-content-{{ $board->id }}"
                           role="tab"
                           aria-controls="org-content-{{ $board->id }}"
                           aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                           {{ $board->name }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content custom-tab-content" id="organizerTabsContent">
                @foreach($filteredOrganizers as $board)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                         id="org-content-{{ $board->id }}"
                         role="tabpanel"
                         aria-labelledby="org-tab-{{ $board->id }}">

                        @foreach($board->subCommittees as $sub)
                            <div class="sub-committee-group mb-5">
                                <h3 class="mb-3 text-dark border-bottom pb-2" style="color: #3971AE !important; border-bottom-color: #3971AE !important;">{{ $sub->name }}</h3>
                                <div class="advisor-list-container">
                                    <ul class="list-group list-group-flush shadow-sm rounded">
                                        @foreach($sub->members as $member)
                                            <li class="list-group-item advisor-list-item d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3">
                                                <div class="member-info">
                                                    <h5 class="mb-1 font-weight-bold" style="color: #3971AE;">{{ $member->name }}</h5>
                                                    <p class="mb-0 text-dark small">
                                                        <span class="font-weight-bold">{{ $member->pivot->role }}</span> |
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
                            </div>
                        @endforeach

                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center">Will be updated soon.</p>
        @endif
    </div>
</section>
<style>
    #organizers{ padding: 60px 0; }
</style>
