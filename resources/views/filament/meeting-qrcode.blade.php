<div class="text-center p-4">
    <div class="flex justify-center mb-4">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate(url('/meeting/' . $meeting->id)) !!}
    </div>

    <div class="mt-4 space-y-3">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Scan QR code untuk mengisi absensi
        </div>

        <div class="flex flex-col gap-2">
            <div class="text-xs text-gray-500 dark:text-gray-500">
                Link Absensi:
            </div>
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3">
                <code class="text-xs break-all">{{ url('/meeting/' . $meeting->id) }}</code>
            </div>
        </div>

        <div class="flex gap-2 justify-center mt-4">
            <button type="button"
                onclick="navigator.clipboard.writeText('{{ url('/meeting/' . $meeting->id) }}').then(() => alert('Link disalin!'))"
                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                📋 Salin Link
            </button>
            <a href="{{ route('meeting.pdf', $meeting->id) }}" target="_blank"
                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                🖨️ Cetak PDF
            </a>
        </div>
    </div>
</div>