@extends('layouts.app')
@section('title', 'Daftar Akun')
@section('content')
    <div class="login-container">
        <h2>Pendaftaran Pengguna Baru</h2>
        
       <form action="/register" method="POST">
            @csrf 
            
            <div class="input-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required>
                @error('nama') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <label for="nomor_telepon">Nomor Telepon</label>
                <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ old('nomor_telepon') }}" required>
                @error('nomor_telepon') <span class="error-text">{{ $message }}</span> @enderror
            </div>
            
            <div class="input-group">
                <label for="password">Password (Min. 8 Karakter)</label>
                <input type="password" name="password" id="password" required>
                @error('password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
            
            <button type="submit" class="btn-submit">Daftar Sekarang</button>
            
            <a href="/login" class="login-link">Sudah punya akun? <span>Login di sini</span></a>
        </form>
    </div>
@endsection