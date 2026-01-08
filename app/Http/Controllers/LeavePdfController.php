<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class LeavePdfController extends Controller
{
    public function download(LeaveRequest $record)
    {
        // Helper to encode image to base64
        $encodeImage = function ($path) {
            if (File::exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = File::get($path);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            return '';
        };

        // Logo
        $logoPath = public_path('logo.png');
        $logoSrc = $encodeImage($logoPath);

        // Signatures
        $signaturePemohon = '';
        if ($record->signature_pemohon) {
            $path = public_path($record->signature_pemohon);
            if (!File::exists($path)) {
                $path = storage_path('app/public/' . $record->signature_pemohon);
            }
            $signaturePemohon = $encodeImage($path);
        }

        $hrd = \App\Services\ConfigService::getHRD();
        $chairman = \App\Services\ConfigService::getChairman();

        // Pre-fill names from ConfigService
        $deptCoord = \App\Services\ConfigService::getCoordinator($record->department);

        $signatures = [
            'koordinator' => ['src' => '', 'name' => $deptCoord['name'], 'date' => null],
            'hrd' => ['src' => '', 'name' => $hrd['name'], 'date' => null],
            'ketua_yayasan' => ['src' => '', 'name' => $chairman['name'], 'date' => null],
        ];

        foreach ($record->approvals as $approval) {
            // Override with actual approver name if signed
            if ($approval->approver_name) {
                $signatures[$approval->role]['name'] = $approval->approver_name;
            }
            // ... rest of loop
            if ($approval->signature_path) {
                $path = public_path($approval->signature_path);
                if (!File::exists($path)) {
                    $path = storage_path('app/public/' . $approval->signature_path);
                }
                $imgSrc = $encodeImage($path);

                // Map roles to signature slots
                $roleKey = $approval->role;
                if (array_key_exists($roleKey, $signatures)) {
                    $signatures[$roleKey]['src'] = $imgSrc;
                    $signatures[$roleKey]['name'] = $approval->approver_name;
                    $signatures[$roleKey]['date'] = $approval->approved_at;
                }
            }
        }

        $data = [
            'leave' => $record,
            'logoSrc' => $logoSrc,
            'signaturePemohon' => $signaturePemohon,
            'signatures' => $signatures,
        ];

        $pdf = Pdf::loadView('pdf.leave_request', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('LeaveRequest-' . $record->id . '.pdf');
    }
}
