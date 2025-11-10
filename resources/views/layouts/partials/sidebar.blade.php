<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <a href="{{ url('index') }}" class="logo logo-normal">
            <img src="{{ asset('admin/logo-default.svg') }}" alt="Logo">
        </a>
        <a href="{{ url('index') }}" class="logo-small">
            <img src="{{ asset('admin/logo-icon.svg') }}" alt="Logo">
        </a>
        <a href="{{ url('index') }}" class="dark-logo">
            <img src="{{ asset('admin/logo-white.svg') }}" alt="Logo">
        </a>
    </div>

    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title"><span>MAIN MENU</span></li>
                <li>
                    <ul>

                <li>
                    <ul>
                        <li class="submenu">
                            <a href="#" class="{{ Request::is('tp-users*') ? 'active subdrop' : '' }}">
                                <i class="ti ti-users-group"></i><span>  Dashboard </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ url('admin-dashboard') }}"
                                        class="{{ Request::is('admin-dashboard') ? 'active' : '' }}"> Admin
                                        Dashboard</a></li>

                                <li><a href="{{ url('employee-dashboard') }}"
                                        class="{{ Request::is('employee-dashboard') ? 'active' : '' }}">Employee
                                        Dashboard</a></li>
                            </ul>
                        </li>







                        <li class="submenu">
                            <a href="#"
                                class="{{ Request::is('roles*', 'departments*', 'employees*') ? 'active subdrop' : '' }}">
                                <i class="ti ti-user-star"></i><span> Employees </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ url('roles') }}"
                                        class="{{ Request::is('roles*') ? 'active' : '' }}">Roles</a></li>
                                <li><a href="{{ url('departments') }}"
                                        class="{{ Request::is('departments*') ? 'active' : '' }}">Departments</a></li>
                                <li><a href="{{ url('employees') }}"
                                        class="{{ Request::is(patterns: 'employees*') ? 'active' : '' }}">Employees</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>


                <li class="menu-title"><span>Settings</span></li>
                <li>
                    <ul>
                        <li class="{{ Request::is('profile') ?  'active': '' }}">
                            <a href="{{ url('profile') }}">
                                <i class="ti ti-smart-home"></i><span>Profile</span>
                            </a>

                        </li>
                        <li class="{{ Request::is('master-equipment') ? 'active' : '' }}">
                            <a href="{{ url('master-equipment') }}">
                                <i class="ti ti-smart-home"></i><span>Master Equipment</span>
                            </a>

                        </li>
                        <li class="{{ Request::is('master-qualification') ? 'active' : '' }}">
                            <a href="{{ url('master-qualification') }}">
                                <i class="ti ti-smart-home"></i><span>Master Qualification</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('enduser-charge') ? 'active' : '' }}">
                            <a href="{{ url('enduser-charge') }}">
                                <i class="ti ti-smart-home"></i><span>End User Charge</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('enduser-additional-data') ? 'active' : '' }}">
                            <a href="{{ url('enduser-additional-data') }}">
                                <i class="ti ti-smart-home"></i><span>End User Additional Data</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title"><span>HRMS</span></li>
                <li>
                    <ul>
                        <li class="{{ Request::is('attendance') ? 'active' : '' }}">
                            <a href="{{ route('admin.attendance.index') }}">
                                <i class="ti ti-smart-home"></i><span>Attendance</span>
                            </a>

                        </li>
                        <ul>
                                    <li class="{{ Request::is('leaves') ? 'active' : '' }}">
                                        <a href="{{ route('admin.leaves.admin_index') }}">
                                            <i class="ti ti-smart-home"></i><span>Admin Leaves</span>
                                    </li>


                                </ul>
                        <li class="{{ Request::is('holidays') ? 'active' : '' }}">
                            <a href="{{ url('holidays') }}">
                                <i class="ti ti-smart-home"></i><span>Holidays</span>
                            </a>
                        </li>

                        <li class="{{ Request::is('leave-type') ? 'active' : '' }}">
                            <a href="{{ url('leave-type') }}">
                                <i class="ti ti-smart-home"></i><span>Leave Type</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('leaves') ? 'active' : '' }}">
                            <a href="{{ url('leaves') }}">
                                <i class="ti ti-smart-home"></i><span>Leaves</span>
                            </a>
                        </li>



                        {{-- <li class="submenu">
                            <a href="#" class="{{ Request::is('roles*','departments*', 'employees*') ? 'active subdrop' : '' }}">
                                <i class="ti ti-user-star"></i><span> Employees  </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{url('roles')}}"  class="{{ Request::is('roles*') ? 'active' : '' }}">Roles</a></li>
                                <li><a href="{{url('departments')}}"  class="{{ Request::is('departments*') ? 'active' : '' }}">Departments</a></li>
                                <li><a href="{{url('employees')}}"  class="{{ Request::is(patterns: 'employees*') ? 'active' : '' }}">Employees</a></li>
                            </ul>
                        </li> --}}
                    </ul>
                </li>



                <li class="menu-title"><span>END USERS</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="#" class="{{ Request::is('tp-users*') ? 'active subdrop' : '' }}">
                                <i class="ti ti-users-group"></i><span> TP Users </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ url('tp-users/all') }}"
                                        class="{{ Request::is('tp-users', 'tp-users/all') ? 'active' : '' }}"> All
                                        Applications</a></li>
                                <li><a href="{{ url('tp-users/pending') }}"
                                        class="{{ Request::is('tp-users/pending') ? 'active' : '' }}">Initial
                                        Submit</a></li>
                                <li><a href="{{ url('tp-users/correction-required') }}"
                                        class="{{ Request::is(patterns: 'tp-users/correction-required') ? 'active' : '' }}">Correction
                                        Requested</a></li>
                                <li><a href="{{ url('tp-users/resubmitted') }}"
                                        class="{{ Request::is(patterns: 'tp-users/resubmitted') ? 'active' : '' }}">Resubmitted</a>
                                </li>
                                <li><a href="{{ url('tp-users/verified') }}"
                                        class="{{ Request::is(patterns: 'tp-users/verified') ? 'active' : '' }}">Verified</a>
                                </li>
                                <li><a href="{{ url('tp-users/final-payment') }}"
                                        class="{{ Request::is(patterns: 'tp-users/final-payment') ? 'active' : '' }}">Final
                                        Submit</a></li>
                                <li><a href="{{ url('tp-users/approved') }}"
                                        class="{{ Request::is(patterns: 'tp-users/approved') ? 'active' : '' }}">Approved</a>
                                </li>
                                <li><a href="{{ url('tp-users/rejected') }}"
                                        class="{{ Request::is(patterns: 'tp-users/rejected') ? 'active' : '' }}">Rejected</a>
                                </li>
                                <li><a href="{{ url('tp-users/blacklisted') }}"
                                        class="{{ Request::is(patterns: 'tp-users/blacklisted') ? 'active' : '' }}">BlackListed</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
