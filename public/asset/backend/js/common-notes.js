(function($){
  const api = '/admin/notes';

  // Load note list
  function load($c, extraParams = {}) {
    const type = $c.data('noteable-type');
    const id   = $c.data('noteable-id');
    const $list = $c.find('.notes-list');

    $list.html('<p>Loading…</p>');
    $.get(`${api}/list`, {
      noteable_type: type,
      noteable_id: id,
      ...extraParams
    }, r => {
      $list.html(r.html);
    });
  }

  // Open add/edit note form
  function openForm($c, mode, noteId = 0) {
    const $modal = $c.find('.notes-modal');
    const $form = $modal.find('.notes-form');

    if (mode === 'add') {
      $form[0].reset();
      $form.find('[name=note_id]').val('');
      $modal.find('.modal-title').text('Add Note');
      $modal.find('.notes-save').text('Save');

      // Reset your text editor here if needed:
      AIZ.plugins.textEditor();

      $modal.modal('show');
      return;
    }

    // EDIT mode: fetch note data from server
    $.getJSON(`${api}/show/${noteId}`, function(data) {
      // Reset form first
      $form[0].reset();

      // Fill form fields manually
      $form.find('[name=note_id]').val(data.id);
      $form.find('[name=note_type_id]').val(data.note_type_id);
      $form.find('[name=content]').val(data.content);

      // Update the text editor's content after setting textarea value
      if (typeof AIZ !== 'undefined' && AIZ.plugins && AIZ.plugins.textEditor) {
        AIZ.plugins.textEditor();
      }

      $modal.find('.modal-title').text('Edit Note');
      $modal.find('.notes-save').text('Update');
      $modal.modal('show');
    });
  }


  // Initialize components
  $(function(){
    $('.notes-component').each(function(){
      load($(this));
    });
  });

  // Handle filter form submit
  $(document).on('submit', '.notes-filter-form', function(e) {
    e.preventDefault();
    const $form = $(this);
    const $c = $form.closest('.notes-component');
    const formData = Object.fromEntries(new URLSearchParams($form.serialize()));
    load($c, formData);
  });

  // Handle reset button
  $(document).on('click', '.notes-reset', function () {
    const $btn = $(this);
    const $c = $btn.closest('.notes-component');
    const $filt = $c.find('.notes-filter-form');
    $filt[0].reset();
    load($c);
  });

  // Add note
  $(document).on('click', '.notes-add', function () {
    const $c = $(this).closest('.notes-component');
    openForm($c, 'add');
  });

  // Edit note
  $(document).on('click', '.notes-edit', function (e) {
    const $btn = $(this);
    const $c = $btn.closest('.notes-component');
    openForm($c, 'edit', $btn.data('id'));
  });

  // View note
  $(document).on('click', '.notes-view', function () {
    const $btn = $(this);
    const $c = $btn.closest('.notes-component');
    const $modal = $c.find('.notes-modal');
    const nid = $btn.data('id');

    $.get(`${api}/show/${nid}`, d => {
      $modal.find('.modal-title').text('View Note');
      $modal.find('.modal-body').html(d.html || '');
      $modal.modal('show');
    });
  });

  // Delete note
  $(document).on('click', '.notes-delete', function () {
    if (!confirm('Delete?')) return;
    const $btn = $(this);
    const $c = $btn.closest('.notes-component');
    const $filt = $c.find('.notes-filter-form');

    $.post(`${api}/delete/${$btn.data('id')}`, {
      _token: '{{ csrf_token() }}'
    }, _ => load($c, Object.fromEntries(new URLSearchParams($filt.serialize()))));
  });

  // Save note form (modal submit)
  $(document).on('submit', '.notes-form', function(e) {
    e.preventDefault();
    const $form = $(this);
    const $c = $form.closest('.notes-component');
    const $modal = $c.find('.notes-modal');
    const $filt = $c.find('.notes-filter-form');

    $.post(`${api}/save`, $form.serialize(), r => {
      $modal.modal('hide');
      load($c, Object.fromEntries(new URLSearchParams($filt.serialize())));
    });
  });

  // Pagination click
  $(document).on('click', '.notes-component .pagination a', function(e){
    e.preventDefault();
    const $a = $(this);
    const $c = $a.closest('.notes-component');
    const url = new URL($a.attr('href'), location.origin);
    const params = Object.fromEntries(url.searchParams.entries());
    load($c, params);
  });

  $(document).on('hidden.bs.modal', '.notes-modal', function () {
    const $f = $(this).find('form')[0];
    if ($f) $f.reset();
    $(this).find('.modal-body').empty(); // Clear form fields and view content
  });

})(jQuery);
