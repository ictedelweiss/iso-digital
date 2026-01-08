<x-filament-widgets::widget>
    @php
        $data = $this->getApprovalStatusData();
        $colorMap = [
            'success' => 'bg-green-50 border-green-200 text-green-800',
            'danger' => 'bg-red-50 border-red-200 text-red-800',
            'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
            'info' => 'bg-blue-50 border-blue-200 text-blue-800',
            'gray' => 'bg-gray-50 border-gray-200 text-gray-800',
        ];
        $iconMap = [
            'success' => 'heroicon-o-check-circle',
            'danger' => 'heroicon-o-x-circle',
            'warning' => 'heroicon-o-clock',
            'info' => 'heroicon-o-information-circle',
            'gray' => 'heroicon-o-question-mark-circle',
        ];
        $colorClass = $colorMap[$data['color']] ?? $colorMap['gray'];
        $icon = $iconMap[$data['color']] ?? $iconMap['gray'];
    @endphp

    <div class="p-6 rounded-xl border-2 {{ $colorClass }}">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <x-filament::icon :icon="$icon" class="w-8 h-8" />
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold mb-1">
                    {{ $data['status'] }}
                    @if(isset($data['step']))
                        <span class="text-sm font-normal opacity-75">({{ $data['step'] }})</span>
                    @endif
                </h3>
                <p class="text-sm leading-relaxed">
                    {!! $data['message'] !!}
                </p>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>