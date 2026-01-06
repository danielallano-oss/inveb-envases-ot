@extends('layouts.app')

@section('content')
<style>
    body {
        margin: 0;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        font-family: 'Roboto';
        min-height: 100vh;
        min-width: 100vw;
    }

    .logoLogin {
        -ms-flex-item-align: center;
        -ms-grid-row-align: center;
        align-self: center;
        margin-bottom: 20px;
    }

    .loginForm .title {
        color: #FFF;
        text-align: center;
        font-size: 20px;
        text-transform: uppercase;
        margin-bottom: 30px;
        font-weight: bold;
    }

    #filterForm {
        background-color: var(--primary);
        color: black;
        position: relative;
        padding: 15px;
        border-radius: 5px;
        -webkit-box-shadow: 0px 3px 5px 1px #aaa;
        box-shadow: 0px 3px 5px 1px #aaa;
        -webkit-transition: all ease 0.5s;
        -o-transition: all ease 0.5s;
        transition: all ease 0.5s;
    }

    #filterForm .form-group>label {
        display: block;
        text-transform: uppercase;
        margin-bottom: 0;
        color: #FFF;
        font-size: 12px;
    }

    .loginForm label {
        text-align: center;
    }

    #filterForm .form-group>input {

        width: 100%;
        border-radius: 3px;
        padding: 5px 10px;

    }

    .loginForm input {

        text-align: center;

    }

    .loginForm .message {

        color: #FFF;
        text-align: center;
        margin-top: 30px;

    }

    .loginForm button {

        width: 200px;
        margin-top: 30px;
        -ms-flex-item-align: center;
        -ms-grid-row-align: center;
        align-self: center;

    }

    .loginForm {
        background-color: #08375c;
        width: 400px;
        -ms-flex-item-align: center;
        align-self: center;
        padding: 15px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
    }

    .sbtn.submit {
        background-color: #218838;
        color: #fff;
        border: 0;
        border-radius: 2px;
        padding: 2px;
    }

    .form-group>input,
    .form-group>.inputDisabled,
    .form-group>textarea {
        border: none;
        font-size: 12px;
        border-bottom: 1px solid #ccc;
        background: transparent;
        padding: 1px 7px 2px;
        color: white;
    }

    input:focus,
    select:focus,
    textarea:focus,
    button:focus {
        outline: none;
    }
</style>
<form class="loginForm" method="POST" action="{{ route('recoveryEmail') }}" id="filterForm">
    <div class="logoLogin">
        <img src="img/logo-cmpc-white.svg" alt="CMPC" class="align-baseline" width="240" height="100">
    </div>
    @csrf
    <div class="title">Restablecer Contraseña</div>
    <div class="form-group">
        <label style="font-weight: 700">Confirmar RUT</label>
        <input id="rut" type="text" class=" @error('rut') is-invalid @enderror" name="rut" value="" required='true'>
    
    </div>     
    
    @error('rut')
        <span class="" role="alert" style="color: red; text-align: center;">
            <strong>{{ $message }}</strong>
            <br><br>
        </span>
    @enderror

    <button type="submit" class="sbtn submit" style="cursor:pointer;">
        Restablecer
    </button>
</form>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>
    $(document).ready(function() {
        $("#rut").focusout(function() {
            if (this.value > 1) {
                this.value = this.value.replace(/-/g, "");
                var rut = this.value;
                this.value = rut.substr(0, rut.length - 1) + "-" + rut.substr(-1);
            }
        });
    });
</script>
<!-- Cambiar el color primario segun sea produccion o local -->
@if (env('APP_ENV') == "local")
<script>
    let root = document.documentElement;
    //   root.style.setProperty('--primary', "#3aaa35")
    root.style.setProperty('--primary', "#17a2b9")
</script>
@endif
@endsection