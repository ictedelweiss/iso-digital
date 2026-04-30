@extends('layouts.public')

@section('title', 'ICT Helpdesk - Buat Tiket')

@section('content')
<div
    style="min-height: 100vh; background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); padding: 40px 16px;">
    <div style="max-width: 720px; margin: 0 auto;">

        {{-- Header --}}
        <div style="text-align: center; margin-bottom: 32px;">
            <div
                style="display: inline-flex; align-items: center; justify-content: center; width: 64px; height: 64px; background: linear-gradient(135deg, #2563eb, #3b82f6); border-radius: 16px; margin-bottom: 16px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="white" width="32" height="32">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                </svg>
            </div>
            <h1
                style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 28px; font-weight: 700; color: #f8fafc; margin: 0 0 8px 0;">
                ICT Helpdesk
            </h1>
            <p style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 15px; color: #94a3b8; margin: 0;">
                Yayasan Sinar Putih Edelweiss — Laporkan masalah IT Anda
            </p>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
        <div
            style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
            <p style="color: #fca5a5; font-size: 14px; font-weight: 600; margin: 0 0 8px 0;">⚠️ Terdapat kesalahan:</p>
            <ul style="color: #fca5a5; font-size: 13px; margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Form Card --}}
        <form action="{{ url()->current() }}" method="POST" enctype="multipart/form-data"
            style="background: rgba(30, 41, 59, 0.8); backdrop-filter: blur(16px); border: 1px solid rgba(148, 163, 184, 0.1); border-radius: 16px; padding: 32px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
            @csrf

            {{-- Informasi Pelapor --}}
            @if(!Auth::check())
            <div style="margin-bottom: 24px;">
                <h3
                    style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 600; color: #e2e8f0; margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 1px solid rgba(148, 163, 184, 0.15);">
                    👤 Informasi Pelapor
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label
                            style="display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px;">Nama</label>
                        <input type="text" name="reporter_name" value="{{ old('reporter_name') }}"
                            style="width: 100%; padding: 10px 14px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 8px; color: #f8fafc; font-size: 14px; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#3b82f6'"
                            onblur="this.style.borderColor='rgba(148, 163, 184, 0.2)'" placeholder="Nama lengkap Anda"
                            required>
                    </div>
                    <div>
                        <label
                            style="display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px;">Email</label>
                        <input type="email" name="reporter_email" value="{{ old('reporter_email') }}"
                            style="width: 100%; padding: 10px 14px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 8px; color: #f8fafc; font-size: 14px; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#3b82f6'"
                            onblur="this.style.borderColor='rgba(148, 163, 184, 0.2)'"
                            placeholder="email@edelweiss.sch.id" required>
                    </div>
                </div>
            </div>
            @else
            <div
                style="margin-bottom: 24px; background: rgba(37, 99, 235, 0.1); border: 1px solid rgba(37, 99, 235, 0.2); border-radius: 10px; padding: 14px 18px;">
                <p style="color: #93c5fd; font-size: 13px; margin: 0;">
                    👋 Login sebagai <strong style="color: #bfdbfe;">{{ Auth::user()->name }}</strong>
                    ({{ Auth::user()->email }})
                </p>
            </div>
            @endif

            {{-- Detail Tiket --}}
            <div style="margin-bottom: 24px;">
                <h3
                    style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 16px; font-weight: 600; color: #e2e8f0; margin: 0 0 16px 0; padding-bottom: 8px; border-bottom: 1px solid rgba(148, 163, 184, 0.15);">
                    🎫 Detail Tiket
                </h3>

                {{-- Subject --}}
                <div style="margin-bottom: 16px;">
                    <label
                        style="display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px;">Subject
                        <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                        style="width: 100%; padding: 10px 14px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 8px; color: #f8fafc; font-size: 14px; outline: none; box-sizing: border-box; transition: border-color 0.2s;"
                        onfocus="this.style.borderColor='#3b82f6'"
                        onblur="this.style.borderColor='rgba(148, 163, 184, 0.2)'" placeholder="Ringkasan masalah Anda"
                        required>
                </div>

                {{-- Category & Priority --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label
                            style="display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px;">Kategori
                            <span style="color: #ef4444;">*</span></label>
                        <select name="category" required
                            style="width: 100%; padding: 10px 14px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 8px; color: #f8fafc; font-size: 14px; outline: none; box-sizing: border-box; appearance: auto;">
                            <option value="" disabled {{ old('category') ? '' : 'selected' }}>-- Pilih Kategori --
                            </option>
                            <option value="Hardware" {{ old('category')=='Hardware' ? 'selected' : '' }}>🖥️ Hardware
                            </option>
                            <option value="Software" {{ old('category')=='Software' ? 'selected' : '' }}>💿 Software
                            </option>
                            <option value="Network" {{ old('category')=='Network' ? 'selected' : '' }}>🌐 Network
                            </option>
                            <option value="Account" {{ old('category')=='Account' ? 'selected' : '' }}>🔑 Account
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            style="display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px;">Prioritas
                            <span style="color: #ef4444;">*</span></label>
                        <select name="priority" required
                            style="width: 100%; padding: 10px 14px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 8px; color: #f8fafc; font-size: 14px; outline: none; box-sizing: border-box; appearance: auto;">
                            <option value="Low" {{ old('priority')=='Low' ? 'selected' : '' }}>🟢 Low</option>
                            <option value="Medium" {{ old('priority', 'Medium' )=='Medium' ? 'selected' : '' }}>🟡
                                Medium</option>
                            <option value="High" {{ old('priority')=='High' ? 'selected' : '' }}>🟠 High</option>
                            <option value="Critical" {{ old('priority')=='Critical' ? 'selected' : '' }}>🔴 Critical
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Description --}}
                <div style="margin-bottom: 16px;">
                    <label
                        style="display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px;">Deskripsi
                        Masalah <span style="color: #ef4444;">*</span></label>
                    <textarea name="description" rows="5" required
                        style="width: 100%; padding: 10px 14px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 8px; color: #f8fafc; font-size: 14px; outline: none; box-sizing: border-box; resize: vertical; transition: border-color 0.2s; font-family: 'Plus Jakarta Sans', sans-serif;"
                        onfocus="this.style.borderColor='#3b82f6'"
                        onblur="this.style.borderColor='rgba(148, 163, 184, 0.2)'"
                        placeholder="Jelaskan masalah yang Anda alami secara detail...">{{ old('description') }}</textarea>
                </div>

                {{-- Attachment --}}
                <div style="margin-bottom: 16px;">
                    <label
                        style="display: block; font-size: 13px; font-weight: 500; color: #94a3b8; margin-bottom: 6px;">📎
                        Lampiran (opsional)</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
                        style="width: 100%; padding: 10px 14px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(148, 163, 184, 0.2); border-radius: 8px; color: #94a3b8; font-size: 13px; box-sizing: border-box;">
                    <p style="color: #64748b; font-size: 11px; margin: 6px 0 0 0;">
                        Format: JPG, PNG, PDF, DOC. Maks 5MB.
                    </p>
                </div>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                style="width: 100%; padding: 14px; background: linear-gradient(135deg, #2563eb, #3b82f6); color: #ffffff; font-size: 16px; font-weight: 600; border: none; border-radius: 10px; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.3s; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);"
                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(37, 99, 235, 0.5)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(37, 99, 235, 0.4)'">
                🚀 Kirim Tiket
            </button>
        </form>

        {{-- Footer --}}
        <p style="text-align: center; color: #475569; font-size: 12px; margin-top: 24px;">
            ICT Helpdesk • Yayasan Sinar Putih Edelweiss<br>
            Butuh bantuan darurat? Hubungi <a href="mailto:ict@edelweiss.sch.id"
                style="color: #3b82f6; text-decoration: none;">ict@edelweiss.sch.id</a>
        </p>
    </div>
</div>
@endsection
