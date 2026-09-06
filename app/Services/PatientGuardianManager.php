<?php

namespace App\Services;

use App\Models\Guardian;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PatientGuardianManager
{
    public function link(
        Patient $patient,
        Guardian $guardian,
        ?string $relationship = null,
        bool $isPrimary = false,
    ): void {
        DB::transaction(function () use ($patient, $guardian, $relationship, $isPrimary): void {
            if ($isPrimary) {
                DB::table('guardian_patient')
                    ->where('patient_id', $patient->id)
                    ->update(['is_primary' => false, 'updated_at' => now()]);
            }

            $patient->guardians()->syncWithoutDetaching([
                $guardian->id => [
                    'relationship' => $relationship,
                    'is_primary' => $isPrimary,
                ],
            ]);
        });
    }

    public function setPrimary(Patient $patient, Guardian $guardian): void
    {
        $linked = DB::table('guardian_patient')
            ->where('patient_id', $patient->id)
            ->where('guardian_id', $guardian->id)
            ->exists();

        if (! $linked) {
            throw ValidationException::withMessages([
                'guardian' => 'El responsable debe estar vinculado al paciente antes de marcarse como principal.',
            ]);
        }

        DB::transaction(function () use ($patient, $guardian): void {
            DB::table('guardian_patient')
                ->where('patient_id', $patient->id)
                ->update(['is_primary' => false, 'updated_at' => now()]);

            DB::table('guardian_patient')
                ->where('patient_id', $patient->id)
                ->where('guardian_id', $guardian->id)
                ->update(['is_primary' => true, 'updated_at' => now()]);
        });
    }
}
