@props(['name' => 'property_id', 'selected' => null])

<select name="{{ $name }}" id="{{ $name }}" class="form-control property-select {{ $name }}" style="width:100%">
    @if($selected)
        @php
            $property = \App\Models\Property::find($selected);
        @endphp
        @if($property)
            <option value="{{ $property->id }}" selected>{{ $property->display_label }}</option>
        @endif
    @endif
</select>

@push('scripts')
<script>
$(function() {
    $('#{{ $name }}').select2({
        placeholder: 'Select Property',
        allowClear: true,
        ajax: {
            url: '{{ route("backend.properties.search-ajax") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.display_label
                        };
                    })
                };
            },
            cache: true
        }
    });
});
</script>
@endpush
