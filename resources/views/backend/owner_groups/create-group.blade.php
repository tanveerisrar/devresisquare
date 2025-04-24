<div id="mainForm">
<form id="owner-group-form" action="{{ route('admin.owner-groups.store_group') }}" method="POST">
    @csrf
    <input type="hidden" name="property_id" class="form-control" value="">

    <button type="button" class="d-flex float-end btn btn-outline-primary btn-sm" id="addContactBtn">Add New Contact</button>

    <div class="form-group">
        <label for="contact_id">Contacts</label>
        <select name="contact_id[]" id="contact_id" class="form-control select2" multiple="multiple" required>
            @foreach($contacts as $contact)
                <option value="{{ $contact->id }}">{{ $contact->full_name }}</option>
            @endforeach
        </select>
    </div>

    <div id="contact-options" class="mt-3"></div>

    <div class="row">
        <div class="col-md-6 col-12">
            <div class="form-group">
                <label for="purchased_date">Purchased Date</label>
                <input type="date" name="purchased_date" id="purchased_date" class="form-control" required>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                <label for="sold_date">Sold Date</label>
                <input type="date" name="sold_date" id="sold_date" class="form-control">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-12">
            <div class="form-group">
                <label for="archived_date">Archived Date</label>
                <input type="date" name="archived_date" id="archived_date" class="form-control">
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="active">Active</option>
                    {{-- <option value="inactive">Inactive</option> --}}
                    <option value="archived">Archived</option>
                </select>
            </div>
        </div>
    </div>

    <button type="submit" class="float-end mt-3 btn btn-secondary">Save</button>
</form>
</div>
<div id="addContactFormContainer" style="display: none;">
    <form id="addContactForm">
        @csrf
        <input type="hidden" class="form-control" id="category_id" name="category_id" value="1">
        <div class="mb-3">
            <label for="contact_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="contact_name" name="full_name" required>
        </div>
        <div class="mb-3">
            <label for="contact_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="contact_email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="contact_phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="contact_phone" name="phone" required>
        </div>
        <div class="row">
            <div class="col-auto">
                <button type="submit" class="btn btn_secondary">Save Contact</button>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn_outline_secondary" id="backToMainForm">Back</button>
            </div>
        </div>
    </form>
</div>

<script>

    initSelect2('.select2');

    // Form submission validation
    $('#mainForm form').on('submit', function(e) {
        if ($('input[name="is_main"]:checked').length === 0) {
            e.preventDefault(); // Prevent form submission
            alert('Please select a main contact.'); // Show alert message
        }
    });
</script>

