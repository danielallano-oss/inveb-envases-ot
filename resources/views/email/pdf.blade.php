<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Cotizacion en espera de Aprobación</title>
    <style>
        a:hover,
        a {
            /* color: #267023; */
            text-decoration: none;
        }

        .btn-success:hover {
            color: #fff;
            background-color: #218838 !important;
            border-color: #1e7e34 !important;
        }

        .btn:hover {
            color: #343a40;
            text-decoration: none;
        }

        [type=button]:not(:disabled),
        [type=reset]:not(:disabled),
        [type=submit]:not(:disabled),
        button:not(:disabled) {
            cursor: pointer;
        }

        .float-right {
            float: right !important;
        }

        .btn-success {
            color: #fff !important;
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            color: #343a40;
            text-align: center;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }
    </style>
</head>

<body>
    <table style="width:100%;">
        <tbody style="text-align: center;font-family: Arial!important">
            <tr style="height: 50px;width: 100%;"></tr>
            <tr>
                <td style="color: #3aaa35;font-size: 20px;font-weight: bold;padding: 20px;">
                    COTIZACION CMPC
                </td>
            </tr>
            <tr>
                <td style="padding: 50px 30px;line-height: 2; text-align:left; color: black; font-size: 14px;">
                    <p>Estimado/a <strong>{{$data["nombre"]}}</strong> <b></b>, el vendedor <strong>{{$data["vendedor"]["fullname"]}}</strong> ha generado una cotizacion la cual se encuentra adjunta en este correo.</p>
                   
                    <p>Saludos coordiales</p>
                </td>
            </tr>
            <tr style="height: 50px;width: 100%;"></tr>
            <tr>
                <td style="padding-bottom: 10px;font-size: 12px;text-align: center; color:gray;">
                    Email generado automáticamente. Favor de no responder.
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>