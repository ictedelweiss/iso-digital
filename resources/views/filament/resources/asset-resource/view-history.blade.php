<div class="space-y-4">
    @if($asset->histories->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs uppercase bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-4 py-3">Tanggal & Waktu</th>
                        <th scope="col" class="px-4 py-3">Aksi</th>
                        <th scope="col" class="px-4 py-3">Field</th>
                        <th scope="col" class="px-4 py-3">Nilai Lama</th>
                        <th scope="col" class="px-4 py-3">Nilai Baru</th>
                        <th scope="col" class="px-4 py-3">Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($asset->histories()->orderBy('created_at', 'desc')->get() as $history)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-3 font-medium whitespace-nowrap">
                                {{ $history->created_at->format('d M Y, H:i')}}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs font-semibold rounded
                                            @if($history->action === 'CREATE') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                            @elseif($history->action === 'UPDATE') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                            @elseif($history->action === 'DELETE') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                            @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                            @endif">
                                    {{ $history->action }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                {{ $history->field_name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $history->old_value ?? '-' }}
                            </td>
                            <td class="px-4 py-3 font-medium">
                                {{ $history->new_value ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $history->changedBy->name ?? 'System' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            <p>Belum ada history perubahan</p>
        </div>
    @endif
</div>