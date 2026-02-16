<?php

use App\Http\Controllers\Auth\MicrosoftController;
use App\Http\Controllers\HelpdeskController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MeetingPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->to('http://localhost/laravel-app/public/admin/login');
});

// Public Meeting Attendance Routes
Route::prefix('meeting')->name('meeting.')->group(function () {
    Route::get('/{meetingId}', [MeetingController::class , 'attend'])->name('attend');
    Route::post('/{meetingId}/submit', [MeetingController::class , 'submitAttendance'])->name('submit');
    Route::get('/{meetingId}/attendees', [MeetingController::class , 'getAttendees'])->name('attendees');
    Route::get('/{meetingId}/pdf', [MeetingPdfController::class , 'generate'])->name('pdf');
});

// Microsoft 365 OAuth Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/microsoft', [MicrosoftController::class , 'redirect'])->name('microsoft');
    Route::get('/microsoft/callback', [MicrosoftController::class , 'callback'])->name('microsoft.callback');
});

// ICT Helpdesk Public Routes
Route::prefix('helpdesk')->name('helpdesk.')->group(function () {
    Route::get('/', [HelpdeskController::class , 'create'])->name('create');
    Route::post('/', [HelpdeskController::class , 'store'])->name('store');
    Route::get('/success/{ticket}', [HelpdeskController::class , 'success'])->name('success');
});

// Dedicated Approval Routes
Route::get('/approval/{type}/{id}', [\App\Http\Controllers\ApprovalController::class , 'review'])->name('approval.review')->middleware('signed');
Route::post('/approval/{type}/{id}', [\App\Http\Controllers\ApprovalController::class , 'submit'])->name('approval.submit')->middleware('signed');
Route::get('/approval/done', [\App\Http\Controllers\ApprovalController::class , 'done'])->name('approval.done');

// Purchase Requisition PDF
Route::get('/admin/purchase-requisitions/{record}/pdf', [\App\Http\Controllers\PrPdfController::class , 'download'])->name('pr.pdf');
Route::get('/admin/leave-requests/{record}/pdf', [\App\Http\Controllers\LeavePdfController::class , 'download'])->name('leave.pdf');
Route::get('/admin/handover-forms/{record}/pdf', [\App\Http\Controllers\HandoverPdfController::class , 'download'])->name('handover.pdf');

// Asset Label Print Routes
Route::get('/admin/assets/print-labels', [\App\Http\Controllers\AssetLabelController::class , 'print'])->name('assets.print.labels');
Route::get('/admin/assets/{id}/print-label', [\App\Http\Controllers\AssetLabelController::class , 'printSingle'])->name('assets.print.single');

// Debug Email Route - REMOVE AFTER FIXING
Route::get('/debug-email', function () {
    try {
        // Clear Config Cache
        \Illuminate\Support\Facades\Artisan::call('config:clear');

        $transport = app('mailer')->getSymfonyTransport();
        $transportClass = get_class($transport);

        $config = config('services.microsoft_graph');
        $queueConnection = config('queue.default');

        // Attempt to send a raw email synchronously
        \Illuminate\Support\Facades\Mail::raw('Test Email from ISO Digital Debug Route', function ($message) use ($config) {
                    $message->to($config['from_address'])
                        ->subject('Debug Email Test - ' . now());
                }
                );

                return response()->json([
                'status' => 'success',
                'message' => 'Email sent successfully (synchronously)',
                'transport' => $transportClass,
                'queue_connection' => $queueConnection,
                'mail_config' => $config,
                ]);
            }
            catch (\Exception $e) {
                return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transport' => isset($transportClass) ? $transportClass : 'Unknown',
                'config_dump' => config('services.microsoft_graph')
                ], 500);
            }
        });