@isset($errors)
    @if ($errors->count() > 0 || session()->has('error') || session()->has('success') || session()->has('info') || session()->has('warning'))
        <script>
            "use strict";
            // Class definition
            var toastrJs = function () {
                // Private functions
                // basic demo
                var demo = function () {
                    toastr.options = {
                        "closeButton": true,
                        "debug": true,
                        "newestOnTop": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };
                    @if($errors->count() > 0)
                    @foreach($errors->all() as $error)
                    var $toast_{{$loop->index}} = toastr['error']('{{$error}}');
                    if (typeof $toast_{{$loop->index}} === 'undefined') {
                        return;
                    }
                    @endforeach
                    @endif
                    @if(session()->has('success'))
                    var $toast = toastr['success']('{{session()->get('success')}}');
                    if (typeof $toast === 'undefined') {
                        return;
                    }
                    @endif
                    @if(session()->has('error'))
                    var $toast = toastr['error']('{{session()->get('error')}}');
                    if (typeof $toast === 'undefined') {
                        return;
                    }
                    @endif
                    @if(session()->has('info'))
                    var $toast = toastr['info']('{{session()->get('info')}}');
                    if (typeof $toast === 'undefined') {
                        return;
                    }
                    @endif
                    @if(session()->has('warning'))
                    var $toast = toastr['warning']('{{session()->get('warning')}}');
                    if (typeof $toast === 'undefined') {
                        return;
                    }
                    @endif
                }
                return {
                    // public functions
                    init: function () {
                        demo();
                    }
                };
            }();
            jQuery(document).ready(function () {
                toastrJs.init();
            });
        </script>
    @endif
@endisset
