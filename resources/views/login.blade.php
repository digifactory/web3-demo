@extends('layouts.app')

@section('content')
    <div id="login-screen">
        <p>Om te stemmen op jouw favoriete medewerker moet je eerst inloggen met behulp van jouw favoriete crypto-portemonnee. Je kan bijvoorbeeld gebruik maken van <a href="https://metamask.io/" target="_blank">MetaMask</a>.</p>
        <div class="d-grid">
            <button onclick="walletConnectLogin.startSession()" class="btn btn-success">Inloggen via WalletConnect</button>
        </div>
    </div>
    <div id="scan-qr-screen" class="d-none">
        <p>Gebruik de popup om in te loggen met je favoriete wallet.</p>
        <div class="d-grid">
            <button onclick="walletConnectLogin.startSession()" class="btn btn-warning">Opnieuw proberen</button>
        </div>
    </div>
    <div id="sign-prompt-screen" class="d-none">
        <p>Onderteken in je wallet app het verzoek vanuit deze website.</p>
        <div class="d-grid">
            <button onclick="walletConnectLogin.startSession()" class="btn btn-warning">Opnieuw proberen</button>
        </div>
    </div>
    <div id="sign-failed-screen" class="d-none">
        <div class="alert alert-danger">
            <p class="mb-0">Ondertekenen geweigerd of mislukt, probeer het opnieuw.</p>
        </div>
        <div class="d-grid">
            <button onclick="walletConnectLogin.startSession()" class="btn btn-warning">Opnieuw proberen</button>
        </div>
    </div>
@endsection