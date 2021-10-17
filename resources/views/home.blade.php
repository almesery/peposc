@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    <table class="table table-striped datatables">
                        <thead>
                            <td>#</td>
                            <td>{{__("Last Login Date")}}</td>
                            <td>{{__("Last Login Time")}}</td>
                        </thead>
                        <tbody>
                        @foreach()
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
