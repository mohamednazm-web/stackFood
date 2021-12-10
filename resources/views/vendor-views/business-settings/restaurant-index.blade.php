@extends('layouts.vendor.app')

@section('title','Settings')

@push('css_or_js')
<link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
<style>    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 15px;
        width: 15px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #377dff;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #377dff;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header p-0">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{__('messages.restaurant')}} {{__('messages.setup')}}</h1>
                </div>
                <div class="col-md-12 mb-3 mt-3">
                    <div class="card">
                        <div class="card-body" style="padding-bottom: 12px">
                            <div class="d-flex flex-row justify-content-between ">

                                    <h5 class="text-capitalize">
                                        <i class="tio-settings-outlined"></i>
                                        {{__('messages.restaurant_temporarily_closed_title')}}
                                    </h5>

                                    <label class="switch toggle-switch-lg">
                                        <input type="checkbox" class="toggle-switch-input" onclick="restaurant_open_status(this)"
                                            {{$restaurant->active ?'':'checked'}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card h-100">
                    <div class="card-header">
                        {{__('messages.restaurant')}} {{__('messages.settings')}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="schedule_order">
                                        <span class="pr-2">{{__('messages.scheduled')}} {{__('messages.order')}}:</span> 
                                        <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->schedule_order?0:1, 'schedule_order'])}}'" id="schedule_order" {{$restaurant->schedule_order?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="delivery">
                                        <span class="pr-2">{{__('messages.delivery')}}:</span> 
                                        <input type="checkbox" name="delivery" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->delivery?0:1, 'delivery'])}}'" id="delivery" {{$restaurant->delivery?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12">
                                <div class="form-group">
                                    <label class="toggle-switch toggle-switch-sm d-flex justify-content-between border border-secondary rounded px-4 form-control" for="take_away">
                                        <span class="pr-2 text-capitalize">{{__('messages.take_away')}}:</span> 
                                        <input type="checkbox" class="toggle-switch-input" onclick="location.href='{{route('vendor.business-settings.toggle-settings',[$restaurant->id,$restaurant->take_away?0:1, 'take_away'])}}'" id="take_away" {{$restaurant->take_away?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <form action="{{route('vendor.business-settings.update-setup',[$restaurant['id']])}}" method="post"
                            enctype="multipart/form-data">
                            @csrf 
                            <div class="row">
                                <div class="col-sm-{{$restaurant->self_delivery_system?'6':'4'}} col-12">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize" for="title">{{__('messages.opening')}} {{__('messages.time')}}</label>
                                        <input type="time" id="closeing_time" class="form-control" name="opening_time" value="{{$restaurant->opening_time?$restaurant->opening_time->format('H:i:s'):''}}">
                                    </div>
                                </div>
                                <div class="col-sm-{{$restaurant->self_delivery_system?'6':'4'}} col-12">
                                    <label class="input-label text-capitalize" for="title">{{__('messages.closing')}} {{__('messages.time')}}</label>
                                    <input type="time" id="closeing_time" class="form-control"  name="closeing_time" value="{{$restaurant->closeing_time?$restaurant->closeing_time->format('H:i:s'):''}}">
                                </div>
                                <div class="col-sm-{{$restaurant->self_delivery_system?'6':'4'}} col-12">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize" for="title">{{__('messages.minimum')}} {{__('messages.order')}} {{__('messages.amount')}}</label>
                                        <input type="number" name="minimum_order" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$restaurant->minimum_order??'0'}}"> 
                                    </div>
                                </div>
                                @if($restaurant->self_delivery_system)
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label text-capitalize" for="title">{{__('messages.delivery_charge')}}</label>
                                        <input type="number" name="delivery_charge" step="0.01" min="0" max="100000" class="form-control" placeholder="100" value="{{$restaurant->delivery_charge??'0'}}"> 
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-sm-6 col-12">
                                    <label class="input-label text-capitalize" for="off_day">{{__('messages.weekly_off_day')}}</label>
                                    <select name="off_day[]" class="form-control js-select2-custom" id="off_day" multiple="multiple" data-placeholder="{{__('messages.select_off_day')}}" data-minimum-results-for-search="-1">
                                        <option value="1" {{str_contains($restaurant->off_day, 1)?'selected':''}}>{{__('messages.monday')}}</option>
                                        <option value="2" {{str_contains($restaurant->off_day, 2)?'selected':''}}>{{__('messages.tuesday')}}</option>
                                        <option value="3" {{str_contains($restaurant->off_day, 3)?'selected':''}}>{{__('messages.wednesday')}}</option>
                                        <option value="4" {{str_contains($restaurant->off_day, 4)?'selected':''}}>{{__('messages.thirsday')}}</option>
                                        <option value="5" {{str_contains($restaurant->off_day, 5)?'selected':''}}>{{__('messages.friday')}}</option>
                                        <option value="6" {{str_contains($restaurant->off_day, 6)?'selected':''}}>{{__('messages.saturday')}}</option>
                                        <option value="7" {{str_contains($restaurant->off_day, 7)?'selected':''}}>{{__('messages.sunday')}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group p-2 border">
                                        <label class="d-flex justify-content-between switch toggle-switch-sm text-dark" for="gst_status">
                                            <span>{{__('messages.gst')}} <span class="input-label-secondary" title="{{__('messages.gst_status_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.gst_status_warning')}}"></span></span>
                                            <input type="checkbox" class="toggle-switch-input" name="gst_status" id="gst_status" value="1" {{$restaurant->gst_status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                        <input type="text" id="gst" name="gst" class="form-control" value="{{$restaurant->gst_code}}" {{isset($restaurant->gst_status)?'':'readonly'}}>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">{{__('messages.update')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        function restaurant_open_status(e) {
            Swal.fire({
                title: '{{__('messages.are_you_sure')}}',
                text: '{{$restaurant->active?__('messages.you_want_to_temporarily_close_this_restaurant'):__('messages.you_want_to_open_this_restaurant')}}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: '{{__('messages.no')}}',
                confirmButtonText: '{{__('messages.yes')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: '{{route('vendor.business-settings.update-active-status')}}',
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            toastr.success(data.message);
                        },
                        complete: function () {
                            $('#loading').hide();
                        },
                    });
                } else {
                    e.checked = !e.checked;
                }
            })
        };


        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });

        $(document).on('ready', function () {
            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            $("#gst_status").on('change', function(){
                if($("#gst_status").is(':checked')){
                    $('#gst').removeAttr('readonly');
                } else {
                    $('#gst').attr('readonly', true);
                }
            });
        });
    </script>
@endpush
