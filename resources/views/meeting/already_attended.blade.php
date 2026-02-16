@extends('layouts.public')

@section('title', 'Sudah Absensi - ' . $meeting->title)

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="logo-text">ISO Digital</div>
            <p>Sistem Absensi Terintegrasi</p>
        </div>

        <div class="meeting-badge fade-in">
            <div class="meeting-badge-label">Meeting</div>
            <div class="meeting-badge-title">{{ $meeting->title }}</div>
        </div>

        <div class="success-state fade-in" style="animation-delay: 0.1s; display: block;">
            <div class="checkmark-circle">
                <div class="background"></div>
                <div class="checkmark draw" style="display: block;"></div>
            </div>
            <h2 style="color: #1e293b; margin-bottom: 8px;">Sudah Absensi</h2>
            <p style="color: #64748b;">Anda sudah tercatat menghadiri meeting ini.</p>
            <p style="color: #64748b; font-size: 0.9em; margin-top: 5px;">{{ now()->format('d M Y') }}</p>
        </div>

        <div style="margin-top: 30px; text-align: center;">
            <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>
@endsection