@extends('layouts.public')

@section('title', 'Absensi Meeting - ' . $meeting->title)

@section('content')
<style>
    .choice-screen {
        text-align: center;
        padding: 20px 0;
    }

    .choice-btn {
        display: block;
        width: 100%;
        padding: 16px;
        margin-bottom: 12px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-ms {
        background: #2f2f2f;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-ms:hover {
        background: #444;
        transform: translateY(-2px);
    }

    .btn-guest {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-guest:hover {
        background: #e2e8f0;
    }

    .divider {
        margin: 16px 0;
        display: flex;
        align-items: center;
        color: #94a3b8;
        font-size: 14px;
    }

    .divider::before,
    .divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    .divider span {
        padding: 0 12px;
    }

    .info-box {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        color: #0369a1;
    }

    .info-box strong {
        display: block;
        color: #0c4a6e;
        font-size: 16px;
        margin-bottom: 4px;
    }

    .info-box-icon {
        font-size: 24px;
        margin-right: 12px;
        float: left;
    }

    .info-box-content {
        overflow: hidden;
    }

    .change-sig-btn {
        color: #0284c7;
        text-decoration: underline;
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        margin-top: 8px;
        font-size: 14px;
        font-family: inherit;
    }

    .change-sig-btn:hover {
        color: #0369a1;
    }
</style>
<div class="card">
    <div class="card-header">
        <div class="logo-text">ISO Digital</div>
        <p>Sistem Absensi Terintegrasi</p>
    </div>

    <div class="meeting-badge fade-in" style="animation-delay: 0.1s;">
        <div class="meeting-badge-label">Meeting</div>
        <div class="meeting-badge-title">{{ $meeting->title }}</div>
    </div>

    <div id="errorMessage" class="error-message"></div>

    @if(!$isLoggedIn)
    <div id="choiceScreen" class="choice-screen fade-in" style="animation-delay: 0.2s;">
        <p style="margin-bottom: 20px; color: #64748b;">Silakan pilih metode absensi:</p>

        <a href="{{ route('auth.microsoft', ['redirect_to' => url()->current()]) }}" class="choice-btn btn-ms">
            <img src="https://authjs.dev/img/providers/microsoft.svg" width="20" alt="MS">
            Staff Edelweiss (Login M365)
        </a>

        <div class="divider"><span>ATAU</span></div>

        <button type="button" class="choice-btn btn-guest" onclick="showGuestForm()">
            Tamu / Eksternal (Isi Manual)
        </button>
    </div>
    @endif

    <form id="attendanceForm" class="fade-in" style="animation-delay: 0.2s; @if(!$isLoggedIn) display: none; @endif">
        @if($isLoggedIn)
        <div class="info-box">
            <div class="info-box-icon">👤</div>
            <div class="info-box-content">
                <strong>Terdeteksi sebagai Staff</strong>
                <p class="text-sm">Data Anda akan otomatis terisi untuk verifikasi.</p>
            </div>
        </div>
        @endif

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap Anda"
                value="{{ $user ? ($user->display_name ?: $user->username) : '' }}" {{ $isLoggedIn ? 'readonly'
                : 'required' }} style="{{ $isLoggedIn ? 'background: #f8fafc; color: #64748b;' : '' }}">
        </div>

        <div class="form-group">
            <label for="division">Divisi / Departemen</label>
            <select id="division" name="division" required {{ $isLoggedIn ? 'disabled' : '' }}
                style="{{ $isLoggedIn ? 'background: #f8fafc; color: #64748b;' : '' }}">
                <option value="" disabled {{ !$user || !$user->division ? 'selected' : '' }}>Pilih Divisi...</option>
                @if($isLoggedIn)
                <option value="{{ $user->division ?: 'Staff' }}" selected>{{ $user->division ?: 'Staff' }}</option>
                @endif
                @foreach(config('approval.departments', []) as $department)
                <option value="{{ $department }}">{{ $department }}</option>
                @endforeach
                <option value="Tamu / Eksternal">Tamu / Eksternal</option>
            </select>
            @if($isLoggedIn)
            <input type="hidden" name="division" value="{{ $user->division ?: 'Staff' }}">
            @endif
        </div>

        <div class="form-group">
            <label>Tanda Tangan Digital</label>

            @if(isset($hasSignature) && $hasSignature)
            <div id="storedSignatureInfo" class="info-box">
                <div class="info-box-icon">✍️</div>
                <div class="info-box-content">
                    <strong>Tanda Tangan Tersimpan</strong>
                    <p class="text-sm">Menggunakan tanda tangan dari profil Anda.</p>
                    <button type="button" class="change-sig-btn" onclick="showSignaturePad()">
                        Gunakan Tanda Tangan Baru
                    </button>
                </div>
            </div>

            <div id="signatureContainer" style="display: none;">
                <div class="signature-area">
                    <canvas id="signatureCanvas" class="signature-canvas"></canvas>
                </div>
                <button type="button" class="btn btn-secondary" onclick="clearSignature()">
                    🗑️ Hapus
                </button>
                <button type="button" class="btn btn-link" onclick="cancelChangeSignature()" style="margin-top: 5px;">
                    Batal
                </button>
            </div>
            @else
            <div class="signature-area">
                <canvas id="signatureCanvas" class="signature-canvas"></canvas>
            </div>
            <button type="button" class="btn btn-secondary" onclick="clearSignature()">
                🗑️ Hapus Tanda Tangan
            </button>
            @endif
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary">
            Simpan Kehadiran
        </button>
    </form>

    <div id="successMessage" class="success-state">
        <div class="checkmark-circle">
            <div class="background"></div>
            <div class="checkmark draw"></div>
        </div>
        <h2 style="color: #1e293b; margin-bottom: 8px;">Berhasil!</h2>
        <p style="color: #64748b;">Terima kasih, kehadiran Anda telah tercatat.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Signature Canvas Setup
    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;

    // Set canvas size
    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        if (rect.width === 0) return; // Don't resize if not visible

        // High DPI adjustment
        const dpr = window.devicePixelRatio || 1;

        // Only update if dimensions actually changed to avoid clearing canvas unnecessarily
        if (canvas.width !== rect.width * dpr || canvas.height !== 180 * dpr) {
            canvas.width = rect.width * dpr;
            canvas.height = 180 * dpr; // Fixed height in CSS

            ctx.scale(dpr, dpr);
            canvas.style.width = rect.width + 'px';
            canvas.style.height = '180px';

            ctx.strokeStyle = '#0f172a';
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
        }
    }

    // Initialize with slight delay to ensure layout is ready
    setTimeout(resizeCanvas, 100);
    window.addEventListener('resize', resizeCanvas);

    function getCoordinates(e) {
        const rect = canvas.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;

        let clientX, clientY;

        if (e.touches && e.touches.length > 0) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }

        return {
            x: (clientX - rect.left),
            y: (clientY - rect.top)
        };
    }

    function startDrawing(e) {
        isDrawing = true;
        // Prevent scrolling on touch
        if (e.type === 'touchstart') e.preventDefault();

        const coords = getCoordinates(e);
        lastX = coords.x;
        lastY = coords.y;
    }

    function draw(e) {
        if (!isDrawing) return;
        if (e.type === 'touchmove') e.preventDefault();

        const coords = getCoordinates(e);
        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(coords.x, coords.y);
        ctx.stroke();
        lastX = coords.x;
        lastY = coords.y;
    }

    function stopDrawing() {
        isDrawing = false;
    }

    function clearSignature() {
        const dpr = window.devicePixelRatio || 1;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function isCanvasEmpty() {
        const blank = document.createElement('canvas');
        const dpr = window.devicePixelRatio || 1;
        blank.width = canvas.width;
        blank.height = canvas.height;
        return canvas.toDataURL() === blank.toDataURL();
    }

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    canvas.addEventListener('touchstart', startDrawing, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDrawing);

    // Choice Screen Logic
    function showGuestForm() {
        const choiceScreen = document.getElementById('choiceScreen');
        const attendanceForm = document.getElementById('attendanceForm');
        const divisionSelect = document.getElementById('division');

        if (choiceScreen) choiceScreen.style.display = 'none';
        if (attendanceForm) attendanceForm.style.display = 'block';

        // Default to Guest for this flow
        if (divisionSelect) divisionSelect.value = 'Tamu / Eksternal';

        // Brief timeout to ensure DOM layout has calculated dimensions
        setTimeout(resizeCanvas, 50);
    }

    // Form submission
    document.getElementById('attendanceForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const errorMessage = document.getElementById('errorMessage');
        const submitBtn = document.getElementById('submitBtn');

        const isSignatureHidden = document.getElementById('signatureContainer') && document.getElementById('signatureContainer').style.display === 'none';

        if (isCanvasEmpty() && (!hasStoredSignature || !isSignatureHidden)) {
            if (!hasStoredSignature) {
                errorMessage.textContent = 'Silakan tanda tangan terlebih dahulu.';
                errorMessage.style.display = 'block';
                return;
            } else if (!isSignatureHidden) {
                // If they clicked "Change" but didn't sign
                errorMessage.textContent = 'Silakan tanda tangan atau batalkan perubahan.';
                errorMessage.style.display = 'block';
                return;
            }
        }

        const name = document.getElementById('name').value.trim();
        const division = document.getElementById('division').value;
        // Downscale for saving
        const signature = canvas.toDataURL('image/png');

        if (!name || !division) {
            errorMessage.textContent = 'Nama dan Divisi wajib diisi.';
            errorMessage.style.display = 'block';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...'; // No emoji for button text, simpler
        errorMessage.style.display = 'none';

        try {
            const response = await fetch('{{ route("meeting.submit", $meeting->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    name: name,
                    division: division,
                    signature: signature,
                }),
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('attendanceForm').style.display = 'none';
                const successMsg = document.getElementById('successMessage');
                successMsg.style.display = 'block';

                // Trigger confetti
                if (window.confetti) {
                    confetti({
                        particleCount: 150,
                        spread: 70,
                        origin: { y: 0.6 },
                        colors: ['#0d9488', '#2563eb', '#10b981', '#f59e0b']
                    });
                }
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            errorMessage.textContent = error.message || 'Gagal menyimpan. Silakan coba lagi.';
            errorMessage.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan Kehadiran';
        }
    });

    // Toggle Signature Pad
    function showSignaturePad() {
        document.getElementById('storedSignatureInfo').style.display = 'none';
        document.getElementById('signatureContainer').style.display = 'block';
        resizeCanvas();
    }

    function cancelChangeSignature() {
        document.getElementById('storedSignatureInfo').style.display = 'block';
        document.getElementById('signatureContainer').style.display = 'none';
        clearSignature();
    }

    // Updated Validation for stored signature
    const hasStoredSignature = {{ isset($hasSignature) && $hasSignature ? 'true' : 'false' }};
</script>
@endpush
