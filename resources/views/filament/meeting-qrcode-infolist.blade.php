<div class="text-center">
    <div class="flex justify-center mb-4">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate(url('/meeting/' . $getRecord()->id)) !!}
    </div>

    <div class="mt-4 space-y-2">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Link Absensi:
        </div>
        <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-3">
            <a href="{{ url('/meeting/' . $getRecord()->id) }}" target="_blank"
                class="text-teal-600 hover:underline text-sm break-all">
                {{ url('/meeting/' . $getRecord()->id) }}
            </a>
        </div>

        <div class="flex gap-2 justify-center mt-4">
            <button type="button"
                onclick="navigator.clipboard.writeText('{{ url('/meeting/' . $getRecord()->id) }}').then(() => alert('Link disalin!'))"
                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                📋 Salin Link
            </button>
            <a href="{{ route('meeting.pdf', $getRecord()->id) }}" target="_blank"
                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                🖨️ Cetak PDF
            </a>
        </div>
    </div>
</div>