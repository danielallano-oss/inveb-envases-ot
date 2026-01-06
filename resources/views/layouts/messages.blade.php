{{--mensajes de validacion--}}
@if(count($errors)>0)
@section('notifyToast')
@foreach($errors->all() as $error)

<script>
    $.notify({
        message: '<p class="text-center">{{$error}}</p>'
    }, {
        type: 'danger',
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated bounceOutUp'
        },
        placement: {
            from: "top",
            align: "center"
        }
    });
</script>
@endforeach

@endsection
@endif
{{--MENSAJES success--}}
@if(isset($success) || Session::get('success'))
@section('notifyToast')

<script>
    $.notify({
        message: `<p style="font-size:16px"  class="text-center">{{ isset($success) ? $success : session::get('success')}}</p>`
    }, {
        type: 'success',
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated bounceOutUp'
        },
        // delay: 500000,
        placement: {
            from: "top",
            align: "center"
        }
    });
</script>
@endsection
@endif
{{--MENSAJES WARNING--}}
@if(isset($warning) || Session::get('warning'))
@section('notifyToast')
<script>
    $.notify({
        message: `<p  class="text-center">{{ isset($warning) ? $warning : session::get('warning')}}</p>`
    }, {
        type: 'warning',
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated bounceOutUp'
        },
        placement: {
            from: "top",
            align: "center"
        }
    });
</script>
@endsection
@endif
{{--MENSAJES Danger--}}
@if(isset($danger) || Session::get('danger'))
@section('notifyToast')
<script>
    $.notify({
        message: `<p  class="text-center">{{ isset($danger) ? $danger : session::get('danger')}}</p>`
    }, {
        type: 'danger',
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated bounceOutUp'
        },
        placement: {
            from: "top",
            align: "center"
        }
    });
</script>
@endsection
@endif

{{--MENSAJES error--}}
@if(session('error'))
@section('notifyToast')
<script>
    $.notify({
        message: `<p class="text-center">{{session('error')}}</p>`
    }, {
        type: 'error',
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated bounceOutUp'
        },
        placement: {
            from: "top",
            align: "center"
        }
    });
</script>
@endsection
@endif

{{--MENSAJES STATUS--}}
@if(session('status'))
@section('notifyToast')
<style>
    .alert-success {
        background-color: #164f8a !important;
        border-color: #164f8a !important;
        color: white !important;
    }
</style>
<script>
    $.notify({
        message: `<p class="text-center">{{session('status')}}</p>`
    }, {
        type: 'success',
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated bounceOutUp'
        },
        placement: {
            from: "top",
            align: "center"
        }
    });
</script>
@endsection
@endif