@extends('backend.layout.app')

@section('content')
    <style>
        #detail-pane,
        #list-pane {
            transition: all 0.5s ease;
        }

        .hidden-pane {
            opacity: 0;
            visibility: hidden;
            width: 0;
            padding: 0;
            margin: 0;
            overflow: hidden;
        }

        .expanded-list {
            width: 100% !important;
        }

        .toggle-btn {
            position: sticky;
            bottom: 15px;
            left: 0;
            z-index: 10;
        }

        .spinner-overlay {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .repair-row>td:hover {
            color: #d83434;
        }

        thead>tr>th {
            background-color: #e9eef5 !important;
            color: #000 !important;
        }
        .repair-row.selected>td {
            background-color: #6c6c6c;
            color: #fff;
        }

        .repair-row>td {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>

    <div class="row" id="master-detail-wrapper">
        <!-- Left: List + Filters -->
        <div class="col-md-5" id="list-pane">
            <!-- Toggle Button -->
            <button id="toggle-detail-pane" class="btn btn-outline-secondary float-end toggle-btn mt-2">
                <i class="fas fa-chevron-left"></i> Hide Detail
            </button>
            @include('backend.repair.list.filter')
            @include('backend.repair.list.cards', ['repairIssues' => $repairIssues])
        </div>
        <!-- Right: Detail -->
        <div class="col-md-7" id="detail-pane">
            <div class="alert alert-info">Select a repair item to view details.</div>
        </div>
    </div>
@endsection

@section('page.scripts')
    <script>
        let lastLoadedUrl = null; // Track the last detail URL

        $(document).ready(function () {
            const $toggleBtn = $('#toggle-detail-pane');

            function showDetailPane() {
                const $detailPane = $('#detail-pane');
                const $listPane = $('#list-pane');
                const $icon = $toggleBtn.find('i');

                if ($detailPane.hasClass('hidden-pane')) {
                    $detailPane.removeClass('hidden-pane col-md-0').addClass('col-md-7');
                    $listPane.removeClass('col-md-12').addClass('col-md-5');
                    $icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                    $toggleBtn.contents().last().replaceWith(' Hide Detail');
                }
            }

            // Toggle button click
            $toggleBtn.click(function () {
                const $detailPane = $('#detail-pane');
                const $listPane = $('#list-pane');
                const $icon = $(this).find('i');
                const isHidden = $detailPane.hasClass('hidden-pane');

                if (isHidden) {
                    // Show
                    $detailPane.removeClass('hidden-pane col-md-0').addClass('col-md-7');
                    $listPane.removeClass('col-md-12').addClass('col-md-5');
                    $icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
                    $(this).contents().last().replaceWith(' Hide Detail');
                } else {
                    // Hide
                    $detailPane.addClass('hidden-pane col-md-0').removeClass('col-md-7');
                    $listPane.removeClass('col-md-5').addClass('col-md-12');
                    $icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
                    $(this).contents().last().replaceWith(' Show Detail');
                }
            });

            // AJAX Search form
            $('#filter-form').on('submit', function (e) {
                e.preventDefault();
                $.get(`{{ route('admin.property_repairs.index') }}`, $(this).serialize(), function (res) {
                    $('#card-list').html(res);
                    lastLoadedUrl = null; // Reset last detail URL
                });
            });

            // Load detail via AJAX + auto-show detail pane if hidden
            window.loadRepairDetailByUrl = function (el) {
                const url = $(el).data('url');

                showDetailPane(); // Ensure detail pane is visible

                // Prevent reloading same content
                if (url === lastLoadedUrl) return;
                lastLoadedUrl = url;

                const $detailPane = $('#detail-pane');
                // Spinner while loading
                $detailPane.html(`
                        <div class="spinner-overlay">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `);

                $('.repair-row').removeClass('selected'); // Remove from all rows
                $(el).closest('.repair-row').addClass('selected'); // Add to clicked row

                $.get(url, function (response) {
                    // $('#detail-pane').html(response);
                    $detailPane.html('<div class="fade-in">' + response + '</div>');
                }).fail(function (xhr) {
                    // console.error(xhr);
                    $detailPane.html(`<div class="alert alert-danger fade-in">Failed to load detail.</div>`);
                });
            };
        });
    </script>
@endsection