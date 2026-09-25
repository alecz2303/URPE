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


    /**
     * Textual anchors transcribed from the URPE-provided HINE.
     * Missing keys are intentional: the source contains a blank cell or a
     * visual criterion that must not be replaced with invented prose.
     */
    public static function clinicalAnchors(): array
    {
        return [
            'facial_appearance' => [
                3 => 'Sonríe o reacciona a los estímulos cerrando los ojos y haciendo muecas',
                1 => 'Cierra los ojos pero no con firmeza, pobre expresión facial',
                0 => 'Apariencia facial inexpresiva, no reacciona a los estímulos',
            ],
            'eye_movements' => [
                3 => 'Movimientos oculares conjugados normales',
                1 => 'Intermitente desviación de los ojos o movimientos anormales',
                0 => 'Continuo desviación de los ojos o movimientos anormales',
            ],
            'visual_response' => [
                3 => 'Sigue el objeto en un arco completo',
                1 => 'Sigue el objeto en un arco incompleto o asimétrico',
                0 => 'No sigue el objeto',
            ],
            'auditory_response' => [
                3 => 'Responde al estímulo desde ambos lados',
                1 => 'Reacción dudosa al estímulo o responde asimétricamente',
                0 => 'No responde',
            ],
            'sucking_swallowing' => [
                3 => 'Buena succión y deglución',
                1 => 'Pobre succión y/o deglución',
                0 => 'No reflejo de succión, no deglución',
            ],
            'head_sitting' => [
                3 => 'Recta; en la línea media',
                1 => 'Ligeramente inclinada hacia un lado o hacia atrás o delante',
                0 => 'Marcadamente inclinada hacia un lado o atrás o delante',
            ],
            'arms_rest' => [
                3 => 'En posición neutra, centrados o ligeramente flexionados',
                2 => 'Ligera rotación interna o rotación externa',
                1 => 'Intermitente postura distónica',
                0 => 'Marcada rotación interna o rotación externa o postura distónica; postura hemiparética',
            ],
            'hands' => [
                3 => 'Manos abiertas',
                1 => 'Intermitente pulgar aducto o manos cerradas',
                0 => 'Persistente pulgar aducto o manos cerradas',
            ],
            'quantity' => [
                3 => 'Normales',
                1 => 'Excesivos o lentos',
                0 => 'Mínimos o nulos',
            ],
            'quality' => [
                3 => 'Libres, alternantes, y suaves',
                2 => 'Bruscos, entrecortados',
                1 => 'Ligero temblor',
                0 => 'Espasmódicos y sincrónicos; espasmos extensores; atetoides; atáxicos; muy temblorosos; espasmos mioclónicos; movimientos distónicos',
            ],
            'passive_shoulder_elevation' => [
                3 => 'Resistencia superable',
                2 => 'Dificultad para vencer la resistencia',
                1 => 'No existe resistencia',
                0 => 'Resistencia no superable',
            ],
            'pronation_supination' => [
                3 => 'Pronación y supinación completas, no existe resistencia',
                1 => 'Resistencia superable para la pronación/supinación completa',
                0 => 'Pronación completa y supinación no posible. Marcada resistencia',
            ],
            'arm_protection' => [
                3 => 'Brazo y mano extendidos',
                2 => 'Brazo semiflexionado',
                1 => 'Brazo completamente flexionado',
            ],
            'vertical_suspension' => [
                3 => 'Pataleo simétrico y alternante',
                1 => 'Una pierna patalea más o pataleo pobre',
                0 => 'No patalea incluso si es estimulado, o adopta una posición "en tijera"',
            ],
            'tendon_reflexes' => [
                3 => 'Se obtienen con facilidad',
                2 => 'Ligeramente exaltados',
                1 => 'Exaltados',
                0 => 'Clono o ausencia',
            ],
        ];
    }


    /**
     * Exact visual references identified in the URPE-provided PDF.
     * Asset filenames intentionally preserve extraction order so each image
     * can be verified against the source before publication in the UI.
     */
    public static function visualReferenceMap(): array
    {
        return [
            'head_sitting' => ['img-002.png','img-003.png','img-004.png'],
            'trunk_sitting' => ['img-005.png','img-006.png','img-007.png','img-008.png','img-009.png'],
            'legs' => ['img-010.png','img-011.png','img-012.png'],
            'scarf_sign' => ['img-017.png','img-018.png','img-019.png','img-020.png'],
            'passive_shoulder_elevation' => ['img-021.png','img-022.png','img-023.png'],
            'hip_adductors' => ['img-024.png','img-025.png','img-026.png','img-027.png'],
            'popliteal_angle' => ['img-028.png','img-029.png','img-030.png','img-031.png','img-032.png'],
            'ankle_dorsiflexion' => ['img-033.png','img-034.png','img-035.png','img-036.png'],
            'pull_to_sit' => ['img-037.png','img-038.png','img-039.png'],
            'ventral_suspension' => ['img-040.png','img-041.png','img-042.png'],
            'arm_protection' => ['img-045.png','img-046.png','img-047.png'],
            'vertical_suspension' => ['img-048.png','img-049.png','img-050.png'],
            'lateral_suspension' => ['img-051.png','img-052.png','img-053.png','img-054.png'],
            'parachute' => ['img-055.png','img-056.png'],
            'sitting' => ['img-057.png','img-058.png','img-059.png','img-060.png'],
            'supine_kicking' => ['img-061.png','img-062.png','img-063.png'],
            'crawling' => ['img-064.png','img-065.png','img-066.png','img-067.png'],
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
            ['key'=>'consciousness','label'=>'Estado de consciencia','option_count'=>6,'options'=>['No despierta','Soñoliento','Duerme pero se despierta fácilmente','Despierto pero no tiene interés','Pierde el interés','Mantiene el interés']],
            ['key'=>'emotional_state','label'=>'Estado emocional','option_count'=>5,'options'=>['Irritable, inconsolable','Irritable, consolable por cuidador','Irritable cuando se le acercan','No contento o triste','Contento y sonriente']],
            ['key'=>'social_interaction','label'=>'Interacción social','option_count'=>4,'options'=>['Evita, se retira','Vacilante','Acepta el acercamiento','Amistoso']],
        ];
    }

    private function __construct() {}
}
