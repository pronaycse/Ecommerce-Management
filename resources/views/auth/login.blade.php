@extends('layouts.app')
@section('title', 'Login')

@section('content')
    <div class="narrow">
        <h1>Login</h1>
        <form method="POST" action="{{ route('login') }}" class="card form">
            @csrf

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label class="check"><input type="checkbox" name="remember" value="1"> Remember me</label>

            <div class="buttons">
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
        <p class="muted">Admin or shop owner? Use the account provided to you. (Customers do not need to log in.)</p>
    </div>
@endsection
