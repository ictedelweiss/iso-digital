<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div x-data="{
        signaturePad: null,
        state: $wire.entangle('{{ $getStatePath() }}'),
        
        init() {
            const canvas = this.$refs.canvas;
            this.resizeCanvas();
            
            // Re-bind resize on window resize
            window.addEventListener('resize', () => this.resizeCanvas());

            this.signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)', // Transparent
                penColor: 'rgb(0, 0, 0)'
            });

            // If existing signature (URL or Base64), we can't easily 'edit' it in canvas without complex logic.
            // But we can show it as an image below if state is a URL.
            // However, the ViewField default is passed as state.
            
            this.signaturePad.addEventListener('endStroke', () => {
                this.state = this.signaturePad.toDataURL();
            });
        },
        
        resizeCanvas() {
            const canvas = this.$refs.canvas;
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);
            
            // If we resize, we clear content, so usually we need to redraw.
            // For simple implementation, we might accept clearing on resize or try to save/restore.
            if (this.signaturePad) {
                this.signaturePad.clear(); 
            }
        },
        
        clear() {
            this.signaturePad.clear();
            this.state = null;
        }
    }" class="w-full">
        @if($getState())
            <div class="mb-4 p-4 border rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                <p class="text-sm text-gray-500 mb-2">Current Signature:</p>
                <!-- Check if state is URL (starts with http or /) or base64 -->
                <img src="{{ $getState() }}" alt="Current Signature" class="h-24 object-contain border bg-white rounded">
                <p class="text-xs text-gray-400 mt-2">Draw below to update.</p>
            </div>
        @endif

        <div
            class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 relative">
            <canvas x-ref="canvas" class="w-full h-48 cursor-crosshair touch-none"></canvas>
            <div class="absolute bottom-2 right-4 text-xs text-gray-400 pointer-events-none">
                Sign here
            </div>
        </div>

        <div class="mt-2 flex justify-end">
            <button type="button" x-on:click="clear()" class="text-sm text-red-500 hover:text-red-700 hover:underline">
                Clear & Reset
            </button>
        </div>
    </div>

</x-dynamic-component>