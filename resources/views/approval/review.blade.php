<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen font-sans text-slate-900">

    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">

        <!-- Header / Brand -->
        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-blue-900 tracking-tight">Approval Request</h2>
            <p class="mt-2 text-lg text-slate-700">Please review the document details below before signing.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-blue-100">

            <!-- Document Header -->
            <div class="bg-gradient-to-r from-blue-900 to-blue-800 px-8 py-6 flex justify-between items-center">
                <div>
                    <p class="text-blue-200 text-xs uppercase font-bold tracking-wider">Document Number</p>
                    <h1 class="text-2xl font-bold text-white mt-1">
                        @if($type === 'purchase_requisition')
                            {{ $record->pr_number }}
                        @else
                            #{{ $record->id }}
                        @endif
                    </h1>
                </div>
                <div class="text-right">
                    <p class="text-blue-200 text-xs uppercase font-bold tracking-wider">Requester</p>
                    <p class="text-white font-medium mt-1">
                        {{ $record->requester ?? $record->name ?? $record->creator->name ?? 'System' }}</p>
                </div>
            </div>

            <div class="p-8 space-y-8">

                <!-- Main Content details -->
                <div>
                    @if($type === 'purchase_requisition')
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-slate-800 mb-2">{{ $record->title }}</h3>
                            <p class="text-slate-600 leading-relaxed">
                                {{ $record->notes ?? 'No additional notes provided.' }}</p>

                            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                                <div class="bg-slate-50 px-3 py-1.5 rounded border border-slate-200 text-slate-600">
                                    <span class="font-bold text-slate-800">Department:</span> {{ $record->department }}
                                </div>
                                <div class="bg-slate-50 px-3 py-1.5 rounded border border-slate-200 text-slate-600">
                                    <span class="font-bold text-slate-800">Date Needed:</span>
                                    {{ \Carbon\Carbon::parse($record->needed_date)->format('d M Y') }}
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="overflow-hidden rounded-lg border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            Item Name</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            Qty</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            Price / Unit</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    @foreach($record->items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                                                {{ $item->item_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-600">
                                                {{ $item->qty }} {{ $item->unit }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-slate-600">Rp
                                                {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-slate-800">
                                                Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Attachments -->
                        @if($record->documents && $record->documents->count() > 0)
                            <div class="mt-8">
                                <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Supporting Documents
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($record->documents as $doc)
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                            class="flex items-center p-4 bg-slate-50 border border-slate-200 rounded-lg hover:bg-sky-50 hover:border-sky-200 hover:shadow-md transition-all group">
                                            <div
                                                class="h-10 w-10 flex-shrink-0 bg-white rounded-lg border border-slate-200 flex items-center justify-center text-red-500 group-hover:text-red-600 group-hover:border-red-100">
                                                <!-- PDF Icon -->
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div class="ml-4 truncate">
                                                <p class="text-sm font-medium text-slate-900 truncate group-hover:text-sky-700">
                                                    {{ $doc->file_name ?? basename($doc->file_path) }}</p>
                                                <p class="text-xs text-slate-500">Click to view/download</p>
                                            </div>
                                            <div class="ml-auto text-slate-400 group-hover:text-sky-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- No Documents Placeholder (Optional) -->
                            <div
                                class="mt-8 p-4 bg-slate-50 rounded-lg border border-slate-100 text-center text-slate-400 text-sm">
                                No Item supporting documents attached.
                            </div>
                        @endif

                    @elseif($type === 'leave_request')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase">Leave Type</p>
                                <p class="text-lg font-semibold text-slate-900 mt-1">{{ $record->leave_type }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-lg border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase">Duration</p>
                                <p class="text-lg font-semibold text-slate-900 mt-1">
                                    {{ \Carbon\Carbon::parse($record->start_date)->format('d M Y') }} -
                                    {{ \Carbon\Carbon::parse($record->end_date)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="col-span-full p-4 bg-slate-50 rounded-lg border border-slate-100">
                                <p class="text-xs font-bold text-slate-500 uppercase">Reason</p>
                                <p class="text-slate-800 mt-1">{{ $record->reason }}</p>
                            </div>
                        </div>

                    @elseif($type === 'handover_form')
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-100 mb-6">
                            <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $record->task_name }}</h3>
                            <p class="text-slate-600">{{ $record->description }}</p>
                        </div>
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg text-sm font-semibold">
                                To: {{ $record->recipient_name }}
                            </div>
                        </div>
                    @endif
                </div>

                <hr class="border-slate-100">

                <!-- PR Number Edit (Accounting Only) -->
                @if($type === 'purchase_requisition' && $record->current_approval_step === 2)
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-6 rounded-lg mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-bold text-amber-800 mb-2">
                                    Edit PR Number (Accounting)
                                </h3>
                                <p class="text-sm text-amber-700 mb-4">
                                    As accounting approver, you can modify the PR number to align with your internal
                                    numbering system.
                                </p>
                                <label class="block text-sm font-bold text-slate-700 mb-2">New PR Number (Optional)</label>
                                <input type="text" name="new_pr_number" id="newPrNumberInput"
                                    value="{{ $record->pr_number }}"
                                    class="w-full max-w-md px-4 py-3 border-2 border-slate-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500 font-mono text-base"
                                    placeholder="e.g., PR-ACC-2026-001">
                                <p class="mt-2 text-xs text-slate-500">
                                    Leave unchanged if you don't need to modify the PR number.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <hr class="border-slate-100">

                <!-- Action Form -->
                <form
                    action="{{ URL::temporarySignedRoute('approval.submit', now()->addDays(7), ['type' => $type, 'id' => $id]) }}"
                    method="POST" id="approvalForm" class="space-y-8">
                    @csrf

                    <!-- Tabs -->
                    <div class="flex p-1 space-x-1 bg-slate-50 rounded-xl border border-slate-200">
                        <button type="button" onclick="setAction('approve')" id="btn-approve"
                            class="w-full py-3 text-sm font-bold leading-5 rounded-lg transition-all text-white bg-gradient-to-r from-emerald-500 to-emerald-600 shadow-md">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Approve</span>
                            </div>
                        </button>
                        <button type="button" onclick="setAction('reject')" id="btn-reject"
                            class="w-full py-3 text-sm font-medium leading-5 text-slate-600 rounded-lg transition-all hover:bg-slate-100 hover:text-slate-800">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <span>Reject</span>
                            </div>
                        </button>
                    </div>

                    <input type="hidden" name="action" id="actionInput" value="approve">

                    <!-- Approve Section -->
                    <div id="approveSection" class="transition-all duration-300">
                        <div
                            class="border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 relative hover:border-sky-400 transition-colors">
                            <canvas id="signature-pad"
                                class="w-full h-48 rounded-2xl cursor-crosshair touch-none"></canvas>
                            <div
                                class="absolute bottom-3 right-4 text-xs font-bold text-slate-400 pointer-events-none uppercase tracking-wide">
                                Draw Signature Above</div>
                        </div>
                        <div class="mt-2 text-right">
                            <button type="button" onclick="clearSignature()"
                                class="text-xs font-medium text-red-500 hover:text-red-700 hover:underline">Clear
                                Signature</button>
                        </div>
                        <input type="hidden" name="signature" id="signatureInput">

                        <div class="mt-6">
                            <button type="submit"
                                class="w-full flex items-center justify-center py-4 px-6 border border-transparent text-base font-bold rounded-xl text-white bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-lg shadow-emerald-500/30 transition-all transform active:scale-95"
                                onclick="return prepareSubmit()">
                                Sign & Approve Document
                            </button>
                        </div>
                    </div>

                    <!-- Reject Section -->
                    <div id="rejectSection" class="hidden transition-all duration-300">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Reason for Rejection</label>
                        <textarea name="reason" rows="4"
                            class="w-full p-4 border border-slate-300 rounded-xl shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Please provide a reason for rejecting this request..."></textarea>

                        <div class="mt-6">
                            <button type="submit"
                                class="w-full flex items-center justify-center py-4 px-6 border border-transparent text-base font-bold rounded-xl text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-lg shadow-red-500/30 transition-all transform active:scale-95">
                                Reject Document
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-slate-50 px-8 py-4 border-t border-blue-100 flex justify-center">
                <p class="text-xs text-slate-500">Secured ISO Digital Approval System &copy; {{ date('Y') }}</p>
            </div>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('signature-pad');

        // Resize canvas for high DPI
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        window.onresize = resizeCanvas;
        resizeCanvas();

        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)' // transparent
        });

        function clearSignature() {
            signaturePad.clear();
        }

        function setAction(action) {
            document.getElementById('actionInput').value = action;

            if (action === 'approve') {
                document.getElementById('approveSection').classList.remove('hidden');
                document.getElementById('rejectSection').classList.add('hidden');
                document.getElementById('btn-approve').className = 'w-full py-3 text-sm font-bold leading-5 rounded-lg transition-all text-white bg-gradient-to-r from-emerald-500 to-emerald-600 shadow-md';
                document.getElementById('btn-reject').className = 'w-full py-3 text-sm font-medium leading-5 text-slate-600 rounded-lg transition-all hover:bg-slate-100 hover:text-slate-800';
            } else {
                document.getElementById('approveSection').classList.add('hidden');
                document.getElementById('rejectSection').classList.remove('hidden');
                document.getElementById('btn-approve').className = 'w-full py-3 text-sm font-medium leading-5 text-slate-600 rounded-lg transition-all hover:bg-slate-100 hover:text-slate-800';
                document.getElementById('btn-reject').className = 'w-full py-3 text-sm font-bold leading-5 rounded-lg transition-all text-white bg-gradient-to-r from-red-500 to-red-600 shadow-md';
            }
        }

        function prepareSubmit() {
            const action = document.getElementById('actionInput').value;
            if (action === 'approve') {
                if (signaturePad.isEmpty()) {
                    alert("Please provide a signature.");
                    return false;
                }
                document.getElementById('signatureInput').value = signaturePad.toDataURL(); // Save as Base64
            }
            return true;
        }
    </script>

</body>

</html>