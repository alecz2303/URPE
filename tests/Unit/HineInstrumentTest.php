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
        $this->assertStringContainsString('"hacer cosquillas" en los pies', $reactions['vertical_suspension']['instruction']);
        $this->assertStringNotContainsString('después de los 6 meses', strtolower($reactions['parachute']['instruction']));
        $this->assertSame('Después de los 6 meses.', $reactions['parachute']['age_note']);
        $this->assertSame(['bíceps', 'rodilla', 'tobillo'], $reactions['tendon_reflexes']['sites']);
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

    public function test_posture_anchor_columns_preserve_source_blanks(): void
    {
        $anchors = HineInstrument::clinicalAnchors();

        $this->assertSame('En posición neutra, centrados o ligeramente flexionados', $anchors['arms_rest'][3]);
        $this->assertSame('Ligera rotación interna o rotación externa; intermitente postura distónica', $anchors['arms_rest'][1]);
        $this->assertSame('Marcada rotación interna o rotación externa o postura distónica; postura hemiparética', $anchors['arms_rest'][0]);
        $this->assertArrayNotHasKey(2, $anchors['arms_rest']);
    }

    public function test_cranial_nerve_anchor_columns_preserve_source_blanks(): void
    {
        $anchors = HineInstrument::clinicalAnchors();

        $this->assertSame('Responde al estímulo desde ambos lados', $anchors['auditory_response'][3]);
        $this->assertSame('No responde al estímulo o responde asimétricamente', $anchors['auditory_response'][1]);
        $this->assertArrayNotHasKey(2, $anchors['auditory_response']);
        $this->assertArrayNotHasKey(0, $anchors['auditory_response']);
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

    public function test_movement_quality_preserves_blank_score_two_source_column(): void
    {
        $anchors = HineInstrument::clinicalAnchors();

        $this->assertSame('Libres, alternantes, y suaves', $anchors['quality'][3]);
        $this->assertArrayNotHasKey(2, $anchors['quality']);
        $this->assertSame('Bruscos, entrecortados; Ligero temblor', $anchors['quality'][1]);
    }

    public function test_passive_shoulder_elevation_preserves_blank_score_two_source_column(): void
    {
        $anchors = HineInstrument::clinicalAnchors();

        $this->assertSame('Resistencia superable', $anchors['passive_shoulder_elevation'][3]);
        $this->assertArrayNotHasKey(2, $anchors['passive_shoulder_elevation']);
        $this->assertSame('No existe resistencia', $anchors['passive_shoulder_elevation'][1]);
        $this->assertSame('Resistencia no superable', $anchors['passive_shoulder_elevation'][0]);
    }

    public function test_tone_numeric_anchor_ranges_match_the_source(): void
    {
        $anchors = HineInstrument::clinicalAnchors();

        $this->assertSame([3 => 'Rango: 150°-80°', 2 => '150-160°', 1 => '>170°', 0 => '<80°'], $anchors['hip_adductors']);
        $this->assertSame([3 => 'Rango: 150°-100°', 2 => '150-160°', 1 => '~90° o >170°', 0 => '<80°'], $anchors['popliteal_angle']);
        $this->assertSame([3 => 'Rango: 30°-85°', 2 => '20-30°', 1 => '<20° o 90°', 0 => '>90°'], $anchors['ankle_dorsiflexion']);
    }

    public function test_parachute_source_columns_are_preserved_without_invented_criteria(): void
    {
        $anchors = HineInstrument::clinicalAnchors();

        $this->assertSame([
            3 => '(después de los 6 meses)',
            0 => '(después de los 6 meses)',
        ], $anchors['parachute']);
        $this->assertArrayNotHasKey(2, $anchors['parachute']);
        $this->assertArrayNotHasKey(1, $anchors['parachute']);
    }

    public function test_posture_hands_legs_and_feet_preserve_source_score_columns(): void
    {
        $anchors = HineInstrument::clinicalAnchors();

        $this->assertSame([
            3 => 'Manos abiertas',
            1 => 'Intermitente pulgar aducto o manos cerradas',
            0 => 'Persistente pulgar aducto o manos cerradas',
        ], $anchors['hands']);
        $this->assertArrayNotHasKey(2, $anchors['hands']);

        $this->assertSame([
            3 => 'En sedestación: capacidad para mantenerse sentado con la espalda recta o ligeramente inclinada (sedestación con las piernas estiradas). En supino y bipedestación: piernas en posición neutra rectas o ligeramente dobladas.',
            2 => 'En supino y bipedestación: ligera rotación interna o rotación externa.',
            1 => 'En sedestación: capacidad para mantenerse sentado con la espalda recta pero las rodillas flexionadas 15-20%. En supino y bipedestación: rotación interna o rotación externa de caderas.',
            0 => 'En sedestación: incapacidad para mantenerse sentado a menos que las rodillas queden marcadamente flexionadas (no mantiene la sedestación con las piernas estiradas). En supino y bipedestación: marcada rotación interna o rotación externa o extensión o flexión fija o contracturas en caderas y rodillas.',
        ], $anchors['legs']);

        $this->assertSame([
            3 => 'Centrados en posición neutra. Dedos de los pies rectos, entre flexión y extensión.',
            1 => 'Ligera rotación interna o rotación externa. Intermitente tendencia a mantenerse de puntillas o a extender o flexionar los dedos.',
            0 => 'Marcada rotación interna o rotación externa de tobillo. Persistente tendencia a mantenerse de puntillas o a extender o flexionar los dedos.',
        ], $anchors['feet']);
        $this->assertArrayNotHasKey(2, $anchors['feet']);
    }

    public function test_hine_visual_reference_dimensions_are_preserved(): void
    {
        $expected = [
            'ankle_dorsiflexion.png' => [804, 62],
            'arm_protection.png' => [689, 130],
            'crawling.png' => [1064, 119],
            'head_sitting.png' => [843, 167],
            'hip_adductors.png' => [966, 182],
            'lateral_suspension.png' => [1052, 111],
            'legs.png' => [415, 161],
            'parachute.png' => [615, 104],
            'passive_shoulder_elevation.png' => [301, 139],
            'popliteal_angle.png' => [1039, 85],
            'pull_to_sit.png' => [796, 137],
            'scarf_sign.png' => [641, 173],
            'sitting.png' => [648, 154],
            'supine_kicking.png' => [511, 110],
            'trunk_sitting.png' => [668, 177],
            'ventral_suspension.png' => [842, 103],
            'vertical_suspension.png' => [800, 184],
        ];

        $this->assertCount(17, HineInstrument::visualReferenceMap());

        foreach ($expected as $filename => [$width, $height]) {
            $path = public_path('images/hine/'.$filename);
            $size = getimagesize($path);

            $this->assertNotFalse($size, $filename.' must remain a readable image.');
            $this->assertSame($width, $size[0], $filename.' width changed.');
            $this->assertSame($height, $size[1], $filename.' height changed.');
        }
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
        $this->assertSame('Prediciendo la GMFCS', $aid['global_score_heading']);
        $this->assertSame('Número de asimetrías', $aid['asymmetry_heading']);
        $this->assertSame('Puntuaciones de Corte para el Alto Riesgo en PC', $aid['high_risk_heading']);
        $this->assertCount(4, $aid['references']);
        $this->assertStringContainsString('Romeo, D. M. et al., (2013)', $aid['references'][0]);
        $this->assertStringContainsString('Pietruszewski, L. et al., (2021)', $aid['references'][3]);
        $this->assertArrayNotHasKey(7, $aid['high_risk_cutoffs_by_age_months']);
        $this->assertStringContainsString('no se interpolan', $aid['age_rule']);
    }

}
