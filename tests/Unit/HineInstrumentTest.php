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
        $this->assertSame([5,6,2,8,5], array_map(fn ($s) => count($s['items']), $sections));
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
}
