<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PatientFolioGenerator
{
    public function next(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');

        $number = DB::transaction(function () use ($year): int {
            DB::table('patient_folio_sequences')->insertOrIgnore([
                'year' => $year,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sequence = DB::table('patient_folio_sequences')
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            $next = ((int) $sequence->last_number) + 1;

            DB::table('patient_folio_sequences')
                ->where('year', $year)
                ->update([
                    'last_number' => $next,
                    'updated_at' => now(),
                ]);

            return $next;
        });

        return sprintf('URPE-%d-%06d', $year, $number);
    }
}
