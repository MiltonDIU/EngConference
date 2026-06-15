<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="{{url('/')}}" class="brand-link">
        <span class="brand-text font-weight-light">{{ trans('panel.site_title') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route("admin.home") }}" class="nav-link">
                        <p>
                            <i class="fas fa-fw fa-tachometer-alt">

                            </i>
                            <span>{{ trans('global.dashboard') }}</span>
                        </p>
                    </a>
                </li>
                @can('user_management_access')
                    <li class="nav-item has-treeview {{ request()->is('admin/permissions*') ? 'menu-open' : '' }} {{ request()->is('admin/roles*') ? 'menu-open' : '' }} {{ request()->is('admin/users*') ? 'menu-open' : '' }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw fas fa-users">

                            </i>
                            <p>
                                <span>{{ trans('cruds.userManagement.title') }}</span>
                                <i class="right fa fa-fw fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-unlock-alt">

                                        </i>
                                        <p>
                                            <span>{{ trans('cruds.permission.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-briefcase">

                                        </i>
                                        <p>
                                            <span>{{ trans('cruds.role.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-user">

                                        </i>
                                        <p>
                                            <span>{{ trans('cruds.user.title') }}</span>
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
                @can('profile')
                    <li class="nav-item">
                        <a href="{{ route("show-profile") }}" class="nav-link {{ request()->is('admin/profile') || request()->is('admin/profile/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs">

                            </i>
                            <p>
                                <span>Profile</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('coupon')
                    <li class="nav-item">
                        <a href="{{ route("admin.coupon.index") }}" class="nav-link {{ request()->is('admin/coupon') || request()->is('admin/coupon/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs">

                            </i>
                            <p>
                                <span>Coupon</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('coupon')
                    <li class="nav-item">
                        <a href="{{ route("admin.domain.index") }}" class="nav-link {{ request()->is('admin/domain') || request()->is('admin/domain/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs">

                            </i>
                            <p>
                                <span>Domain</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('custom_email_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.custom-mail.index") }}" class="nav-link {{ request()->is('admin/custom-mail') || request()->is('admin/custom-mail/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs">

                            </i>
                            <p>
                                <span>Custom Mail</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('email_data_bank_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/data-bank-categories*") ? "menu-open" : "" }} {{ request()->is("admin/data-banks*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/data-bank-categories*") ? "active" : "" }} {{ request()->is("admin/data-banks*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.emailDataBank.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('data_bank_category_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.data-bank-categories.index") }}" class="nav-link {{ request()->is("admin/data-bank-categories") || request()->is("admin/data-bank-categories/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.dataBankCategory.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('data_bank_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.data-banks.index") }}" class="nav-link {{ request()->is("admin/data-banks") || request()->is("admin/data-banks/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.dataBank.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan



                @can('attendance_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.attendances.index") }}" class="nav-link {{ request()->is("admin/attendances") || request()->is("admin/attendance/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                              Attendances
                            </p>
                        </a>
                    </li>
                @endcan


                @can('event_activity_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.event-activities.index") }}" class="nav-link {{ request()->is("admin/event-activities") || request()->is("admin/event-activities/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.eventActivity.title') }}
                            </p>
                        </a>
                    </li>
                @endcan


                @can('referral_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.referrals.index") }}" class="nav-link {{ request()->is("admin/referrals") || request()->is("admin/referrals/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.referral.title') }}
                            </p>
                        </a>
                    </li>
                @endcan

                @can('setting_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.settings.index") }}" class="nav-link {{ request()->is('admin/settings') || request()->is('admin/settings/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-cogs">

                            </i>
                            <p>
                                <span>{{ trans('cruds.setting.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan

                @can('upload_medium_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.upload-media.index") }}" class="nav-link {{ request()->is("admin/upload-media") || request()->is("admin/upload-media/*") ? "active" : "" }}">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.uploadMedium.title') }}
                            </p>
                        </a>
                    </li>
                @endcan

                @can('blogs_post_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/blog-categories*") ? "menu-open" : "" }} {{ request()->is("admin/tags*") ? "menu-open" : "" }} {{ request()->is("admin/posts*") ? "menu-open" : "" }} {{ request()->is("admin/comments*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/blog-categories*") ? "active" : "" }} {{ request()->is("admin/tags*") ? "active" : "" }} {{ request()->is("admin/posts*") ? "active" : "" }} {{ request()->is("admin/comments*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.blogsPost.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('blog_category_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.blog-categories.index") }}" class="nav-link {{ request()->is("admin/blog-categories") || request()->is("admin/blog-categories/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.blogCategory.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('tag_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.tags.index") }}" class="nav-link {{ request()->is("admin/tags") || request()->is("admin/tags/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.tag.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('post_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.posts.index") }}" class="nav-link {{ request()->is("admin/posts") || request()->is("admin/posts/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.post.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('comment_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.comments.index") }}" class="nav-link {{ request()->is("admin/comments") || request()->is("admin/comments/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-cogs">

                                        </i>
                                        <p>
                                            {{ trans('cruds.comment.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan



                @can('events_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.events.index") }}" class="nav-link {{ request()->is('admin/events') || request()->is('admin/events/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-list">

                            </i>
                            <p>
                                <span>{{ trans('cruds.events.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('speaker_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.speakers.index") }}" class="nav-link {{ request()->is('admin/speakers') || request()->is('admin/speakers/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-users">

                            </i>
                            <p>
                                <span>{{ trans('cruds.speaker.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('paper_access')
                @if(!auth()->user()->roles->contains('id', 3) || (auth()->user()->profile && auth()->user()->profile->is_author))
                <li class="nav-item">
                    <a href="{{ route("papers.index") }}" class="nav-link {{ request()->is('papers*') ? 'active' : '' }}">
                        <i class="fa-fw fas fa-file-alt">

                        </i>
                        <p>
                            <span>Papers</span>
                        </p>
                    </a>
                </li>
                @endif
                @endcan
                @can('track_report')
                <li class="nav-item">
                    <a href="{{ route("admin.tracks-report") }}" class="nav-link {{ request()->is('admin/tracks-report*') ? 'active' : '' }}">
                        <i class="fa-fw fas fa-chart-pie">

                        </i>
                        <p>
                            <span>Tracks Report</span>
                        </p>
                    </a>
                </li>
                @endcan
                @can('schedule_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.schedules.index") }}" class="nav-link {{ request()->is('admin/schedules') || request()->is('admin/schedules/*') ? 'active' : '' }}">
                            <i class="fa-fw far fa-clock">

                            </i>
                            <p>
                                <span>{{ trans('cruds.schedule.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('venue_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.venues.index") }}" class="nav-link {{ request()->is('admin/venues') || request()->is('admin/venues/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-map-marker-alt">

                            </i>
                            <p>
                                <span>{{ trans('cruds.venue.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('hotel_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.hotels.index") }}" class="nav-link {{ request()->is('admin/hotels') || request()->is('admin/hotels/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-hotel">

                            </i>
                            <p>
                                <span>{{ trans('cruds.hotel.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('gallery_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.galleries.index") }}" class="nav-link {{ request()->is('admin/galleries') || request()->is('admin/galleries/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-images">

                            </i>
                            <p>
                                <span>{{ trans('cruds.gallery.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('sponsor_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.sponsors.index") }}" class="nav-link {{ request()->is('admin/sponsors') || request()->is('admin/sponsors/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-hand-holding-usd">

                            </i>
                            <p>
                                <span>{{ trans('cruds.sponsor.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('strategic_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.strategics.index") }}" class="nav-link {{ request()->is('admin/strategics') || request()->is('admin/strategics/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-hand-holding-usd">

                            </i>
                            <p>
                                <span>{{ trans('cruds.strategic.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('faq_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.faqs.index") }}" class="nav-link {{ request()->is('admin/faqs') || request()->is('admin/faqs/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-question">

                            </i>
                            <p>
                                <span>{{ trans('cruds.faq.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('amenity_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.amenities.index") }}" class="nav-link {{ request()->is('admin/amenities') || request()->is('admin/amenities/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-check">

                            </i>
                            <p>
                                <span>{{ trans('cruds.amenity.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan
                @can('price_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.prices.index") }}" class="nav-link {{ request()->is('admin/prices') || request()->is('admin/prices/*') ? 'active' : '' }}">
                            <i class="fa-fw fas fa-money-bill">

                            </i>
                            <p>
                                <span>{{ trans('cruds.price.title') }}</span>
                            </p>
                        </a>
                    </li>
                @endcan

                {{-- Committee Management --}}
                @if(Gate::check('committee_type_access') || Gate::check('committee_access') || Gate::check('conference_member_access'))
                    <li class="nav-item has-treeview {{ request()->is('admin/committee-types*') ? 'menu-open' : '' }} {{ request()->is('admin/committees*') ? 'menu-open' : '' }} {{ request()->is('admin/conference-members*') ? 'menu-open' : '' }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is('admin/committee-types*') ? 'active' : '' }} {{ request()->is('admin/committees*') ? 'active' : '' }} {{ request()->is('admin/conference-members*') ? 'active' : '' }}" href="#">
                            <i class="fa-fw nav-icon fas fa-sitemap"></i>
                            <p>
                                Committee Management
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('committee_type_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.committee-types.index') }}" class="nav-link {{ request()->is('admin/committee-types') || request()->is('admin/committee-types/*') ? 'active' : '' }}">
                                        <i class="fa-fw nav-icon fas fa-tags"></i>
                                        <p>Committee Types</p>
                                    </a>
                                </li>
                            @endcan
                            @can('committee_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.committees.index') }}" class="nav-link {{ request()->is('admin/committees') || request()->is('admin/committees/*') ? 'active' : '' }}">
                                        <i class="fa-fw nav-icon fas fa-users-cog"></i>
                                        <p>Committees</p>
                                    </a>
                                </li>
                            @endcan
                            @can('conference_member_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.conference-members.index') }}" class="nav-link {{ request()->is('admin/conference-members') || request()->is('admin/conference-members/*') ? 'active' : '' }}">
                                        <i class="fa-fw nav-icon fas fa-user-tie"></i>
                                        <p>Committee  Members </p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <p>
                            <i class="fas fa-fw fa-sign-out-alt">

                            </i>
                            <span>{{ trans('global.logout') }}</span>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
