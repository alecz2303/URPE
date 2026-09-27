<?php

namespace App\Services;

use InvalidArgumentException;

class HineScoreCalculator
{
    public const SECTION_MAXIMA = [
        'cranial_nerves' => 15.0,
        'posture' => 18.0,
        'movements' => 6.0,
        'tone' => 24.0,
        'reflexes_reactions' => 15.0,
    ];

    public const ALLOWED_SCORES = [0.0, 0.5, 1.0, 1.5, 2.0, 2.5, 3.0];

    public function calculate(array $responses): array
    {
        $subtotals = array_fill_keys(array_keys(self::SECTION_MAXIMA), 0.0);
        $asymmetryCount = 0;

        foreach ($responses as $response) {
            $section = $response['section_key'] ?? null;
            $score = $response['score'] ?? null;

            if ($score !== null) {
                $score = (float) $score;

                if (! in_array($score, self::ALLOWED_SCORES, true)) {
                    throw new InvalidArgumentException('La puntuación HINE debe estar entre 0 y 3 en incrementos de 0.5.');
                }

                if (array_key_exists($section, $subtotals)) {
                    $subtotals[$section] += $score;
                }
            }

            if (($response['asymmetry'] ?? false) === true) {
                $asymmetryCount++;
            }
        }

        foreach (self::SECTION_MAXIMA as $section => $maximum) {
            if ($subtotals[$section] > $maximum) {
                throw new InvalidArgumentException("El subtotal HINE de {$section} excede su máximo permitido.");
            }
        }

        return [
            'subtotals' => $subtotals,
            'global_score' => array_sum($subtotals),
            'asymmetry_count' => $asymmetryCount,
        ];
    }
}
