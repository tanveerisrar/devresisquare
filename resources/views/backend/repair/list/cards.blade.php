@include('backend.repair.list.filter')

@foreach ($repairIssues as $item)
    <x-backend.repair-card :repair="$item" />
@endforeach

<!-- Pagination -->
<div class="d-flex justify-content-center mt-3">
    {{ $repairIssues->appends(request()->query())->links() }}
</div>
