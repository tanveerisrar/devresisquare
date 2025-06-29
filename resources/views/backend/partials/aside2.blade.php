<aside id="menu" class="sidebar bg-light sidebar">
    <ul class="list-unstyled components">
        <li class="sidebar-list-item submenu_wrapper">
            <a class="{{ request()->routeIs('backend.dashboard') ? 'active' : '' }}"
                href="{{ route('backend.dashboard') }}">
                <span class="icon_wrapper"><i class="fa-solid fa-tachometer-alt"></i>Dashboard</span>
            </a>
        </li>
        
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#propertiesSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ request()->routeIs('admin.properties.index') || request()->routeIs('admin.properties.soft_deleted') || request()->routeIs('admin.properties.create') ? 'true' : 'false' }} "
                class="dropdown-toggle {{ request()->routeIs('admin.properties.index') || request()->routeIs('admin.properties.quick') || request()->routeIs('admin.properties.soft_deleted') || request()->routeIs('admin.properties.create') ? 'active' : '' }}">
                <span class="icon_wrapper"><i class="fa-solid fa-building"></i>Properties</span>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="nav-second-level collapse list-unstyled {{ request()->routeIs('admin.properties.index') || request()->routeIs('admin.properties.quick') || request()->routeIs('admin.properties.soft_deleted') || request()->routeIs('admin.properties.create') ? 'show' : '' }}"
                id="propertiesSubmenu">
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ request()->routeIs('admin.properties.index') ? 'active' : '' }} @endslot
                    @slot('link') {{ route('admin.properties.index') }} @endslot
                    @slot('link_name') View Properties @endslot
                @endcomponent
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ request()->routeIs('admin.properties.quick') ? 'active' : '' }} @endslot
                    @slot('link') {{ route('admin.properties.quick') }} @endslot
                    @slot('link_name') Add Property @endslot
                @endcomponent
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ request()->routeIs('admin.properties.soft_deleted') ? 'active' : '' }} @endslot
                    @slot('link') {{ route('admin.properties.soft_deleted') }} @endslot
                    @slot('link_name') Deleted Properties @endslot
                @endcomponent
                {{-- <li class="sidebar-sub-list-item py-0 mb-0">
                    <a class="{{ request()->routeIs('admin.properties.index') ? 'active' : '' }}"
                        href="{{ route('admin.properties.index') }}">
                        View Properties
                    </a>
                </li>
                <li class="sidebar-sub-list-item py-0 mb-0">
                    <a class="{{ request()->routeIs('admin.properties.quick') ? 'active' : '' }}"
                        href="{{ route('admin.properties.quick') }}">
                        Add Property
                    </a>
                </li>
                <li class="sidebar-sub-list-item py-0 mb-0">
                    <a class="{{ request()->routeIs('admin.properties.soft_deleted') ? 'active' : '' }}"
                        href="{{ route('admin.properties.soft_deleted') }}">
                        Deleted Properties
                    </a>
                </li> --}}
            </ul>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#contactsSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ request()->routeIs('admin.contacts.index') || request()->routeIs('contacts.create') ? 'true' : 'false' }}"
                class="dropdown-toggle {{ request()->routeIs('admin.contacts.index') || request()->routeIs('contacts.create') ? 'active' : '' }}">
                <span class="icon_wrapper"><i class="fa-solid fa-address-book"></i>Contacts</span>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="nav-second-level collapse list-unstyled {{ request()->routeIs('admin.contacts.index') || request()->routeIs('contacts.create') ? 'show' : '' }}"
                id="contactsSubmenu">
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ request()->routeIs('admin.contacts.index') && !request()->has('category') ? 'active' : '' }} @endslot
                    @slot('link') {{ route('admin.contacts.index') }} @endslot
                    @slot('link_name') All @endslot
                @endcomponent
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ request()->category == 1 ? 'active' : '' }} @endslot
                    @slot('link') {{ route('admin.contacts.index', ['category' => 1]) }} @endslot
                    @slot('link_name') Owners @endslot
                @endcomponent
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ request()->category == 2 ? 'active' : '' }} @endslot
                    @slot('link') {{ route('admin.contacts.index', ['category' => 2]) }} @endslot
                    @slot('link_name') Property Managers @endslot
                @endcomponent
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ request()->category == 3 ? 'active' : '' }} @endslot
                    @slot('link') {{ route('admin.contacts.index', ['category' => 3]) }} @endslot
                    @slot('link_name') Tenants @endslot
                @endcomponent
{{--                 
                <li class="sidebar-sub-list-item py-0 mb-0">
                    <a href="{{ route('admin.contacts.index') }}"
                        class="{{ request()->routeIs('admin.contacts.index') && !request()->has('category') ? 'active' : '' }}">
                        All
                    </a>
                </li>
                <li class="sidebar-sub-list-item py-0 mb-0">
                    <a href="{{ route('admin.contacts.index', ['category' => 1]) }}"
                        class="{{ request()->category == 1 ? 'active' : '' }}">
                        Owners
                    </a>
                </li>
                <li class="sidebar-sub-list-item py-0 mb-0">
                    <a href="{{ route('admin.contacts.index', ['category' => 2]) }}"
                        class="{{ request()->category == 2 ? 'active' : '' }}">
                        Property Managers
                    </a>
                </li>
                <li class="sidebar-sub-list-item py-0 mb-0">
                    <a href="{{ route('admin.contacts.index', ['category' => 3]) }}"
                        class="{{ request()->category == 3 ? 'active' : '' }}">
                        Tenants
                    </a>
                </li>
                <li class="sidebar-sub-list-item py-0 mb-0">
                    <a href="{{ route('admin.contacts.index', ['category' => 4]) }}"
                        class="{{ request()->category == 4 ? 'active' : '' }}">
                        Landlords
                    </a>
                </li> --}}
            </ul>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <span class="icon_wrapper"><i class="fa-solid fa-home"></i>Tenancies</span>
            </a>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#repairSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ request()->routeIs('admin.property_repairs.*') ? 'true' : 'false' }}"
                class="dropdown-toggle {{ request()->routeIs('admin.property_repairs.*') ? 'active' : '' }}">
                <span class="icon_wrapper"><i class="fa-solid fa-building"></i>Repair</span>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="nav-second-level collapse list-unstyled {{ request()->routeIs('admin.property_repairs.*') ? 'show' : '' }}"
                id="repairSubmenu">
                <!-- Raise Repair Issue -->
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ request()->routeIs('admin.property_repairs.create') || request()->routeIs('admin.property_repairs.edit') ? 'active' : '' }} @endslot
                    @slot('link') {{ route('admin.property_repairs.create') }} @endslot
                    @slot('link_name') Raise Repair Issue @endslot
                @endcomponent
                {{-- <li class="sidebar-sub-list-item py-0 mb-0">
                    <a class="{{ request()->routeIs('admin.property_repairs.create') || request()->routeIs('admin.property_repairs.edit') ? 'active' : '' }}"
                        href="{{ route('admin.property_repairs.create') }}">
                        Raise Repair Issue
                    </a>
                </li> --}}

                <!-- Repair Issues Section -->
                <li class="sidebar-sub-list-item py-0 mb-0 submenu_wrapper">
                    <a href="#repairIssuesSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ request()->routeIs('admin.property_repairs.index') ? 'true' : 'false' }}"
                        class="dropdown-toggle {{ request()->routeIs('admin.property_repairs.index') || request()->routeIs('admin.property_repairs.show') ? 'active' : '' }}">
                        <span class="icon_wrapper">Repair Issues</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ request()->routeIs('admin.property_repairs.index') ? 'show' : '' }}"
                        id="repairIssuesSubmenu">

                        <!-- "All" Status Option -->
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ request()->fullUrl() === route('admin.property_repairs.index') ? 'active' : '' }} @endslot
                            @slot('link') {{ route('admin.property_repairs.index') }} @endslot
                            @slot('link_name') All @endslot
                        @endcomponent

                        {{-- <li class="sidebar-sub-list-item">
                            <a href="{{ route('admin.property_repairs.index') }}"
                                class="{{ request()->fullUrl() === route('admin.property_repairs.index') ? 'active' : '' }}">
                                All
                            </a>
                        </li> --}}


                        @php
                            $statuses = ['Pending', 'Reported', 'Under Process', 'Work Completed', 'Invoice Received', 'Invoice Paid', 'Closed'];
                            $currentStatus = request('status');
                        @endphp

                        @foreach($statuses as $status)
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ $currentStatus === $status ? 'active' : '' }} @endslot
                            @slot('link') {{ route('admin.property_repairs.index', ['status' => $status]) }} @endslot
                            @slot('link_name') {{ $status }}
                            @endslot
                        @endcomponent
                            {{-- <li class="sidebar-sub-sub-list-item">
                                <a href="{{ route('admin.property_repairs.index', ['status' => $status]) }}"
                                    class="{{ $currentStatus === $status ? 'active' : '' }}">
                                    {{ $status }}
                                </a>
                            </li> --}}
                        @endforeach
                    </ul>
                </li>
            </ul>
        </li>

        @php
            $invoiceStatuses = [
                'all' => 'All Invoices',
                'pending' => 'Pending Invoices',
                'paid' => 'Paid Invoices',
                'overdue' => 'Overdue Invoices',
                'cancelled' => 'Cancelled Invoices'
            ];
            $currentInvoiceStatus = request('status');
        @endphp

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#invoiceSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ request()->routeIs('admin.invoices.index') ? 'true' : 'false' }}"
                class="dropdown-toggle {{ request()->routeIs('admin.invoices.index') ? 'active' : '' }}">
                <span class="icon_wrapper"><i class="fas fa-file-invoice-dollar aiz-side-nav-icon"></i>Invoices</span>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="nav-second-level list-unstyled collapse {{ request()->routeIs('admin.invoices.index') ? 'show' : '' }}"
                id="invoiceSubmenu">
                @foreach($invoiceStatuses as $key => $status)
                    @component('components.backend.common.sidebar-sublink')
                        @slot('class') {{ $currentInvoiceStatus === $key ? 'active' : '' }} @endslot
                        @slot('link') {{ route('admin.invoices.index', ['status' => $key]) }} @endslot
                        @slot('link_name') {{ $status }}
                        @endslot
                    @endcomponent
                    {{-- <li class="sidebar-sub-sub-list-item">
                        <a href="{{ route('admin.invoices.index', ['status' => $key]) }}"
                            class="{{ $currentInvoiceStatus === $key ? 'active' : '' }}">
                            {{ $status }}
                        </a>
                    </li> --}}
                @endforeach
            </ul>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <span class="icon_wrapper"><i class="fa-solid fa-file-alt"></i>Documents</span>
            </a>
        </li>
        <hr>

        <!-- Website Setup -->
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#websiteSetupSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ areActiveRoutes(['website.footer', 'website.header', 'website.appearance'], 'true') }}"
                class="dropdown-toggle {{ areActiveRoutes(['website.footer', 'website.header', 'website.appearance']) }}">
                {{-- <i class="las la-desktop aiz-side-nav-icon"></i> --}}
                <span class="icon_wrapper pb_25"><i class="fa-solid fa-cog"></i>Website Setup</span>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="nav-second-level list-unstyled collapse {{ areActiveRoutes(['website.footer', 'website.header', 'website.appearance'], 'show') }}"
                id="websiteSetupSubmenu">
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ areActiveRoutes(['website.header']) }} @endslot
                    @slot('link') {{ route('website.header') }} @endslot
                    @slot('link_name') Header
                    @endslot
                @endcomponent
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ areActiveRoutes(['website.footer']) }} @endslot
                    @slot('link') {{ route('website.footer') }} @endslot
                    @slot('link_name') Footer
                    @endslot
                @endcomponent
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ areActiveRoutes(['website.appearance']) }} @endslot
                    @slot('link') {{ route('website.appearance') }} @endslot
                    @slot('link_name') Appearance
                    @endslot
                @endcomponent
                {{-- <li class="sidebar-sub-list-item py-0 mb-0">
                    <a href="{{ route('website.header') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['website.header']) }}">
                        <span class="aiz-side-nav-text">Header</span>
                    </a>
                </li>
                <li class="sidebar-sub-list-item py-0 mb-0">
                    <a href="{{ route('website.footer') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['website.footer']) }}">
                        <span class="aiz-side-nav-text">Footer</span>
                    </a>
                </li>
                <li class="sidebar-sub-list-item py-0 mb-0">
                    <a href="{{ route('website.appearance') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['website.appearance']) }}">
                        <span class="aiz-side-nav-text">Appearance</span>
                    </a>
                </li> --}}
            </ul>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#masterManageSubmenu" data-bs-toggle="collapse" aria-expanded="{{ areActiveRoutes([
                'admin.branches.index',
                'admin.designations.index',
                'admin.note-types.index',
                'admin.note-types.create',
                'admin.document-types.index',
                'admin.document-types.create',
                'admin.tenancy_types.index',
                'admin.tenancy_types.create',
                'admin.tenancy_sub_statuses.index',
                'admin.tenancy_sub_statuses.create',
                'admin.job_types.index',
                'admin.job_types.create'
                ], 'true') }}" class="dropdown-toggle {{ areActiveRoutes([
                    'admin.branches.index',
                    'admin.designations.index',
                    'admin.tenancy_types.index',
                    'admin.tenancy_types.create',
                    'admin.tenancy_sub_statuses.index',
                    'admin.tenancy_sub_statuses.create',
                    'admin.job_types.index',
                    'admin.job_types.create'
                ]) }}">
                <span class="icon_wrapper pb_25"><i class="fa-solid fa-cogs"></i>Master Manage</span>
                <i class="fa fa-angle-down"></i>
            </a>
            <ul class="nav-second-level list-unstyled collapse {{ areActiveRoutes([
                'contact-categories.index',
                'admin.branches.index',
                'admin.designations.index',
                'admin.note-types.index',
                'admin.note-types.create',
                'admin.document-types.index',
                'admin.document-types.create',
                'admin.tenancy_types.index',
                'admin.tenancy_types.create',
                'admin.tenancy_sub_statuses.index',
                'admin.tenancy_sub_statuses.create',
                'admin.job_types.index',
                'admin.job_types.create'
            ], 'show') }}" id="masterManageSubmenu">

                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ areActiveRoutes(['contact-categories.index']) }} @endslot
                    @slot('link') {{ route('contact-categories.index') }} @endslot
                    @slot('link_name') Categories
                    @endslot
                @endcomponent
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ areActiveRoutes(['admin.branches.index']) }} @endslot
                    @slot('link') {{ route('admin.branches.index') }} @endslot
                    @slot('link_name') Branches
                    @endslot
                @endcomponent
                @component('components.backend.common.sidebar-sublink')
                    @slot('class') {{ areActiveRoutes(['admin.designations.index']) }} @endslot
                    @slot('link') {{ route('admin.designations.index') }} @endslot
                    @slot('link_name') Designation
                    @endslot
                @endcomponent
                {{-- <li class="sidebar-sub-sub-list-item submenu_wrapper">
                    <a class="{{ areActiveRoutes(['contact-categories.index']) }}"
                        href="{{ route('contact-categories.index') }}">
                        Categories
                    </a>
                </li>

                <li class="sidebar-sub-sub-list-item submenu_wrapper">
                    <a class="{{ areActiveRoutes(['admin.branches.index']) }}"
                        href="{{ route('admin.branches.index') }}">
                        Branches
                    </a>
                </li>

                <li class="sidebar-sub-sub-list-item submenu_wrapper">
                    <a class="{{ areActiveRoutes(['admin.designations.index']) }}"
                        href="{{ route('admin.designations.index') }}">
                        Designation
                    </a>
                </li> --}}
                <!-- Note Types Section -->
                <li class="sidebar-sub-list-item submenu_wrapper">
                    <a href="#noteTypesSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ areActiveRoutes(['admin.note-types.index', 'admin.note-types.create'], 'true') }}"
                        class="dropdown-toggle {{ areActiveRoutes(['admin.note-types.index', 'admin.note-types.create']) }}">
                        
                        <span class="icon_wrapper">Note Types</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ areActiveRoutes(['admin.note-types.index', 'admin.note-types.create'], 'show') }}"
                        id="noteTypesSubmenu">
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ areActiveRoutes(['admin.note-types.index']) }} @endslot
                            @slot('link') {{ route('admin.note-types.index') }} @endslot
                            @slot('link_name') View All
                            @endslot
                        @endcomponent
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ areActiveRoutes(['admin.note-types.create']) }} @endslot
                            @slot('link') {{ route('admin.note-types.create') }} @endslot
                            @slot('link_name') Add
                            @endslot
                        @endcomponent
                        {{-- <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.note-types.index']) }}"
                                href="{{ route('admin.note-types.index') }}">
                                View All
                            </a>
                        </li>
                        <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.note-types.create']) }}"
                                href="{{ route('admin.note-types.create') }}">
                                Add
                            </a>
                        </li> --}}
                    </ul>
                </li>
                <!-- Document Types Section -->
                <li class="sidebar-sub-list-item submenu_wrapper">
                    <a href="#documentTypesSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ areActiveRoutes(['admin.document-types.index', 'admin.document-types.create'], 'true') }}"
                        class="dropdown-toggle {{ areActiveRoutes(['admin.document-types.index', 'admin.document-types.create']) }}">
                        
                        <span class="icon_wrapper">Document Types</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ areActiveRoutes(['admin.document-types.index', 'admin.document-types.create'], 'show') }}"
                        id="documentTypesSubmenu">
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ areActiveRoutes(['admin.document-types.index']) }} @endslot
                            @slot('link') {{ route('admin.document-types.index') }} @endslot
                            @slot('link_name') View All
                            @endslot
                        @endcomponent
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ areActiveRoutes(['admin.document-types.create']) }} @endslot
                            @slot('link') {{ route('admin.document-types.create') }} @endslot
                            @slot('link_name') Add
                            @endslot
                        @endcomponent
                        {{-- <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.document-types.index']) }}"
                                href="{{ route('admin.document-types.index') }}">
                                View All
                            </a>
                        </li>
                        <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.document-types.create']) }}"
                                href="{{ route('admin.document-types.create') }}">
                                Add
                            </a>
                        </li> --}}
                    </ul>
                </li>

                <!-- Tenancy Types Section -->
                <li class="sidebar-sub-list-item submenu_wrapper">
                    <a href="#tenancyTypesSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ areActiveRoutes(['admin.tenancy_types.index', 'admin.tenancy_types.create'], 'true') }}"
                        class="dropdown-toggle {{ areActiveRoutes(['admin.tenancy_types.index', 'admin.tenancy_types.create']) }}">

                        <span class="icon_wrapper">Tenancy Types</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ areActiveRoutes(['admin.tenancy_types.index', 'admin.tenancy_types.create'], 'show') }}"
                        id="tenancyTypesSubmenu">
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ areActiveRoutes(['admin.tenancy_types.index']) }} @endslot
                            @slot('link') {{ route('admin.tenancy_types.index') }} @endslot
                            @slot('link_name') View All
                            @endslot
                        @endcomponent
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ areActiveRoutes(['admin.tenancy_types.create']) }} @endslot
                            @slot('link') {{ route('admin.tenancy_types.create') }} @endslot
                            @slot('link_name') Add
                            @endslot
                        @endcomponent
                        {{-- <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.tenancy_types.index']) }}"
                                href="{{ route('admin.tenancy_types.index') }}">
                                View All
                            </a>
                        </li>
                        <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.tenancy_types.create']) }}"
                                href="{{ route('admin.tenancy_types.create') }}">
                                Add
                            </a>
                        </li> --}}
                    </ul>
                </li>

                <!-- Tenancy Sub Status Section -->
                <li class="sidebar-sub-list-item submenu_wrapper">
                    <a href="#tenancySubStatusSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ areActiveRoutes(['admin.tenancy_sub_statuses.index', 'admin.tenancy_sub_statuses.create'], 'true') }}"
                        class="dropdown-toggle {{ areActiveRoutes(['admin.tenancy_sub_statuses.index', 'admin.tenancy_sub_statuses.create']) }}">
                        
                        <span class="icon_wrapper">Tenancy Sub Status</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ areActiveRoutes(['admin.tenancy_sub_statuses.index', 'admin.tenancy_sub_statuses.create'], 'show') }}"
                        id="tenancySubStatusSubmenu">
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ areActiveRoutes(['admin.tenancy_sub_statuses.index']) }} @endslot
                            @slot('link') {{ route('admin.tenancy_sub_statuses.index') }} @endslot
                            @slot('link_name') View All
                            @endslot
                        @endcomponent
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ areActiveRoutes(['admin.tenancy_sub_statuses.create']) }} @endslot
                            @slot('link') {{ route('admin.tenancy_sub_statuses.create') }} @endslot
                            @slot('link_name') Add
                            @endslot
                        @endcomponent
                        {{-- <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.tenancy_sub_statuses.index']) }}"
                                href="{{ route('admin.tenancy_sub_statuses.index') }}">
                                View All
                            </a>
                        </li>
                        <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.tenancy_sub_statuses.create']) }}"
                                href="{{ route('admin.tenancy_sub_statuses.create') }}">
                                Add
                            </a>
                        </li> --}}
                    </ul>
                </li>

                <!-- Job Types Section -->
                <li class="sidebar-sub-list-item  submenu_wrapper">
                    <a href="#jobTypesSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ areActiveRoutes(['admin.job_types.index', 'admin.job_types.create'], 'true') }}"
                        class="dropdown-toggle {{ areActiveRoutes(['admin.job_types.index', 'admin.job_types.create']) }}">
                        
                        <span class="icon_wrapper">Job Types</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ areActiveRoutes(['admin.job_types.index', 'admin.job_types.create'], 'show') }}"
                        id="jobTypesSubmenu">
                        @component('components.backend.common.sidebar-sublink')
                            @slot('class') {{ areActiveRoutes(['admin.job_types.index']) }} @endslot
                            @slot('link') {{ route('admin.job_types.index') }} @endslot
                            @slot('link_name') View All
                            @endslot
                        @endcomponent
                        {{-- <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.job_types.index']) }}"
                                href="{{ route('admin.job_types.index') }}">
                                View All
                            </a>
                        </li> --}}
                    </ul>
                </li>
            </ul>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <span class="icon_wrapper"><i class="fa-solid fa-users"></i>Users</span> 
            </a>
        </li>
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <span class="icon_wrapper"><i class="fa-solid fa-cogs"></i>Settings</span> 
            </a>
        </li>
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <span class="icon_wrapper"><i class="fa-solid fa-chart-bar"></i>Reports</span> 
            </a>
        </li>
        <li class="sidebar-list-item submenu_wrapper">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <a href="#" class="logout_btn_wrapper">
                    <button type="submit" class="logout_btn border-0 background-none">
                        <i class="fa-solid fa-sign-out-alt"></i> Logout
                    </button>
                </a>
            </form>
        </li>
    </ul>
</aside>
<div class="backdrop"></div>