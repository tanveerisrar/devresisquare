@extends('backend.layout.app')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">Staff Information</h5>
            </div>

            <form class="form-horizontal" action="{{ route('staffs.store') }}" method="POST" enctype="multipart/form-data">
            	@csrf
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">Name</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="Name" id="name" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email">Email</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="Email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    {{-- <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="mobile">Phone</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="Phone" id="mobile" name="mobile" class="form-control" required>
                        </div>
                    </div> --}}
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="password">Password</label>
                        <div class="col-sm-9">
                            <input type="password" placeholder="Password" id="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">Role</label>
                        <div class="col-sm-9">
                            <select name="role_id" required class="form-control select2">
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection
@include('backend.partials.assets.select2')
@section('page.scripts')
    <script>
        initSelect2('.select2');
    </script>
@endsection