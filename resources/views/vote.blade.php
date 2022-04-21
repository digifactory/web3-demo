@extends('layouts.app')

@section('content')
    <h5 class="text-center">Stem op je favoriete medewerker:</h5>
    <p class="text-center text-muted">Nadat je gestemd hebt krijg je de huidige stand te zien.</p>
    <x-employees/>
    <hr/>
    <p class="text-break text-muted small">Je bent ingelogd met publiek adres: <code>{{ auth()->user()->address }}</code>.</p>
    <form action="{{ route('logout') }}" method="post" onsubmit="return confirm('Weet je zeker dat je wilt uitloggen?')">
        {{ csrf_field() }}
        <div class="d-grid">
            <button type="submit" class="btn btn-danger">Uitloggen</button>
        </div>
    </form>
@endsection