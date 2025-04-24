<aside id="menu" class="sidebar bg-light sidebar">
    <ul class="list-unstyled components">
        <li class="sidebar-list-item submenu_wrapper">
            <a class="{{ request()->routeIs('backend.dashboard') ? 'active' : '' }}"
                href="{{ route('backend.dashboard') }}">
                <i class="fa-solid fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#propertiesSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ request()->routeIs('admin.properties.index') || request()->routeIs('admin.properties.soft_deleted') || request()->routeIs('admin.properties.create') ? 'true' : 'false' }} "
                class="dropdown-toggle {{ request()->routeIs('admin.properties.index') || request()->routeIs('admin.properties.quick') || request()->routeIs('admin.properties.soft_deleted') || request()->routeIs('admin.properties.create') ? 'active' : '' }}">
                <i class="fa-solid fa-building"></i> Properties
            </a>
            <ul class="nav-second-level collapse list-unstyled {{ request()->routeIs('admin.properties.index') || request()->routeIs('admin.properties.quick') || request()->routeIs('admin.properties.soft_deleted') || request()->routeIs('admin.properties.create') ? 'show' : '' }}"
                id="propertiesSubmenu">
                <li class="sidebar-sub-list-item">
                    <a class="{{ request()->routeIs('admin.properties.index') ? 'active' : '' }}"
                        href="{{ route('admin.properties.index') }}">
                        <i class="fa-solid fa-eye"></i> View Properties
                    </a>
                </li>
                <li class="sidebar-sub-list-item">
                    <a class="{{ request()->routeIs('admin.properties.quick') ? 'active' : '' }}"
                        href="{{ route('admin.properties.quick') }}">
                        <i class="fa-solid fa-plus"></i> Add Property
                    </a>
                </li>
                <li class="sidebar-sub-list-item">
                    <a class="{{ request()->routeIs('admin.properties.soft_deleted') ? 'active' : '' }}"
                        href="{{ route('admin.properties.soft_deleted') }}">
                        <i class="fa-solid fa-trash"></i> Deleted Properties
                    </a>
                </li>
            </ul>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#contactsSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ request()->routeIs('admin.contacts.index') || request()->routeIs('contacts.create') ? 'true' : 'false' }}"
                class="dropdown-toggle {{ request()->routeIs('admin.contacts.index') || request()->routeIs('contacts.create') ? 'active' : '' }}">
                <i class="fa-solid fa-address-book"></i> Contacts
            </a>
            <ul class="nav-second-level collapse list-unstyled {{ request()->routeIs('admin.contacts.index') || request()->routeIs('contacts.create') ? 'show' : '' }}"
                id="contactsSubmenu">
                <li class="sidebar-sub-list-item">
                    <a href="{{ route('admin.contacts.index') }}"
                        class="{{ request()->routeIs('admin.contacts.index') && !request()->has('category') ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i> All
                    </a>
                </li>
                <li class="sidebar-sub-list-item">
                    <a href="{{ route('admin.contacts.index', ['category' => 1]) }}"
                        class="{{ request()->category == 1 ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i> Owners
                    </a>
                </li>
                <li class="sidebar-sub-list-item">
                    <a href="{{ route('admin.contacts.index', ['category' => 2]) }}"
                        class="{{ request()->category == 2 ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i> Property Managers
                    </a>
                </li>
                <li class="sidebar-sub-list-item">
                    <a href="{{ route('admin.contacts.index', ['category' => 3]) }}"
                        class="{{ request()->category == 3 ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i> Tenants
                    </a>
                </li>
                <li class="sidebar-sub-list-item">
                    <a href="{{ route('admin.contacts.index', ['category' => 4]) }}"
                        class="{{ request()->category == 4 ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i> Landlords
                    </a>
                </li>
            </ul>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <i class="fa-solid fa-home"></i> Tenancies
            </a>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#repairSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ request()->routeIs('admin.property_repairs.*') ? 'true' : 'false' }}"
                class="dropdown-toggle {{ request()->routeIs('admin.property_repairs.*') ? 'active' : '' }}">
                <i class="fa-solid fa-building"></i> Repair
            </a>
            <ul class="nav-second-level collapse list-unstyled {{ request()->routeIs('admin.property_repairs.*') ? 'show' : '' }}"
                id="repairSubmenu">
                <!-- Raise Repair Issue -->
                <li class="sidebar-sub-list-item">
                    <a class="{{ request()->routeIs('admin.property_repairs.create') || request()->routeIs('admin.property_repairs.edit') ? 'active' : '' }}"
                        href="{{ route('admin.property_repairs.create') }}">
                        <i class="fa-solid fa-plus"></i> Raise Repair Issue
                    </a>
                </li>

                <!-- Repair Issues Section -->
                <li class="sidebar-sub-list-item submenu_wrapper">
                    <a href="#repairIssuesSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ request()->routeIs('admin.property_repairs.index') ? 'true' : 'false' }}"
                        class="dropdown-toggle {{ request()->routeIs('admin.property_repairs.index') || request()->routeIs('admin.property_repairs.show') ? 'active' : '' }}">
                        <i class="fa-solid fa-eye"></i> Repair Issues
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ request()->routeIs('admin.property_repairs.index') ? 'show' : '' }}"
                        id="repairIssuesSubmenu">

                        <!-- "All" Status Option -->
                        <li class="sidebar-sub-sub-list-item">
                            <a href="{{ route('admin.property_repairs.index') }}"
                                class="{{ request()->fullUrl() === route('admin.property_repairs.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-list"></i> All
                            </a>
                        </li>


                        @php
                            $statuses = ['Pending', 'Reported', 'Under Process', 'Work Completed', 'Invoice Received', 'Invoice Paid', 'Closed'];
                            $currentStatus = request('status');
                        @endphp

                        @foreach($statuses as $status)
                            <li class="sidebar-sub-sub-list-item">
                                <a href="{{ route('admin.property_repairs.index', ['status' => $status]) }}"
                                    class="{{ $currentStatus === $status ? 'active' : '' }}">
                                    <i class="fa-solid fa-tag"></i> {{ $status }}
                                </a>
                            </li>
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
                <i class="fas fa-file-invoice-dollar aiz-side-nav-icon"></i>
                <span class="aiz-side-nav-text">Invoices</span>
                <span class="aiz-side-nav-arrow"></span>
            </a>
            <ul class="aiz-side-nav-list level-2 collapse {{ request()->routeIs('admin.invoices.index') ? 'show' : '' }}" id="invoiceSubmenu">
                @foreach($invoiceStatuses as $key => $status)
                    <li class="sidebar-sub-sub-list-item">
                        <a href="{{ route('admin.invoices.index', ['status' => $key]) }}"
                            class="{{ $currentInvoiceStatus === $key ? 'active' : '' }}">
                            {{ $status }}
                        </a>                        
                    </li>
                @endforeach
            </ul>
        </li>
    
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <i class="fa-solid fa-file-alt"></i> Documents
            </a>
        </li>
        <hr>
        
        <!-- Website Setup -->
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#websiteSetupSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ areActiveRoutes(['website.footer', 'website.header', 'website.appearance'], 'true') }}"
                class="dropdown-toggle {{ areActiveRoutes(['website.footer', 'website.header', 'website.appearance']) }}">
                <i class="las la-desktop aiz-side-nav-icon"></i>
                <span class="aiz-side-nav-text">Website Setup</span>
                <span class="aiz-side-nav-arrow"></span>
            </a>
            <ul class="aiz-side-nav-list level-2 collapse {{ areActiveRoutes(['website.footer', 'website.header', 'website.appearance'], 'show') }}"
                id="websiteSetupSubmenu">
                <li class="sidebar-sub-list-item">
                    <a href="{{ route('website.header') }}" class="aiz-side-nav-link {{ areActiveRoutes(['website.header']) }}">
                        <span class="aiz-side-nav-text">Header</span>
                    </a>
                </li>
                <li class="sidebar-sub-list-item">
                    <a href="{{ route('website.footer') }}" class="aiz-side-nav-link {{ areActiveRoutes(['website.footer']) }}">
                        <span class="aiz-side-nav-text">Footer</span>
                    </a>
                </li>
                <li class="sidebar-sub-list-item">
                    <a href="{{ route('website.appearance') }}" class="aiz-side-nav-link {{ areActiveRoutes(['website.appearance']) }}">
                        <span class="aiz-side-nav-text">Appearance</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="sidebar-list-item submenu_wrapper">
            <a href="#masterManageSubmenu" data-bs-toggle="collapse"
                aria-expanded="{{ areActiveRoutes([
                    'admin.branches.index', 
                    'admin.designations.index', 
                    'admin.tenancy_types.index', 
                    'admin.tenancy_types.create', 
                    'admin.tenancy_sub_statuses.index', 
                    'admin.tenancy_sub_statuses.create', 
                    'admin.job_types.index', 
                    'admin.job_types.create'
                ], 'true') }}"
                class="dropdown-toggle {{ areActiveRoutes([
                    'admin.branches.index', 
                    'admin.designations.index', 
                    'admin.tenancy_types.index', 
                    'admin.tenancy_types.create', 
                    'admin.tenancy_sub_statuses.index', 
                    'admin.tenancy_sub_statuses.create', 
                    'admin.job_types.index', 
                    'admin.job_types.create'
                ]) }}">
                <i class="fa-solid fa-cogs"></i> Master Manage
            </a>
            <ul class="nav-second-level collapse list-unstyled {{ areActiveRoutes([
                'contact-categories.index', 
                'admin.branches.index', 
                'admin.designations.index', 
                'admin.tenancy_types.index', 
                'admin.tenancy_types.create', 
                'admin.tenancy_sub_statuses.index', 
                'admin.tenancy_sub_statuses.create', 
                'admin.job_types.index', 
                'admin.job_types.create'
            ], 'show') }}"
                id="masterManageSubmenu">
        
                <li class="sidebar-list-item submenu_wrapper">
                    <a class="{{ areActiveRoutes(['contact-categories.index']) }}"
                       href="{{ route('contact-categories.index') }}">
                        <i class="fa-solid fa-tags"></i> Categories
                    </a>
                </li>
        
                <li class="sidebar-list-item submenu_wrapper">
                    <a class="{{ areActiveRoutes(['admin.branches.index']) }}"
                       href="{{ route('admin.branches.index') }}">
                        <i class="fa-solid fa-sitemap"></i> Branches
                    </a>
                </li>
        
                <li class="sidebar-list-item submenu_wrapper">
                    <a class="{{ areActiveRoutes(['admin.designations.index']) }}"
                       href="{{ route('admin.designations.index') }}">
                        <i class="fa-solid fa-user-tag"></i> Designation
                    </a>
                </li>
        
                <!-- Tenancy Types Section -->
                <li class="sidebar-sub-list-item submenu_wrapper">
                    <a href="#tenancyTypesSubmenu" data-bs-toggle="collapse"
                       aria-expanded="{{ areActiveRoutes(['admin.tenancy_types.index', 'admin.tenancy_types.create'], 'true') }}"
                       class="dropdown-toggle {{ areActiveRoutes(['admin.tenancy_types.index', 'admin.tenancy_types.create']) }}">
                        <i class="fa-solid fa-clipboard-list"></i> Tenancy Types
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ areActiveRoutes(['admin.tenancy_types.index', 'admin.tenancy_types.create'], 'show') }}"
                        id="tenancyTypesSubmenu">
                        <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.tenancy_types.index']) }}"
                               href="{{ route('admin.tenancy_types.index') }}">
                                <i class="fa-solid fa-eye"></i> View All
                            </a>
                        </li>
                        <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.tenancy_types.create']) }}"
                               href="{{ route('admin.tenancy_types.create') }}">
                                <i class="fa-solid fa-plus"></i> Add
                            </a>
                        </li>
                    </ul>
                </li>
        
                <!-- Tenancy Sub Status Section -->
                <li class="sidebar-sub-list-item submenu_wrapper">
                    <a href="#tenancySubStatusSubmenu" data-bs-toggle="collapse"
                       aria-expanded="{{ areActiveRoutes(['admin.tenancy_sub_statuses.index', 'admin.tenancy_sub_statuses.create'], 'true') }}"
                       class="dropdown-toggle {{ areActiveRoutes(['admin.tenancy_sub_statuses.index', 'admin.tenancy_sub_statuses.create']) }}">
                        <i class="fa-solid fa-stream"></i> Tenancy Sub Status
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ areActiveRoutes(['admin.tenancy_sub_statuses.index', 'admin.tenancy_sub_statuses.create'], 'show') }}"
                        id="tenancySubStatusSubmenu">
                        <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.tenancy_sub_statuses.index']) }}"
                               href="{{ route('admin.tenancy_sub_statuses.index') }}">
                                <i class="fa-solid fa-eye"></i> View All
                            </a>
                        </li>
                        <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.tenancy_sub_statuses.create']) }}"
                               href="{{ route('admin.tenancy_sub_statuses.create') }}">
                                <i class="fa-solid fa-plus"></i> Add
                            </a>
                        </li>
                    </ul>
                </li>
        
                <!-- Job Types Section -->
                <li class="sidebar-sub-list-item submenu_wrapper">
                    <a href="#jobTypesSubmenu" data-bs-toggle="collapse"
                       aria-expanded="{{ areActiveRoutes(['admin.job_types.index', 'admin.job_types.create'], 'true') }}"
                       class="dropdown-toggle {{ areActiveRoutes(['admin.job_types.index', 'admin.job_types.create']) }}">
                        <i class="fa-solid fa-tools"></i> Job Types
                    </a>
                    <ul class="nav-third-level collapse list-unstyled {{ areActiveRoutes(['admin.job_types.index', 'admin.job_types.create'], 'show') }}"
                        id="jobTypesSubmenu">
                        <li class="sidebar-sub-sub-list-item">
                            <a class="{{ areActiveRoutes(['admin.job_types.index']) }}"
                               href="{{ route('admin.job_types.index') }}">
                                <i class="fa-solid fa-eye"></i> View All
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <i class="fa-solid fa-users"></i> Users
            </a>
        </li>
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <i class="fa-solid fa-cogs"></i> Settings
            </a>
        </li>
        <li class="sidebar-list-item submenu_wrapper">
            <a href="#">
                <i class="fa-solid fa-chart-bar"></i> Reports
            </a>
        </li>
        <li class="sidebar-list-item submenu_wrapper">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <a href="#">
                <button type="submit" class="logout_btn border-0 background-none">
                    <i class="fa-solid fa-sign-out-alt"></i> Logout
                </button>
                </a>
            </form>
        </li>
    </ul>
</aside>
<div class="backdrop"></div>
