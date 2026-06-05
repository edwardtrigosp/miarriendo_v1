<?php

/**
 * Controlador de contratos de arrendamiento.
 * Maneja la solicitud del inquilino y la aprobación/rechazo del propietario.
 */
class ContratoController
{
    /** Procesa la solicitud de arriendo (crea alquiler + contrato). */
    public function crearSolicitud(string $id): void
    {
        requiereLogin();
        $propiedad = Propiedad::buscarPorId((int) $id);

        if ($propiedad === null) {
            http_response_code(404);
            view('404', ['title' => 'Propiedad no encontrada | 404']);
            return;
        }

        $inquilinoId   = (int) $_SESSION['usuario_id'];
        $propietarioId = (int) $propiedad['propietario_id'];

        // Validaciones de negocio
        $error = null;
        $fechaInicio = trim($_POST['fecha_inicio'] ?? '');
        $duracion    = (int) ($_POST['duracion_meses'] ?? 0);
        $inicio      = $fechaInicio !== '' ? DateTime::createFromFormat('Y-m-d', $fechaInicio) : false;

        if ($inquilinoId === $propietarioId) {
            $error = 'No puedes solicitar el arriendo de tu propia propiedad.';
        } elseif ((int) $propiedad['disponible'] !== 1) {
            $error = 'Esta propiedad no está disponible para arriendo.';
        } elseif ($inicio === false) {
            $error = 'Indica una fecha de inicio válida.';
        } elseif ($duracion < 1 || $duracion > 60) {
            $error = 'La duración debe estar entre 1 y 60 meses.';
        } elseif (Contrato::existeSolicitudViva((int) $id, $inquilinoId)) {
            $error = 'Ya tienes una solicitud activa para esta propiedad.';
        }

        if ($error !== null) {
            view('solicitar_arriendo', [
                'title'     => 'Solicitar arriendo | miarriendo.online',
                'propiedad' => $propiedad,
                'error'     => $error,
            ]);
            return;
        }

        $fechaFin = (clone $inicio)->modify("+{$duracion} months")->format('Y-m-d');

        // Cláusulas definitivas (instantánea con los datos reales de ambas partes)
        $clausulasTexto = ContratoPlantilla::comoTexto([
            'arrendador'     => trim($propiedad['propietario_nombre'] . ' ' . $propiedad['propietario_apellidos']),
            'arrendatario'   => trim($_SESSION['usuario_nombre'] ?? 'EL ARRENDATARIO'),
            'inmueble'       => trim($propiedad['calle'] . ' ' . ($propiedad['numero_exterior'] ?? ''))
                . (!empty($propiedad['barrio']) ? ', ' . $propiedad['barrio'] : ''),
            'ciudad'         => $propiedad['ciudad'],
            'tipo'           => $propiedad['tipo_propiedad'],
            'precio'         => $propiedad['precio_alquiler_mensual'],
            'deposito'       => $propiedad['deposito'],
            'duracion_meses' => $duracion,
            'fecha_inicio'   => $inicio->format('Y-m-d'),
            'mascotas'       => $propiedad['mascotas_permitidas'],
            'amueblado'      => $propiedad['amueblado'],
            'extra'          => $propiedad['clausulas_contrato'] ?? '',
        ]);

        $pdo = Database::conexion();
        $pdo->beginTransaction();
        try {
            $alquilerId = Alquiler::crear([
                'propiedad_id'   => (int) $id,
                'inquilino_id'   => $inquilinoId,
                'fecha_inicio'   => $inicio->format('Y-m-d'),
                'fecha_fin'      => $fechaFin,
                'precio_mensual' => $propiedad['precio_alquiler_mensual'],
                'deposito'       => $propiedad['deposito'],
                'estado'         => 'pendiente',
            ]);

            $contratoId = Contrato::crear([
                'alquiler_id'    => $alquilerId,
                'propietario_id' => $propietarioId,
                'inquilino_id'   => $inquilinoId,
                'clausulas'      => $clausulasTexto,
                'monto_mensual'  => $propiedad['precio_alquiler_mensual'],
                'deposito'       => $propiedad['deposito'],
                'fecha_inicio'   => $inicio->format('Y-m-d'),
                'fecha_fin'      => $fechaFin,
                'duracion_meses' => $duracion,
                'estado'         => 'borrador',
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            view('solicitar_arriendo', [
                'title'     => 'Solicitar arriendo | miarriendo.online',
                'propiedad' => $propiedad,
                'error'     => 'No se pudo crear la solicitud. Intenta de nuevo.',
            ]);
            return;
        }

        redirect('/contrato/' . $contratoId);
    }

    /** Muestra el detalle de un contrato (solo propietario o inquilino). */
    public function ver(string $id): void
    {
        requiereLogin();
        $contrato = Contrato::buscarPorId((int) $id);

        if ($contrato === null) {
            http_response_code(404);
            view('404', ['title' => 'Contrato no encontrado | 404']);
            return;
        }

        $usuarioId = (int) $_SESSION['usuario_id'];
        if ($usuarioId !== (int) $contrato['propietario_id'] && $usuarioId !== (int) $contrato['inquilino_id']) {
            http_response_code(403);
            view('404', ['title' => 'Sin acceso | 403']);
            return;
        }

        view('contrato', [
            'title'    => 'Contrato | miarriendo.online',
            'contrato' => $contrato,
            'esDueno'  => $usuarioId === (int) $contrato['propietario_id'],
        ]);
    }

    /** El propietario aprueba la solicitud: contrato -> enviado. */
    public function aprobar(string $id): void
    {
        $contrato = $this->soloPropietario((int) $id);
        if ($contrato === null) {
            return;
        }
        if ($contrato['estado'] === 'borrador') {
            Contrato::cambiarEstado((int) $id, 'enviado');
        }
        redirect('/contrato/' . (int) $id);
    }

    /** El propietario rechaza la solicitud: contrato -> rechazado, alquiler -> cancelado. */
    public function rechazar(string $id): void
    {
        $contrato = $this->soloPropietario((int) $id);
        if ($contrato === null) {
            return;
        }
        if (in_array($contrato['estado'], ['borrador', 'enviado'], true)) {
            Contrato::cambiarEstado((int) $id, 'rechazado');
            Alquiler::cambiarEstado((int) $contrato['alquiler_id'], 'cancelado');
        }
        redirect('/contrato/' . (int) $id);
    }

    /**
     * Carga el contrato y verifica que el usuario en sesión sea el propietario.
     * Devuelve el contrato o null (ya respondió 403/404).
     */
    private function soloPropietario(int $id): ?array
    {
        requiereLogin();
        $contrato = Contrato::buscarPorId($id);

        if ($contrato === null) {
            http_response_code(404);
            view('404', ['title' => 'Contrato no encontrado | 404']);
            return null;
        }
        if ((int) $_SESSION['usuario_id'] !== (int) $contrato['propietario_id']) {
            http_response_code(403);
            view('404', ['title' => 'Sin acceso | 403']);
            return null;
        }
        return $contrato;
    }
}
