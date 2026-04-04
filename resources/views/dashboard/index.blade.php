@extends('layouts.app')

@section('content')
<div class="p-6 text-white">
    <h1>Dashboard</h1>
    <p>Welcome, {{ $user->name }}</p>
</div>
@endsection