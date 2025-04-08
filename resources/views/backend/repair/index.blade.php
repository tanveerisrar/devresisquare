@extends('backend.layout.app')

@section('content')
<style>
    #detail-pane {
    transition: all 0.3s ease;
}
</style>
<div class="row">
    <!-- Left: List + Filters -->
    <div class="col-md-4" id="list-pane">
        @include('backend.repair.list.cards', ['repairIssues' => $repairIssues])
        <!-- Toggle Button -->
        <button id="toggle-detail-pane" class="btn btn-outline-secondary float-end mb-2">
            <i class="fas fa-chevron-right"></i> Hide Detail
        </button>
    </div>

    <!-- Right: Detail -->
    <div class="col-md-8" id="detail-pane">
        <div class="alert alert-info">Select a repair item to view details.</div>
    </div>
</div>
@endsection

@section('page.scripts')
<script>
    $(document).ready(function () {
        // Toggle the detail pane
        $('#toggle-detail-pane').click(function () {
            const $detail = $('#detail-pane');
            const $icon = $(this).find('i');
            const isVisible = $detail.is(':visible');

            $detail.toggle();
            $('#list-pane').toggleClass('col-md-4 col-md-12');

            // Update icon and text
            if (isVisible) {
                $icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                $(this).contents().last().replaceWith(' Show Detail');
            } else {
                $icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                $(this).contents().last().replaceWith(' Hide Detail');
            }
        });
    });
    // Load detail via AJAX
    // $(document).on('click', '.card[data-id]', function () {
    //     const id = $(this).data('id');
    //     const entity = "{{ $entity }}";
    //     $('#detail-section').html('<div class="text-center py-5">Loading...</div>');

    //     $.get(`/${entity}/${id}`, function (res) {
    //         $('#detail-section').html(res);
    //     });
    // });

    // Search form
    $('#filter-form').on('submit keyup', function (e) {
        e.preventDefault();
        $.get(`/${'{{ $entity }}'}`, $(this).serialize(), function (res) {
            $('#card-list').html(res);
        });
    });
    function loadRepairDetailByUrl(el) {
        const url = $(el).data('url');
        $.get(url, function(response) {
            $('#detail-pane').html(response);
        }).fail(function(xhr) {
            console.error(xhr);
            alert('Failed to load detail.');
        });
    }

</script>
@endsection
