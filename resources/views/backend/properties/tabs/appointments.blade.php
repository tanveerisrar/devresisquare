<form id="appointments-filter-form" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search title or diary owner">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-control">
            <option value="">All Statuses</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Cancelled">Cancelled</option>
            <option value="Pending">Pending</option>
        </select>
    </div>
    <input type="hidden" name="property_id" value="{{ $propertyId }}">
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
    <div class="col-md-2">
        <button type="button" id="reset-appointments-filter" class="btn btn-secondary w-100">Reset</button>
    </div>
</form>


<div id="appointments-results">
    @include('backend.properties.tabs.component._appointments_table', ['events' => $events])
</div>
