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
            'trunk_sitting' => [
                3 => 'Recto',
                1 => 'Ligeramente curvado o inclinado lateralmente',
                0 => 'Muy curvado; hiper-extendido; inclinado lateralmente',
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
            'legs' => [
                3 => 'En sedestación: capacidad para mantenerse sentado con la espalda recta o ligeramente inclinada (sedestación con las piernas estiradas). En supino y bipedestación: piernas en posición neutra rectas o ligeramente dobladas.',
                2 => 'En supino y bipedestación: ligera rotación interna o rotación externa.',
                1 => 'En sedestación: capacidad para mantenerse sentado con la espalda recta pero las rodillas flexionadas 15-20%. En supino y bipedestación: rotación interna o rotación externa de caderas.',
                0 => 'En sedestación: incapacidad para mantenerse sentado a menos que las rodillas queden marcadamente flexionadas (no mantiene la sedestación con las piernas estiradas). En supino y bipedestación: marcada rotación interna o rotación externa o extensión o flexión fija o contracturas en caderas y rodillas.',
            ],
            'feet' => [
                3 => 'Centrados en posición neutra. Dedos de los pies rectos, entre flexión y extensión.',
                1 => 'Ligera rotación interna o rotación externa. Intermitente tendencia a mantenerse de puntillas o a extender o flexionar los dedos.',
                0 => 'Marcada rotación interna o rotación externa de tobillo. Persistente tendencia a mantenerse de puntillas o a extender o flexionar los dedos.',
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
            'hip_adductors' => [
                3 => 'Rango: 150°-80°',
                2 => '150-160°',
                1 => '>170°',
                0 => '<80°',
            ],
            'popliteal_angle' => [
                3 => 'Rango: 150°-100°',
                2 => '150-160°',
                1 => '~90° o >170°',
                0 => '<80°',
            ],
            'ankle_dorsiflexion' => [
                3 => 'Rango: 30°-85°',
                2 => '20-30°',
                1 => '<20° o 90°',
                0 => '>90°',
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
                1 => 'Brazo semiflexionado',
                0 => 'Brazo completamente flexionado',
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
            'head_sitting' => 'head_sitting.png',
            'trunk_sitting' => 'trunk_sitting.png',
            'legs' => 'legs.png',
            'scarf_sign' => 'scarf_sign.png',
            'passive_shoulder_elevation' => 'passive_shoulder_elevation.png',
            'hip_adductors' => 'hip_adductors.png',
            'popliteal_angle' => 'popliteal_angle.png',
            'ankle_dorsiflexion' => 'ankle_dorsiflexion.png',
            'pull_to_sit' => 'pull_to_sit.png',
            'ventral_suspension' => 'ventral_suspension.png',
            'arm_protection' => 'arm_protection.png',
            'vertical_suspension' => 'vertical_suspension.png',
            'lateral_suspension' => 'lateral_suspension.png',
            'parachute' => 'parachute.png',
            'sitting' => 'sitting.png',
            'supine_kicking' => 'supine_kicking.png',
            'crawling' => 'crawling.png',
        ];
    }

    public static function motorMilestones(): array
    {
        return [
            [
                'key'=>'head_control',
                'label'=>'Control cefálico',
                'options'=>[
                    'Incapaz de mantener la cabeza erguida',
                    'Tambaleante',
                    'Mantiene la posición erguida todo el tiempo',
                ],
                'normal_ages'=>['Normal antes de los 3m','Normal hasta los 4m','Normal desde los 5m'],
                'age_note'=>'Por favor, anote la edad a la cual se consigue la máxima habilidad.',
            ],
            [
                'key'=>'sitting','label'=>'Sedestación','visual'=>true,
                'options'=>['No puede mantenerse sentado','Con soporte en caderas','Se apoya','Sedestación estable','Pivota (rota)'],
                'normal_ages'=>['Normal a los 4m','Normal a los 6m','Normal a los 7-8m','Normal a los 9m'],
            ],
            [
                'key'=>'voluntary_grasp','label'=>'Agarre voluntario','instruction'=>'Observe el lado.',
                'options'=>['No agarra','Usa toda la mano','Dedo índice y pulgar pero agarre inmaduro','Agarre con pinza'],
            ],
            [
                'key'=>'supine_kicking','label'=>'Habilidad para patalear en supino','visual'=>true,
                'options'=>['No patalea','Patalea horizontalmente pero no eleva las piernas','Eleva las piernas (verticalmente)','Se toca las piernas','Se toca los dedos'],
                'normal_ages'=>['Normal a los 3m','Normal a los 4-5m','Normal a los 5-6m'],
            ],
            [
                'key'=>'rolling','label'=>'Volteo','instruction'=>'Observe hacia qué lado(s).',
                'options'=>['No voltea','Voltea hacia un lado','De prono a supino','De supino a prono'],
                'normal_ages'=>['Normal a los 4m','Normal a los 6m','Normal a los 6m'],
            ],
            [
                'key'=>'crawling','label'=>'Gateo','instruction'=>'Observe si arrastra las nalgas.','visual'=>true,
                'options'=>['No levanta la cabeza','Sobre los codos','Sobre las manos extendidas','Gatea arrastrándose sobre el abdomen','Gatea sobre manos y rodillas'],
                'normal_ages'=>['Normal a los 3m','Normal a los 4m','Normal a los 8m','Normal a los 10m'],
            ],
            [
                'key'=>'standing','label'=>'Bipedestación',
                'options'=>['No soporta el peso','Soporta su peso','Se mantiene de pie con soporte','Se mantiene de pie sin ayuda'],
                'normal_ages'=>['Normal a los 4m','Normal a los 7m','Normal a los 12m'],
            ],
            [
                'key'=>'walking','label'=>'Marcha',
                'options'=>['Rebota (intenta botar)','Camina con apoyo','Camina independiente'],
                'normal_ages'=>['Normal a los 6m','Normal a los 12m','Normal a los 15m'],
            ],
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


    public static function interpretationAid(): array
    {
        return [
            'global_score_ranges' => [
                ['operator' => '<', 'value' => 40, 'label' => '<40'],
                ['operator' => 'between', 'minimum' => 40, 'maximum' => 60, 'label' => '40–60'],
                ['operator' => '>', 'value' => 60, 'label' => '>60'],
            ],
            'asymmetry_attention_threshold' => 4,
            'high_risk_cutoffs_by_age_months' => [
                3 => 56,
                6 => 59,
                9 => 62,
                12 => 65,
            ],
            'note' => 'Apoyo para la interpretación. Las puntuaciones de corte son una referencia del material proporcionado y no constituyen por sí solas un diagnóstico.',
            'age_rule' => 'Sólo se muestran los puntos de corte explícitos para 3, 6, 9 y 12 meses; no se interpolan edades intermedias.',
        ];
    }

    private function __construct() {}
}
