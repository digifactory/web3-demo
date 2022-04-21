<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
    </head>
    <body class="min-vh-100 d-flex pb-5">

        <div class="container align-self-center">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body">
                            @guest
                                <button onclick="walletConnectLogin.startSession()">Inloggen via WalletConnect</button>
                            @else
                                Ingelogd als {{ auth()->user()->address }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="position-absolute bottom-0 start-0 end-0 text-center p-2 bg-white">
            <p class="m-0">Deze pagina hoort bij een <a href="#" target="_blank">blog</a> van <a href="#" target="_blank">DigiFactory</a>, de broncode is openbaar op <a href="#">GitHub</a>.</p>
        </div>

        <script type="text/javascript">
            window.csrfToken = '{{ csrf_token() }}';
            window.guest = {{ auth()->guest() ? 'true' : 'false' }};
        </script>

        <script src="{{ mix('/js/app.js') }}"></script>
    </body>
</html>
