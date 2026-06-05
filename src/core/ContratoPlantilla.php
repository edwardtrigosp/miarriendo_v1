<?php

/**
 * Genera el clausulado de un contrato de arrendamiento de vivienda urbana.
 * Combina una plantilla estándar (rellenada con los datos reales) con las
 * cláusulas adicionales que el propietario definió al publicar la propiedad.
 *
 * Se usa tanto para la vista previa en el detalle de la propiedad como para
 * el contrato definitivo y su PDF (fases siguientes).
 */
class ContratoPlantilla
{
    /**
     * Construye las cláusulas del contrato.
     *
     * @param array $d Datos: arrendador, arrendatario, inmueble, ciudad, tipo,
     *                 precio, deposito, duracion_meses, fecha_inicio,
     *                 mascotas (bool), amueblado (bool), extra (string).
     * @return array<int, array{titulo:string, texto:string}>
     */
    public static function clausulas(array $d): array
    {
        $arrendador   = ($d['arrendador']   ?? '') ?: 'EL ARRENDADOR';
        $arrendatario = ($d['arrendatario'] ?? '') ?: 'EL ARRENDATARIO';
        $inmueble     = ($d['inmueble']     ?? '') ?: '(dirección del inmueble)';
        $ciudad       = ($d['ciudad']       ?? '') ?: '';
        $tipo         = ($d['tipo']         ?? '') ?: 'inmueble';
        $precio       = self::money($d['precio'] ?? null);
        $deposito     = self::money($d['deposito'] ?? null);

        $clausulas = [];

        $clausulas[] = [
            'titulo' => 'PRIMERA — OBJETO',
            'texto'  => "EL ARRENDADOR ($arrendador) entrega a EL ARRENDATARIO ($arrendatario), "
                . "a título de arrendamiento, el bien inmueble de tipo $tipo ubicado en $inmueble"
                . ($ciudad !== '' ? ", en la ciudad de $ciudad" : '') . '.',
        ];

        $clausulas[] = [
            'titulo' => 'SEGUNDA — CANON DE ARRENDAMIENTO',
            'texto'  => "El canon mensual de arrendamiento es de $precio (pesos colombianos), "
                . 'que EL ARRENDATARIO pagará dentro de los primeros cinco (5) días de cada mes.',
        ];

        if (!empty($d['deposito'])) {
            $clausulas[] = [
                'titulo' => 'TERCERA — DEPÓSITO',
                'texto'  => "EL ARRENDATARIO entrega la suma de $deposito (pesos colombianos) "
                    . 'como depósito en garantía del cumplimiento de las obligaciones del presente contrato.',
            ];
        }

        $clausulas[] = [
            'titulo' => 'CUARTA — DURACIÓN',
            'texto'  => self::textoDuracion($d['duracion_meses'] ?? null, $d['fecha_inicio'] ?? null),
        ];

        $clausulas[] = [
            'titulo' => 'QUINTA — DESTINACIÓN',
            'texto'  => 'El inmueble se destinará exclusivamente para vivienda y no podrá '
                . 'dársele un uso distinto sin autorización escrita de EL ARRENDADOR.',
        ];

        $clausulas[] = [
            'titulo' => 'SEXTA — SERVICIOS PÚBLICOS',
            'texto'  => 'Los servicios públicos domiciliarios del inmueble estarán a cargo de '
                . 'EL ARRENDATARIO durante la vigencia del contrato.',
        ];

        $clausulas[] = [
            'titulo' => 'SÉPTIMA — ESTADO Y MOBILIARIO',
            'texto'  => !empty($d['amueblado'])
                ? 'El inmueble se entrega amueblado; EL ARRENDATARIO se obliga a conservar el mobiliario en buen estado.'
                : 'El inmueble se entrega sin amueblar y en buen estado de conservación.',
        ];

        $clausulas[] = [
            'titulo' => 'OCTAVA — MASCOTAS',
            'texto'  => !empty($d['mascotas'])
                ? 'Se permite la tenencia de mascotas, conforme al reglamento de propiedad horizontal aplicable.'
                : 'No se permite la tenencia de mascotas en el inmueble.',
        ];

        // Cláusulas adicionales del propietario (texto libre)
        $extra = trim((string) ($d['extra'] ?? ''));
        if ($extra !== '') {
            $clausulas[] = [
                'titulo' => 'NOVENA — CLÁUSULAS ADICIONALES',
                'texto'  => $extra,
            ];
        }

        return $clausulas;
    }

    /** Une las cláusulas en un único texto plano (para hash y PDF). */
    public static function comoTexto(array $d): string
    {
        $lineas = [];
        foreach (self::clausulas($d) as $c) {
            $lineas[] = $c['titulo'];
            $lineas[] = $c['texto'];
            $lineas[] = '';
        }
        return trim(implode("\n", $lineas));
    }

    private static function textoDuracion(?int $meses, ?string $fechaInicio): string
    {
        $base = $meses
            ? "El término de duración del contrato es de $meses meses"
            : 'El término de duración del contrato se acordará entre las partes';
        if (!empty($fechaInicio)) {
            $base .= ', contados a partir del ' . self::fecha($fechaInicio);
        }
        return $base . ', prorrogable por acuerdo entre las partes.';
    }

    private static function money($valor): string
    {
        if ($valor === null || $valor === '') {
            return '$________';
        }
        return '$' . number_format((float) $valor, 0, ',', '.');
    }

    private static function fecha(string $iso): string
    {
        $ts = strtotime($iso);
        return $ts ? date('d/m/Y', $ts) : $iso;
    }
}
