<div class="card">
  <div class="card-header">
    <strong>Compliance Information</strong>
  </div>
  <div class="card-body row g-3">
    {{-- Nationality --}}
    <div class="col-md-6">
      <label for="nationality_id" class="form-label"><strong>Nationality</strong></label>
      <select name="nationality_id" id="nationality_id" class="form-select">
        <option value="">-- Select Nationality --</option>
        @foreach($nationalities as $id => $name)
          <option value="{{ $id }}"
            {{ old('nationality_id', $contact->nationality_id ?? '') == $id ? 'selected' : '' }}>
            {{ $name }}
          </option>
        @endforeach
      </select>
    </div>

    {{-- Visa Expiry Date --}}
    <div class="col-md-6">
      <label for="visa_expiry" class="form-label"><strong>Visa Expiry Date</strong></label>
      <input
        type="date"
        class="form-control"
        name="visa_expiry"
        id="visa_expiry"
        value="{{ old('visa_expiry', $contact->visa_expiry ?? '') }}"
      >
    </div>

    {{-- Passport No --}}
    <div class="col-md-6">
      <label for="passport_no" class="form-label"><strong>Passport No.</strong></label>
      <input
        type="text"
        class="form-control"
        name="passport_no"
        id="passport_no"
        value="{{ old('passport_no', $contact->passport_no ?? '') }}"
      >
    </div>

    {{-- NRL Number --}}
    <div class="col-md-6">
      <label for="nrl_number" class="form-label"><strong>NRL Number</strong></label>
      <input
        type="text"
        class="form-control"
        name="nrl_number"
        id="nrl_number"
        value="{{ old('nrl_number', $contact->nrl_number ?? '') }}"
      >
    </div>

    {{-- Right to Rent Check --}}
    <div class="col-12">
      <div class="form-check mb-2">
        <input
          class="form-check-input"
          type="checkbox"
          id="right_to_rent_check"
          name="right_to_rent_check"
          value="1"
          {{ old('right_to_rent_check', $contact->right_to_rent_check ?? false) ? 'checked' : '' }}
        >
        <label class="form-check-label" for="right_to_rent_check">
          Right to Rent Check completed in person?
        </label>
      </div>

      <div id="rent-check-person-details" class="row g-3 mt-2" style="display: none;">
        {{-- Internal staff --}}
        <div class="col-md-6">
          <label for="checked_by_user" class="form-label"><strong>Select Staff</strong></label>
          <select name="checked_by_user" id="checked_by_user" class="form-select">
            <option value="">-- Select User --</option>
            @foreach($users as $id => $name)
              <option value="{{ $id }}"
                {{ old('checked_by_user', $contact->checked_by_user ?? '') == $id ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- External person --}}
        <div class="col-md-6">
          <label for="checked_by_external" class="form-label"><strong>Or External Person</strong></label>
          <input
            type="text"
            class="form-control"
            name="checked_by_external"
            id="checked_by_external"
            placeholder="Name of external checker"
            value="{{ old('checked_by_external', $contact->checked_by_external ?? '') }}"
          >
        </div>
      </div>

      <div
        id="rent-check-message"
        class="alert alert-info mt-3"
        style="display: none;"
      >
        Thank you for confirming the Right to Rent Check. You have personally checked and completed the formalities of this applicant.
      </div>
    </div>
  </div>
</div>
