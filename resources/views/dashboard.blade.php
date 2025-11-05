@extends('layouts.app')

@section('content')
<div class="content-card">
    <div class="auth-header" style="background-color: #28a745;"> Mon Tableau de Bord
    </div>
    <div class="auth-body">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <p style="font-size: 1.1rem; text-align: center;">
            {{ __('Vous êtes connecté !') }}
        </p>
    </div>
</div>
@endsection