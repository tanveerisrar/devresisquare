@if($bankDetails->isEmpty())
    <div class="alert alert-warning">No bank details found for this contact.</div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th>Sort Code</th>
                    <th>Bank Name</th>
                    <th>Swift Code</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bankDetails as $index => $bank)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $bank->account_name }}</td>
                        <td>{{ $bank->account_no }}</td>
                        <td>{{ $bank->sort_code }}</td>
                        <td>{{ $bank->bank_name }}</td>
                        <td>{{ $bank->swift_code }}</td>
                        <td>{{ formatDateTime($bank->updated_at) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
