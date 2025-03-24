<div class="row mt-md-5">
    <div class="col-md-4 mb-3">
        <div class="form-group"> 
            <label class="form-label d-block">Invoice To</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="invoice_to" value="Landlord" id="invoice_landlord" required {{ old('invoice_to', $repairIssue->workOrder->invoice_to ?? '') == 'Landlord' ? 'checked' : '' }}>
                <label class="form-check-label" for="invoice_landlord">Landlord</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="invoice_to" value="Tenant" id="invoice_tenant" {{ old('invoice_to', $repairIssue->workOrder->invoice_to ?? '') == 'Tenant' ? 'checked' : '' }}>
                <label class="form-check-label" for="invoice_tenant">Tenant</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="invoice_to" value="Company" id="invoice_company" {{ old('invoice_to', $repairIssue->workOrder->invoice_to ?? '') == 'Company' ? 'checked' : '' }}>
                <label class="form-check-label" for="invoice_company">
                    Company
                </label>
            </div>
        </div>
    </div>
    
    <!-- Dynamic Dropdown Container -->
    <div id="invoiceToContainer" class="col-md-4 mb-3"></div>
    
    <!-- Hidden Fields -->
    <input type="hidden" id="existingInvoiceToId" value="{{ old('invoice_to_id', $repairIssue->workOrder->invoice_to_id ?? '') }}">

    <!-- Contact Details (Hidden Initially) -->
    <div id="contactDetails" class="mt-3 col-md-4" style="display: none;">
        {{-- <h6>Contact Details</h6>
        <p><strong>Name:</strong> <span id="contactName"></span></p> --}}
        <p><strong>Address:</strong> <span id="contactAddress"></span></p>
        <p><strong>Email:</strong> <span id="contactEmail"></span></p>
        <p><strong>Phone:</strong> <span id="contactPhone"></span></p>
    </div>

    <div class="row">
        <div class="col mb-3">
            <div class="form-group"> 
                <label class="form-label">Actual Cost</label>
                <input required type="number" step="0.01" name="actual_cost" class="form-control" value="{{ old('actual_cost', $repairIssue->workOrder->actual_cost ?? '') }}">
            </div>
        </div>

        <div class="col mb-3">
            <div class="form-group"> 
                <label class="form-label">Charge to Landlord</label>
                <input type="number" step="0.01" name="charge_to_landlord" class="form-control" value="{{ old('charge_to_landlord', 
                $repairIssue->workOrder->charge_to_landlord ?? '') }}">
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="form-group"> 
            <label class="form-label">Payment Terms</label>
            <select required name="payment_by" class="form-control">
                <option value="Landlord"  {{ old('payment_by', $repairIssue->workOrder->payment_by ?? '') == 'Landlord' ? 'selected' : '' }}>Landlord</option>
                <option value="Tenant"  {{ old('payment_by', $repairIssue->workOrder->payment_by ?? '') == 'Tenant' ? 'selected' : '' }}>Tenant</option>
                <option value="Company"  {{ old('payment_by', $repairIssue->workOrder->payment_by ?? '') == 'Company' ? 'selected' : '' }}>Company</option>
            </select>
        </div>
    </div>
    <!-- Status Dropdown (Dynamically Updated) -->
    <div class="col-md-6 mb-3">
        <div class="form-group"> 
            <label class="form-label">Status</label>
            <select required name="status" id="statusSelect" class="form-control">
                <!-- Status options will be dynamically inserted here -->
            </select>
        </div>
    </div>

    <!-- Hidden Field to Store the Preselected Status -->
    <input type="hidden" id="existingStatus" value="{{ old('status', $repairIssue->workOrder->status ?? '') }}">


</div>