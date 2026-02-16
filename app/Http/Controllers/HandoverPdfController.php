<?php

namespace App\Http\Controllers;

use App\Models\HandoverForm;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class HandoverPdfController extends Controller
{
    public function download(HandoverForm $record)
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

        // ICT Signature (Issuer)
        $signatureICT = '';
        if ($record->ict_signature_path) {
            $path = public_path($record->ict_signature_path);
            if (!File::exists($path)) {
                $path = storage_path('app/public/' . $record->ict_signature_path);
            }
            $signatureICT = $encodeImage($path);
        }

        $ict = \App\Services\ConfigService::getICT();
        $hrd = \App\Services\ConfigService::getHRD();

        // ICT Signature (Issuer)
        $ictName = $record->creator->name ?? $ict['name'];

        // Pre-fill Coordinator from department
        $deptCoord = \App\Services\ConfigService::getCoordinator($record->recipient_department);

        $signatures = [
            'recipient' => ['src' => '', 'name' => $record->recipient_name],
            'coordinator' => ['src' => '', 'name' => $deptCoord['name']],
            'hrd' => ['src' => '', 'name' => $hrd['name']],
        ];

        foreach ($record->approvals as $approval) {
            if ($approval->approver_name) {
                $signatures[$approval->role]['name'] = $approval->approver_name;
            }
            if ($approval->signature_path) {
                $path = public_path($approval->signature_path);
                if (!File::exists($path)) {
                    $path = storage_path('app/public/' . $approval->signature_path);
                }
                $imgSrc = $encodeImage($path);

                // Map roles
                $roleKey = $approval->role;
                if (array_key_exists($roleKey, $signatures)) {
                    $signatures[$roleKey]['src'] = $imgSrc;
                    $signatures[$roleKey]['name'] = $approval->approver_name;
                }
            }
        }

        $data = [
            'form' => $record,
            'logoSrc' => $logoSrc,
            'signatureICT' => $signatureICT,
            'signatures' => $signatures,
        ];

        $pdf = Pdf::loadView('pdf.handover_form', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('HandoverForm-' . $record->id . '.pdf');
    }
}
