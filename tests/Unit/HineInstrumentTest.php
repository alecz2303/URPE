<?php

namespace Tests\Unit;

use App\Support\HineInstrument;
use PHPUnit\Framework\TestCase;

class HineInstrumentTest extends TestCase
{
    public function test_neurological_catalog_preserves_source_structure_and_maxima(): void
    {
        $sections = HineInstrument::neurologicalSections();

        $this->assertSame(['cranial_nerves','posture','movements','tone','reflexes_reactions'], array_keys($sections));
        $this->assertSame([5,6,2,8,5], array_values(array_map(fn ($s) => count($s['items']), $sections)));
        $this->assertSame(78, array_sum(array_column($sections, 'maximum')));
    }

    public function test_urpe_extended_scale_is_explicit(): void
    {
        $this->assertSame([0,0.5,1,1.5,2,2.5,3], HineInstrument::URPE_SCORES);
    }

    public function test_non_scored_sections_preserve_source_item_counts(): void
    {
        $this->assertCount(8, HineInstrument::motorMilestones());
        $this->assertSame([6,5,4], array_column(HineInstrument::behaviorItems(), 'option_count'));
    }

    public function test_catalog_marks_items_that_require_original_visual_reference(): void
    {
        $sections = HineInstrument::neurologicalSections();

        $this->assertTrue($sections['posture']['items'][0]['visual']);
        $this->assertTrue($sections['tone']['items'][0]['visual']);
        $this->assertTrue($sections['reflexes_reactions']['items'][0]['visual']);
        $this->assertTrue(HineInstrument::motorMilestones()[1]['visual']);
    }
    public function test_source_anchors_do_not_invent_half_point_descriptions(): void
    {
        $anchors = HineInstrument::clinicalAnchors();

        $this->assertSame('Sonríe o reacciona a los estímulos cerrando los ojos y haciendo muecas', $anchors['facial_appearance'][3]);
        $this->assertArrayNotHasKey('0.5', $anchors['facial_appearance']);
        $this->assertArrayNotHasKey('1.5', $anchors['facial_appearance']);
        $this->assertArrayNotHasKey('2.5', $anchors['facial_appearance']);
        $this->assertArrayNotHasKey(2, $anchors['facial_appearance']);
    }

    public function test_motor_milestones_preserve_source_observation_criteria(): void
    {
        $milestones = collect(HineInstrument::motorMilestones())->keyBy('key');

        $this->assertSame(
            ['No puede mantenerse sentado', 'Con soporte en caderas', 'Se apoya', 'Sedestación estable', 'Pivota (rota)'],
            $milestones['sitting']['options']
        );
        $this->assertSame('Normal a los 10m', $milestones['crawling']['normal_ages'][4]);
        $this->assertSame(
            ['Rebota (intenta botar)', 'Camina con apoyo', 'Camina independiente'],
            $milestones['walking']['options']
        );
        $this->assertSame(
            'Por favor, anote la edad a la cual se consigue la máxima habilidad.',
            $milestones['head_control']['age_note']
        );
    }

    public function test_behavior_options_are_preserved_without_converting_them_to_neurological_scores(): void
    {
        $items = HineInstrument::behaviorItems();

        $this->assertSame('Mantiene el interés', $items[0]['options'][5]);
        $this->assertSame('Contento y sonriente', $items[1]['options'][4]);
        $this->assertSame('Amistoso', $items[2]['options'][3]);
    }
    public function test_visual_reference_map_covers_every_catalog_item_marked_as_visual(): void
    {
        $map = HineInstrument::visualReferenceMap();
        $required = [];

        foreach (HineInstrument::neurologicalSections() as $section) {
            foreach ($section['items'] as $item) {
                if ($item['visual'] ?? false) {
                    $required[] = $item['key'];
                }
            }
        }

        foreach (HineInstrument::motorMilestones() as $item) {
            if ($item['visual'] ?? false) {
                $required[] = $item['key'];
            }
        }

        sort($required);
        $mapped = array_keys($map);
        sort($mapped);

        $this->assertSame($required, $mapped);
        $this->assertSame('scarf_sign.png', $map['scarf_sign']);
        $this->assertSame('parachute.png', $map['parachute']);
        $this->assertSame('crawling.png', $map['crawling']);

        foreach ($map as $image) {
            $this->assertMatchesRegularExpression('/^[a-z0-9_]+\\.png$/', $image);
        }
    }
    public function test_motor_milestone_normal_ages_keep_their_source_columns(): void
    {
        $milestones = collect(HineInstrument::motorMilestones())->keyBy('key');

        $this->assertSame([1 => 'Normal a los 4m', 2 => 'Normal a los 6m', 3 => 'Normal a los 7-8m', 4 => 'Normal a los 9m'], $milestones['sitting']['normal_ages']);
        $this->assertSame([2 => 'Normal a los 3m', 3 => 'Normal a los 4-5m', 4 => 'Normal a los 5-6m'], $milestones['supine_kicking']['normal_ages']);
        $this->assertSame([1 => 'Normal a los 4m', 2 => 'Normal a los 6m', 3 => 'Normal a los 6m'], $milestones['rolling']['normal_ages']);
        $this->assertSame([1 => 'Normal a los 3m', 2 => 'Normal a los 4m', 3 => 'Normal a los 8m', 4 => 'Normal a los 10m'], $milestones['crawling']['normal_ages']);
        $this->assertSame([1 => 'Normal a los 4m', 2 => 'Normal a los 7m', 3 => 'Normal a los 12m'], $milestones['standing']['normal_ages']);
    }

