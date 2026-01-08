@extends('layouts.public')

@section('title', 'Absensi Meeting - ' . $meeting->title)

@section('content')
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

        <form id="attendanceForm" class="fade-in" style="animation-delay: 0.2s;">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap Anda" required>
            </div>

            <div class="form-group">
                <label for="division">Divisi / Departemen</label>
                <select id="division" name="division" required>
                    <option value="" disabled selected>Pilih Divisi...</option>
                    <option value="KB/TK">KB/TK</option>
                    <option value="SD">SD</option>
                    <option value="SMP">SMP</option>
                    <option value="PKBM">PKBM</option>
                    <option value="Customer Service Officer">Customer Service Officer</option>
                    <option value="Finance & Accounting">Finance & Accounting</option>
                    <option value="HRD">HRD</option>
                    <option value="ICT">ICT</option>
                    <option value="Management">Management</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Operator">Operator</option>
                </select>
            </div>

            <div class="form-group">
                <div class="checkbox-wrapper" onclick="document.getElementById('isGuest').click()">
                    <input type="checkbox" id="isGuest" onclick="event.stopPropagation()">
                    <span class="checkbox-label">Saya Tamu Eksternal / External Guest</span>
                </div>
            </div>

            <div class="form-group">
                <label>Tanda Tangan Digital</label>
                <div class="signature-area">
                    <canvas id="signatureCanvas" class="signature-canvas"></canvas>
                </div>
                <button type="button" class="btn btn-secondary" onclick="clearSignature()">
                    🗑️ Hapus Tanda Tangan
                </button>
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
            // High DPI adjustment
            const dpr = window.devicePixelRatio || 1;
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
            if(e.type === 'touchstart') e.preventDefault();
            
            const coords = getCoordinates(e);
            lastX = coords.x;
            lastY = coords.y;
        }

        function draw(e) {
            if (!isDrawing) return;
            if(e.type === 'touchmove') e.preventDefault();

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

        // Form submission
        document.getElementById('attendanceForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const errorMessage = document.getElementById('errorMessage');
            const submitBtn = document.getElementById('submitBtn');

            if (isCanvasEmpty()) {
                errorMessage.textContent = 'Silakan tanda tangan terlebih dahulu.';
                errorMessage.style.display = 'block';
                return;
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
    </script>
@endpush