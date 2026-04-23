<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #F3F4F6;
        }

        .header {
            width: 100%;
            background: #1E3A8A;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
        }

        .container {
            display: flex;
            height: calc(100vh - 60px);
            justify-content: center;
            align-items: center;
        }

        .card {
            background: #FFFFFF;
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #1E3A8A;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 14px;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            margin-top: 5px;
        }

        input:focus {
            outline: none;
            border-color: #3B82F6;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #3B82F6;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #2563EB;
        }

        .link {
            text-align: center;
            margin-top: 15px;
        }

        .link a {
            color: #1E3A8A;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="header">
    Sistema de Denúncias Urbanas
</div>

<div class="container">
    @yield('content')
</div>

</body>
</html>