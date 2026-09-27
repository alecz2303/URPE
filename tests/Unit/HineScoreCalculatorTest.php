<?php

namespace Tests\Unit;

use App\Services\HineScoreCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HineScoreCalculatorTest extends TestCase
{
    public function test_it_preserves_half_points_and_calculates_global_score(): void
    {
        $result = (new HineScoreCalculator())->calculate([
            ['section_key' => 'cranial_nerves', 'score' => 2.5, 'asymmetry' => true],
            ['section_key' => 'posture', 'score' => 1.5, 'asymmetry' => false],
            ['section_key' => 'movements', 'score' => 3.0, 'asymmetry' => true],
            ['section_key' => 'tone', 'score' => 0.5, 'asymmetry' => false],
            ['section_key' => 'reflexes_reactions', 'score' => 2.0, 'asymmetry' => false],
        ]);

        $this->assertSame(2.5, $result['subtotals']['cranial_nerves']);
        $this->assertSame(1.5, $result['subtotals']['posture']);
        $this->assertSame(9.5, $result['global_score']);
        $this->assertSame(2, $result['asymmetry_count']);
    }

    #[DataProvider('allowedScores')]
    public function test_it_accepts_every_urpe_score(float $score): void
    {
        $result = (new HineScoreCalculator())->calculate([
            ['section_key' => 'cranial_nerves', 'score' => $score],
        ]);

        $this->assertSame($score, $result['global_score']);
    }

    public static function allowedScores(): array
    {
        return array_map(fn (float $score) => [$score], HineScoreCalculator::ALLOWED_SCORES);
    }

    public function test_it_rejects_scores_outside_half_point_scale(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new HineScoreCalculator())->calculate([
            ['section_key' => 'cranial_nerves', 'score' => 1.25],
        ]);
    }

    public function test_it_rejects_section_totals_above_hine_maximum(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new HineScoreCalculator())->calculate(array_fill(0, 6, [
            'section_key' => 'cranial_nerves',
            'score' => 3.0,
        ]));
    }

    public function test_maximum_global_score_is_78(): void
    {
        $responses = [];

        foreach (['cranial_nerves' => 5, 'posture' => 6, 'movements' => 2, 'tone' => 8, 'reflexes_reactions' => 5] as $section => $items) {
            for ($i = 0; $i < $items; $i++) {
                $responses[] = ['section_key' => $section, 'score' => 3.0];
            }
        }

        $result = (new HineScoreCalculator())->calculate($responses);

        $this->assertSame(78.0, $result['global_score']);
    }

    public function test_non_scored_sections_do_not_change_global_score(): void
    {
        $result = (new HineScoreCalculator())->calculate([
            ['section_key' => 'motor_milestones', 'score' => 3.0],
            ['section_key' => 'behavior', 'score' => 2.0],
        ]);

        $this->assertSame(0.0, $result['global_score']);
    }
}
