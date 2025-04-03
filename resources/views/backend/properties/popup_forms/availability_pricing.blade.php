@if(!isset($editMode) || !$editMode)
<!-- Display View Mode -->
<strong>Availability:</strong> {{ $property->available_from }} <br>
<strong>Price:</strong> £{{ $property->price }} <br>
<strong>Letting Price:</strong> £{{ $property->letting_price }} <br>
@else
<form id="availabilityPricingForm">
    @csrf
    <input type="hidden" name="property_id" value="{{ $property->id }}">
    <input type="hidden" name="form_type" value="availability_pricing">

    <div class="mb-3">
        <label>Availability</label>
        <input type="date" name="available_from" class="form-control" value="{{ $property->available_from }}">
    </div>

    <div class="mb-3">
        <label>Price (£)</label>
        <input type="text" name="price" class="form-control" value="{{ $property->price }}">
    </div>

    <div class="mb-3">
        <label>Letting Price (£)</label>
        <input type="text" name="letting_price" class="form-control" value="{{ $property->letting_price }}">
    </div>

    <button type="submit" class="btn btn-success">Save Changes</button>
</form>
@endif