<?php

namespace App\Support;

final class HineInstrument
{
    public const VERSION = 'v ES 08.12.18';

    public const URPE_SCORES = [0, 0.5, 1, 1.5, 2, 2.5, 3];

    public static function neurologicalSections(): array
    {
        return [
            'cranial_nerves' => [
                'label' => 'Evaluación de la función de los pares craneales',
                'maximum' => 15,
                'items' => [
                    ['key'=>'facial_appearance','label'=>'Apariencia facial','instruction'=>'En reposo y al llorar o ser estimulado.'],
                    ['key'=>'eye_movements','label'=>'Movimientos oculares'],
                    ['key'=>'visual_response','label'=>'Respuesta visual','instruction'=>'Habilidad para seguir un objeto blanco y negro.'],
                    ['key'=>'auditory_response','label'=>'Respuesta auditiva','instruction'=>'Respuesta a un sonajero.'],
                    ['key'=>'sucking_swallowing','label'=>'Succión / deglución','instruction'=>'Observe al niño succionando del pecho o de un biberón. Si es mayor, pregunte sobre alimentación asociada a tos o a excesiva salivación.'],
                ],
            ],
            'posture' => [
                'label'=>'Evaluación de la postura','note'=>'Observe las asimetrías.','maximum'=>18,
                'items'=>[
                    ['key'=>'head_sitting','label'=>'Cabeza en sedestación','visual'=>true],
                    ['key'=>'trunk_sitting','label'=>'Tronco en sedestación','visual'=>true],
                    ['key'=>'arms_rest','label'=>'Brazos en reposo'],
                    ['key'=>'hands','label'=>'Manos'],
                    ['key'=>'legs','label'=>'Piernas','instruction'=>'En sedestación. En supino y bipedestación.','visual'=>true],
                    ['key'=>'feet','label'=>'Pies','instruction'=>'En supino y bipedestación.'],
                ],
            ],
            'movements' => [
                'label'=>'Evaluación de los movimientos','maximum'=>6,
                'items'=>[
                    ['key'=>'quantity','label'=>'Cantidad','instruction'=>'Observar con el niño en decúbito supino.'],
                    ['key'=>'quality','label'=>'Calidad','instruction'=>'Observar la actividad motora voluntaria espontánea del niño durante el transcurso de la evaluación.'],
                ],
            ],
            'tone' => [
                'label'=>'Evaluación del tono','maximum'=>24,
                'items'=>[
                    ['key'=>'scarf_sign','label'=>'Signo de la bufanda','laterality'=>true,'visual'=>true],
                    ['key'=>'passive_shoulder_elevation','label'=>'Elevación pasiva del hombro','laterality'=>true,'visual'=>true],
                    ['key'=>'pronation_supination','label'=>'Pronación / supinación','laterality'=>true],
                    ['key'=>'hip_adductors','label'=>'Aductores de cadera','laterality'=>true,'visual'=>true],
                    ['key'=>'popliteal_angle','label'=>'Ángulo poplíteo','laterality'=>true,'visual'=>true],
                    ['key'=>'ankle_dorsiflexion','label'=>'Dorsiflexión de tobillo','laterality'=>true,'visual'=>true],
                    ['key'=>'pull_to_sit','label'=>'Pull to sit','visual'=>true],
                    ['key'=>'ventral_suspension','label'=>'Suspensión ventral','visual'=>true],
                ],
            ],
            'reflexes_reactions' => [
                'label'=>'Reflejos y reacciones','maximum'=>15,
                'items'=>[
                    ['key'=>'arm_protection','label'=>'Protección del brazo','laterality'=>true,'visual'=>true],
                    ['key'=>'vertical_suspension','label'=>'Suspensión vertical','visual'=>true],
                    ['key'=>'lateral_suspension','label'=>'Suspensión lateral','instruction'=>'Describir el lado superior.','laterality'=>true,'visual'=>true],
                    ['key'=>'parachute','label'=>'Paracaídas','instruction'=>'Después de los 6 meses.','visual'=>true],
                    ['key'=>'tendon_reflexes','label'=>'Reflejos tendinosos','instruction'=>'Bíceps, rodilla, tobillo.'],
                ],
            ],
        ];
    }

    public static function motorMilestones(): array
    {
        return [
            ['key'=>'head_control','label'=>'Control cefálico'],
            ['key'=>'sitting','label'=>'Sedestación','visual'=>true],
            ['key'=>'voluntary_grasp','label'=>'Agarre voluntario','instruction'=>'Observe el lado.'],
            ['key'=>'supine_kicking','label'=>'Habilidad para patalear en supino','visual'=>true],
            ['key'=>'rolling','label'=>'Volteo','instruction'=>'Observe hacia qué lado(s).'],
            ['key'=>'crawling','label'=>'Gateo','instruction'=>'Observe si arrastra las nalgas.','visual'=>true],
            ['key'=>'standing','label'=>'Bipedestación'],
            ['key'=>'walking','label'=>'Marcha'],
        ];
    }

    public static function behaviorItems(): array
    {
        return [
            ['key'=>'consciousness','label'=>'Estado de consciencia','option_count'=>6],
            ['key'=>'emotional_state','label'=>'Estado emocional','option_count'=>5],
            ['key'=>'social_interaction','label'=>'Interacción social','option_count'=>4],
        ];
    }

    private function __construct() {}
}
