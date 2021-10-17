@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Last Login Details') }}</div>
                    <div class="card-body">
                        <table class="table table-striped datatables">
                            <thead>
                            <td>#</td>
                            <td>{{__("Ip address")}}</td>
                            <td>{{__("Last Login Date")}}</td>
                            <td>{{__("Last Login Time")}}</td>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.datatables').DataTable({
            serverSide: true,
            processing: true,
            ajax: "{{route("home")}}",
            columns: [
                {name: "id", data: "id"},
                {name:"ip_address", data: "ip_address"},
                {name: "last_login_date", data: "last_login_date"},
                {name: "last_login_time", data: "last_login_time"},
            ]
        })
    </script>
@endsection
