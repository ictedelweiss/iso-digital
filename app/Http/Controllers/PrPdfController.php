<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequisition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PrPdfController extends Controller
{
    public function download(PurchaseRequisition $record)
    {
        // Eager load relationships
        $record->load(['items', 'approvals']);

        // Prepare data for the 5-row loop
        $items = $record->items->toArray();
        $paddedItems = [];
        for ($i = 0; $i < 5; $i++) {
            if (isset($items[$i])) {
                $paddedItems[] = $items[$i];
            } else {
                $paddedItems[] = null; // Empty row
            }
        }

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

        // Requester Signature
        $signaturePemohon = '';
        if ($record->requester_signature_path) {
            // Check public path first, then storage path
            $path = public_path($record->requester_signature_path);
            if (!File::exists($path)) {
                $path = storage_path('app/public/' . $record->requester_signature_path);
            }
            $signaturePemohon = $encodeImage($path);
        }

        // Approval Signatures
        $accounting = \App\Services\ConfigService::getAccounting();
        $chairman = \App\Services\ConfigService::getChairman();

        $signatures = [
            'koordinator' => ['src' => '', 'name' => 'Koordinator'],
            'accounting' => ['src' => '', 'name' => $accounting['name']],
            'ketua_yayasan' => ['src' => '', 'name' => $chairman['name']],
        ];

        foreach ($record->approvals as $approval) {
            if ($approval->role === 'koordinator' && !$approval->approver_name) {
                // Try to fallback to department coordinator name if not stored
                $coord = \App\Services\ConfigService::getCoordinator($record->department);
                $approval->approver_name = $coord['name'];
            }

            if ($approval->signature_path) {
                $path = public_path($approval->signature_path);
                if (!File::exists($path)) {
                    $path = storage_path('app/public/' . $approval->signature_path);
                }

                $imgSrc = $encodeImage($path);

                if ($approval->role === 'koordinator') {
                    $signatures['koordinator']['src'] = $imgSrc;
                    if ($approval->approver_name) {
                        $signatures['koordinator']['name'] = $approval->approver_name;
                    }
                } elseif ($approval->role === 'accounting') {
                    $signatures['accounting']['src'] = $imgSrc;
                    if ($approval->approver_name) {
                        $signatures['accounting']['name'] = $approval->approver_name;
                    }
                } elseif ($approval->role === 'ketua_yayasan') {
                    $signatures['ketua_yayasan']['src'] = $imgSrc;
                    if ($approval->approver_name) {
                        $signatures['ketua_yayasan']['name'] = $approval->approver_name;
                    }
                }
            }
        }

        $data = [
            'pr' => $record,
            'items' => $paddedItems, // Exactly 5 items
            'grandTotal' => $record->items->sum(fn($i) => $i->qty * $i->price),
            'logoSrc' => $logoSrc,
            'signaturePemohon' => $signaturePemohon,
            'signatures' => $signatures,
        ];

        // Using setPaper('a4', 'portrait') - Legacy was A4
        $pdf = Pdf::loadView('pdf.pr_legacy', $data)->setPaper('a4', 'portrait');

        return $pdf->stream('PR-' . $record->pr_number . '.pdf');
    }
}