    public function test_source_examination_instructions_are_preserved_for_tone_and_reactions(): void
    {
        $sections = HineInstrument::neurologicalSections();
        $tone = collect($sections['tone']['items'])->keyBy('key');
        $reactions = collect($sections['reflexes_reactions']['items'])->keyBy('key');

        $this->assertStringContainsString('posición del codo con relación a la línea media', $tone['scarf_sign']['instruction']);
        $this->assertStringContainsString('pronación y supinación de antebrazo', $tone['pronation_supination']['instruction']);
        $this->assertStringContainsString('ángulo entre el pie y la pierna', $tone['ankle_dorsiflexion']['instruction']);
        $this->assertStringContainsString('soportar la cabeza si es necesario', $tone['pull_to_sit']['instruction']);
        $this->assertStringContainsString('reacción del brazo libre', $reactions['arm_protection']['instruction']);
        $this->assertStringContainsString('cosquillas en los pies', $reactions['vertical_suspension']['instruction']);
        $this->assertStringContainsString('después de los 6 meses', $reactions['parachute']['instruction']);
        $this->assertStringContainsString('Bíceps, rodilla, tobillo', $reactions['tendon_reflexes']['instruction']);
    }

    public function test_reaction_source_notes_remain_separate_from_examination_instruction(): void
    {
        $sections = HineInstrument::neurologicalSections();
        $items = collect($sections['reflexes_reactions']['items'])->keyBy('key');

        $this->assertSame('Después de los 6 meses.', $items['parachute']['age_note']);
        $this->assertStringNotContainsString('después de los 6 meses', strtolower($items['parachute']['instruction']));
        $this->assertSame(['bíceps', 'rodilla', 'tobillo'], $items['tendon_reflexes']['sites']);
        $this->assertStringContainsString('-puede "hacer cosquillas"', $items['vertical_suspension']['instruction']);
    }

    public function test_source_anchor_columns_preserve_blank_cells_and_verified_ranges(): void
    {
        $anchors = HineInstrument::clinicalAnchors();

        $this->assertSame('En supino y bipedestación: ligera rotación interna o rotación externa.', $anchors['legs'][2]);
        $this->assertArrayNotHasKey(2, $anchors['feet']);
        $this->assertSame('150-160°', $anchors['hip_adductors'][2]);
        $this->assertSame('~90° o >170°', $anchors['popliteal_angle'][1]);
        $this->assertSame('<20° o 90°', $anchors['ankle_dorsiflexion'][1]);

        $this->assertArrayNotHasKey(2, $anchors['pronation_supination']);
        $this->assertArrayNotHasKey(2, $anchors['arm_protection']);
        $this->assertSame('Brazo semiflexionado', $anchors['arm_protection'][1]);
        $this->assertSame('Brazo completamente flexionado', $anchors['arm_protection'][0]);
    }

    public function test_every_visual_reference_has_a_real_public_asset(): void
    {
        foreach (HineInstrument::visualReferenceMap() as $image) {
            $this->assertFileExists(public_path('images/hine/'.$image));
        }
    }

    public function test_interpretation_aid_preserves_only_explicit_source_cutoffs(): void
    {
        $aid = HineInstrument::interpretationAid();

        $this->assertSame(['<40', '40–60', '>60'], array_column($aid['global_score_ranges'], 'label'));
        $this->assertSame(4, $aid['asymmetry_attention_threshold']);
        $this->assertSame([3 => 56, 6 => 59, 9 => 62, 12 => 65], $aid['high_risk_cutoffs_by_age_months']);
        $this->assertSame('Puntuación global', $aid['global_score_heading']);
        $this->assertSame('Número de asimetrías', $aid['asymmetry_heading']);
        $this->assertSame('Puntuaciones de Corte para el Alto Riesgo en PC', $aid['high_risk_heading']);
        $this->assertArrayNotHasKey(7, $aid['high_risk_cutoffs_by_age_months']);
        $this->assertStringContainsString('no se interpolan', $aid['age_rule']);
    }

}
