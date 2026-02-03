@csrf

<div class="row">
    <!-- Header Type -->
    <div class="col-md-6 mb-3">
        <label class="form-label d-block">Header Type <span class="text-danger">*</span></label>
        <div class="d-flex gap-3">
            @foreach($headerTypes as $type)
                <div class="form-check form-check-inline">
                    <input type="radio" name="header_type" id="header_type_{{ $type }}" class="form-check-input"
                           value="{{ $type }}"
                           {{ old('header_type', $accountHeader->header_type ?? '') === $type ? 'checked' : '' }} required>
                    <label class="form-check-label" for="header_type_{{ $type }}">{{ ucwords(str_replace('_',' ', $type)) }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Status -->
    <div class="col-md-3 mb-3">
        <label class="form-label d-block">Status</label>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1"
                {{ old('status', $accountHeader->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="status">Active</label>
        </div>
    </div>

    <!-- Reference Number -->
    <div class="col-md-3 mb-3">
        <label class="form-label">Reference Number</label>
        <input type="text" class="form-control" value="{{ old('reference_number', $accountHeader->reference_number ?? '') }}"
               placeholder="Auto-generated on save" readonly>
    </div>
</div>

<div class="row">
    <!-- Name -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Header Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $accountHeader->name ?? '') }}" required>
    </div>

    <!-- Description -->
    <div class="col-md-6 mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2" placeholder="Optional details">{{ old('description', $accountHeader->description ?? '') }}</textarea>
    </div>
</div>

<div class="text-end">
    <button type="submit" class="btn btn-success">{{ $buttonText ?? 'Save' }}</button>
</div>
