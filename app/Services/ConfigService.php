<?php

namespace App\Services;

class ConfigService
{
    // Test Mode Configuration
    const TEST_MODE = true;
    const TEST_EMAIL = 'aris.setyawan@edelweiss.sch.id';

    public static function getCoordinators(): array
    {
        return [
            'KB/TK' => [
                'name' => 'Armitridesi Shinta Marito',
                'email' => 'armitridesi.marito@edelweiss.sch.id',
            ],
            'SD' => [
                'name' => 'Miske Ferlani',
                'email' => 'miske.ferlani@edelweiss.sch.id',
            ],
            'SMP' => [
                'name' => 'Yudha Hadi Purnama',
                'email' => 'yudha.purnama@edelweiss.sch.id',
            ],
            'PKBM' => [
                'name' => 'Mia Roosmalisa',
                'email' => 'mia.roosmalisa@edelweiss.sch.id',
            ],
            'Customer Service Officer' => [
                'name' => 'Juarsa Oemardikarta',
                'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
            ],
            'Finance & Accounting' => [
                'name' => 'Juarsa Oemardikarta',
                'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
            ],
            'HRD' => [
                'name' => 'Juarsa Oemardikarta',
                'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
            ],
            'ICT' => [
                'name' => 'Juarsa Oemardikarta',
                'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
            ],
            'Management' => [
                'name' => 'Ayu Puspitawati',
                'email' => 'ayu.puspitawati@edelweiss.sch.id',
            ],
            'Marketing' => [
                'name' => 'Juarsa Oemardikarta',
                'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
            ],
            'Operator' => [
                'name' => 'Juarsa Oemardikarta',
                'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
            ],
        ];
    }

    public static function getCoordinator(string $department): array
    {
        $coordinators = self::getCoordinators();
        return $coordinators[$department] ?? [
            'name' => 'Koordinator',
            'email' => 'iso.digital@edelweiss.sch.id', // Fallback
        ];
    }

    public static function getAccounting(): array
    {
        return [
            'name' => 'Titis Rahmawati Wijiastuti',
            'email' => 'titis.wijiastuti@edelweiss.sch.id',
        ];
    }

    public static function getHRD(): array
    {
        return [
            'name' => 'Medina Marpaung',
            'email' => 'medina.marpaung@edelweiss.sch.id',
        ];
    }

    public static function getChairman(): array
    {
        return [
            'name' => 'Juarsa Oemardikarta',
            'email' => 'juarsa.oemardikarta@edelweiss.sch.id',
        ];
    }

    public static function getApproverForStep(string $type, int $step, ?string $department = null): ?array
    {
        $approver = null;
        $roleDisplay = null;

        if ($type === 'purchase_requisition') {
            $approver = match ($step) {
                1 => self::getCoordinator($department),
                2 => self::getAccounting(),
                3 => self::getChairman(),
                default => null,
            };
            $roleDisplay = match ($step) {
                1 => 'Koordinator ' . ($department ?? ''),
                2 => 'Accounting',
                3 => 'Ketua Yayasan',
                default => null,
            };
        } elseif ($type === 'leave_request') {
            $approver = match ($step) {
                1 => self::getCoordinator($department),
                2 => self::getHRD(),
                // Leave request in legacy only has 2 steps: Koordinator -> HRD. 
                // However, check if step 3 was ever intended. 
                // Legacy config said 1=>Koordinator, 2=>HRD.
                default => null,
            };
            $roleDisplay = match ($step) {
                1 => 'Koordinator ' . ($department ?? ''),
                2 => 'HRD',
                default => null,
            };
        } elseif ($type === 'handover_form') {
            $approver = match ($step) {
                // Step 1 is Recipient, handled dynamically outside
                1 => null,
                2 => self::getCoordinator($department),
                3 => self::getHRD(),
                default => null,
            };
            $roleDisplay = match ($step) {
                1 => null,
                2 => 'Koordinator ' . ($department ?? ''),
                3 => 'HRD',
                default => null,
            };
        }

        // Apply Test Mode Override
        if ($approver && self::TEST_MODE) {
            $approver['email'] = self::TEST_EMAIL;
        }

        // Add role_display to approver array
        if ($approver && $roleDisplay) {
            $approver['role_display'] = $roleDisplay;
        }

        return $approver;
    }
}
