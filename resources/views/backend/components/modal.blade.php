<!-- Modal -->
<div class="modal fade" id="largeModal" tabindex="-1" aria-labelledby="largeModal-label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="largeModal-label">Loading...</h5>
        <a type="button" class="btn-close" onclick="closeModel();" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        Loading...
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="largeModalScrollable" tabindex="-1" aria-labelledby="largeModalScrollable-label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="largeModalScrollable-label">Loading...</h5>
        <a type="button" class="btn-close" onclick="closeModel();" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        Loading...
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="smallModal" tabindex="-1" aria-labelledby="smallModal-label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="smallModal-label">Loading...</h5>
        <a type="button" class="btn-close" onclick="closeModel();" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        Loading...
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModal-label" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center">
        <form method="POST" class="ajaxDeleteForm" action="" id ="delete_form">
          @csrf
          <i class="fa-solid fa-circle-info" style="font-size: 50px;color: #dc3545;"></i>
          <p class="mt-3">Are you sure?</p>
            <div class="d-flex justify-content-evenly">
                <button type="button" class="btn btn-sm btn-info" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark me-2"></i> Cancel
                </button>
                <button type="submit" class="btn btn-sm btn-secondary" onclick="">
                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Continue
                </button>
            </div>


        </form>
      </div>
    </div>
  </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Image Preview</h5>
              <button type="button" class="btn-close" id="closeModalBtn" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
              <img id="previewImage" class="img-fluid" src="" alt="Preview Image">
          </div>
      </div>
  </div>
</div>
