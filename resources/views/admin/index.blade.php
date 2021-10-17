@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('All Users') }}</div>
                    <div class="card-body">
                        <table class="table table-striped datatables">
                            <thead>
                            <td>#</td>
                            <td>{{__("Name")}}</td>
                            <td>{{__("Email")}}</td>
                            <td>{{__("Last Login")}}</td>
                            <td>{{__("Action")}}</td>
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
        let datatable= $('.datatables').DataTable({
            serverSide: true,
            processing: true,
            ajax: "{{route("user.index")}}",
            columns: [
                {name: "id", data: "id"},
                {name: "name", data: "name"},
                {name: "email", data: "email"},
                {name: "last_login_date", data: "last_login_date"},
                {name: "actions", data: "actions", searchable: false, sortable: false},
            ]
        });
        $(function () {
            $(document).on('click', '.delete_record', function () {
                if (confirm("Are You Sure ? ")) {
                    $.ajax({
                        url: "/user/" +  $(this).data('id'),
                        method: "DELETE",
                        data: {
                            _token: "{{csrf_token()}}"
                        },
                        type:"DELETE",
                        success: function (response) {
                            if(response.success) {
                                toastr.success(response.message)
                            } else{
                                toastr.error(response.message)
                            }
                            datatable.ajax.reload();
                        },
                    })
                }
            })
        })
    </script>
@endsection
