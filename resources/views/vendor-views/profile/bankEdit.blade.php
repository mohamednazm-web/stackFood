@extends('layouts.vendor.app')

@section('title','Bank Info')

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page">Owner</li>
                <li class="breadcrumb-item">Bank info</li>
            </ol>
        </nav>

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-black-50">{{__('messages.bank_info')}}</h1>
        </div>
        <!-- Content Row -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0 ">Edit Bank Info</h1>
                    </div>
                    <div class="card-body">
                        <form action="{{route('vendor.profile.bank_update',[$data->id])}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">Bank Name <span class="text-danger">*</span></label>
                                        <input type="text" name="bank_name" value="{{$data->bank_name}}"
                                               class="form-control" id="name"
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="name">Branch Name <span class="text-danger">*</span></label>
                                        <input type="text" name="branch" value="{{$data->branch}}" class="form-control"
                                               id="name"
                                               required>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="account_no">Holder Name <span class="text-danger">*</span></label>
                                        <input type="text" name="holder_name" value="{{$data->holder_name}}"
                                               class="form-control" id="account_no"
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="account_no">Account No <span class="text-danger">*</span></label>
                                        <input type="number" name="account_no" value="{{$data->account_no}}"
                                               class="form-control" id="account_no"
                                               required>
                                    </div>

                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary" id="btn_update">Update</button>
                            <a class="btn btn-danger" href="{{route('vendor.profile.view')}}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end/js/croppie.js')}}"></script>
    <script>




@endpush
