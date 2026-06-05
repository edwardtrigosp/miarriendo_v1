<?php

require_once BASE_PATH . '/src/lib/fpdf/fpdf.php';

/**
 * Genera el PDF de un contrato de arrendamiento usando FPDF (sin Composer).
 * Las fuentes core de FPDF usan Latin-1, por eso convertimos el texto UTF-8.
 */
class ContratoPdf extends FPDF
{
    private const MORADO = [137, 23, 212];

    private array $c;

    public function __construct(array $contrato)
    {
        parent::__construct('P', 'mm', 'A4');
        $this->c = $contrato;
        $this->SetAutoPageBreak(true, 18);
        $this->SetTitle('Contrato ' . ($contrato['contrato_id'] ?? ''));
    }

    /** Cabecera de cada página. */
    public function Header(): void
    {
        $this->SetFont('Helvetica', 'B', 16);
        $this->SetTextColor(...self::MORADO);
        $this->Cell(0, 9, $this->t('miarriendo.online'), 0, 1, 'L');

        $this->SetTextColor(20, 20, 20);
        $this->SetFont('Helvetica', 'B', 12);
        $this->Cell(0, 7, $this->t('CONTRATO DE ARRENDAMIENTO DE VIVIENDA URBANA'), 0, 1, 'C');
        $this->SetDrawColor(...self::MORADO);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY() + 1, 200, $this->GetY() + 1);
        $this->Ln(6);
    }

    /** Pie de cada página. */
    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(130, 130, 130);
        $this->Cell(0, 10, $this->t('miarriendo.online  ·  Página ' . $this->PageNo()), 0, 0, 'C');
    }

    /** Construye el documento completo. */
    public function construir(): void
    {
        $this->AddPage();

        $arrendador   = trim(($this->c['propietario_nombre'] ?? '') . ' ' . ($this->c['propietario_apellidos'] ?? ''));
        $arrendatario = trim(($this->c['inquilino_nombre'] ?? '') . ' ' . ($this->c['inquilino_apellidos'] ?? ''));

        // Partes
        $this->SetTextColor(20, 20, 20);
        $this->SetFont('Helvetica', '', 10);
        $this->MultiCell(0, 5.5, $this->t(
            "Entre $arrendador, en calidad de EL ARRENDADOR, y $arrendatario, en calidad de "
            . "EL ARRENDATARIO, se celebra el presente contrato de arrendamiento sobre el inmueble "
            . '"' . ($this->c['propiedad_titulo'] ?? '') . '", sujeto a las siguientes cláusulas:'
        ));
        $this->Ln(3);

        // Datos clave (tabla simple)
        $this->datosClave();
        $this->Ln(2);

        // Cláusulas (instantánea firmada)
        $this->clausulas((string) ($this->c['clausulas'] ?? ''));

        // Firma
        $this->bloqueFirma();
    }

    private function datosClave(): void
    {
        $money = static fn($v) => '$' . number_format((float) $v, 0, ',', '.');
        $fmt   = static fn($iso) => $iso ? date('d/m/Y', strtotime($iso)) : '—';

        $filas = [
            ['Canon mensual', $money($this->c['monto_mensual'] ?? 0)],
            ['Depósito', !empty($this->c['deposito']) ? $money($this->c['deposito']) : '—'],
            ['Fecha de inicio', $fmt($this->c['fecha_inicio'] ?? null)],
            ['Fecha de fin', $fmt($this->c['fecha_fin'] ?? null)],
            ['Duración', ($this->c['duracion_meses'] ?? '—') . ' meses'],
        ];

        $this->SetFont('Helvetica', '', 9.5);
        foreach ($filas as [$k, $v]) {
            $this->SetFillColor(245, 243, 250);
            $this->SetFont('Helvetica', 'B', 9.5);
            $this->Cell(45, 7, $this->t($k), 1, 0, 'L', true);
            $this->SetFont('Helvetica', '', 9.5);
            $this->Cell(0, 7, $this->t((string) $v), 1, 1, 'L');
        }
    }

    private function clausulas(string $texto): void
    {
        $this->Ln(3);
        // El clausulado viene como bloques "TÍTULO\ntexto" separados por línea en blanco.
        $bloques = preg_split("/\n\s*\n/", trim($texto));
        foreach ($bloques as $bloque) {
            $lineas = explode("\n", trim($bloque), 2);
            $titulo = $lineas[0] ?? '';
            $cuerpo = $lineas[1] ?? '';

            $this->SetFont('Helvetica', 'B', 10);
            $this->SetTextColor(...self::MORADO);
            $this->MultiCell(0, 5.5, $this->t($titulo));

            if ($cuerpo !== '') {
                $this->SetFont('Helvetica', '', 10);
                $this->SetTextColor(30, 30, 30);
                $this->MultiCell(0, 5.5, $this->t($cuerpo));
            }
            $this->Ln(2);
        }
    }

    private function bloqueFirma(): void
    {
        $this->Ln(6);
        $this->SetDrawColor(200, 200, 200);
        $this->SetLineWidth(0.2);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(4);

        $firmado = ($this->c['estado'] ?? '') === 'aceptado' && !empty($this->c['firma_inquilino']);

        $this->SetFont('Helvetica', 'B', 10);
        $this->SetTextColor(20, 20, 20);
        $this->Cell(0, 6, $this->t('Firma electrónica'), 0, 1);

        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(60, 60, 60);
        if ($firmado) {
            $fecha = $this->c['fecha_aceptacion'] ? date('d/m/Y H:i', strtotime($this->c['fecha_aceptacion'])) : '';
            $this->MultiCell(0, 5, $this->t(
                "Firmado por: " . $this->c['firma_inquilino'] . "\n"
                . "Fecha: $fecha   ·   IP: " . ($this->c['ip_aceptacion'] ?? '') . "\n"
                . "Hash SHA-256 del documento:\n" . ($this->c['hash_documento'] ?? '')
            ));
        } else {
            $this->MultiCell(0, 5, $this->t('Documento pendiente de firma por el arrendatario.'));
        }
    }

    /** Convierte UTF-8 a Windows-1252 para las fuentes core de FPDF. */
    private function t(string $s): string
    {
        return mb_convert_encoding($s, 'Windows-1252', 'UTF-8');
    }
}
