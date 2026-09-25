<?php

namespace Tests\Feature;

use App\Models\HineAssessment;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HinePersistenceSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_hine_schema_preserves_source_labels_without_inventing_units_or_behavior_score(): void
    {
        $this->assertTrue(Schema::hasColumns('hine_assessments', [
            'gestational_age',
            'chronological_age',
            'corrected_age',
            'head_circumference',
            'cranial_nerves_score',
            'posture_score',
            'movements_score',
            'tone_score',
            'reflexes_reactions_score',
            'global_score',
            'asymmetry_count',
            'general_comments',
        ]));

        $this->assertFalse(Schema::hasColumn('hine_assessments', 'gestational_age_weeks'));
        $this->assertFalse(Schema::hasColumn('hine_assessments', 'chronological_age_days'));
        $this->assertFalse(Schema::hasColumn('hine_assessments', 'corrected_age_days'));
        $this->assertFalse(Schema::hasColumn('hine_assessments', 'head_circumference_cm'));
        $this->assertFalse(Schema::hasColumn('hine_assessments', 'behavior_score'));
    }

    public function test_hine_permissions_are_assigned_to_administrator_and_clinical_coordination(): void
    {
        foreach (['administrator', 'clinical_coordination'] as $roleSlug) {
            $role = Role::query()->where('slug', $roleSlug)->firstOrFail();
            $permissionSlugs = $role->permissions()->pluck('slug')->all();

            $this->assertContains('clinical_assessments.view', $permissionSlugs);
            $this->assertContains('clinical_assessments.manage', $permissionSlugs);
        }
    }

    public function test_hine_model_does_not_expose_behavior_score_as_fillable_or_cast(): void
    {
        $model = new HineAssessment();

        $this->assertNotContains('behavior_score', $model->getFillable());
        $this->assertArrayNotHasKey('behavior_score', $model->getCasts());
    }
}
