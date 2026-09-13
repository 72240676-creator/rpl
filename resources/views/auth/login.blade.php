@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <div class="login-container">
        <h2>Masuk ke Akun Anda</h2>
        
        <form action="/login" method="POST">
            @csrf 
            
            <div class="input-group">
                <label for="email">Email</label>
                <!-- Menambahkan old('email') agar teks tidak hilang jika error -->
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                <!-- Area kemunculan pesan error email -->
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                <!-- Area kemunculan pesan error password (seperti minimal 8 huruf) -->
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn-submit">Login</button>
                <div class="divider"></div>
            <a href="/register" class="btn-outline">Buat Akun Baru</a>
        </form>
    </div>
@endsection