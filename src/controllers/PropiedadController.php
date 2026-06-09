<?php

/**
 * Controlador de propiedades (listar, publicar, ver ruta).
 */
class PropiedadController
{
    /** Listado de arriendos disponibles, con filtros y orden opcionales. */
    public function index(): void
    {
        $filtros = [
            'q'               => trim($_GET['q'] ?? ''),
            'departamento_id' => $_GET['departamento_id'] ?? '',
            'ciudad_id'       => $_GET['ciudad_id'] ?? '',
            'tipo'            => $_GET['tipo'] ?? '',
            'precio_min'      => $_GET['precio_min'] ?? '',
            'precio_max'      => $_GET['precio_max'] ?? '',
            'orden'           => $_GET['orden'] ?? 'recientes',
            'lat'             => $_GET['lat'] ?? null,
            'lon'             => $_GET['lon'] ?? null,
        ];

        $filas = Propiedad::listarDisponibles($filtros);
        $propiedades = array_map([Propiedad::class, 'formatearParaTarjeta'], $filas);

        view('arriendos', [
            'title'       => 'Arriendos | miarriendo.online',
            'propiedades' => $propiedades,
            'filtros'     => $filtros,
            'ubicaciones' => Ubicacion::paraFormulario(),
        ]);
    }

    /** Formulario para publicar un inmueble. */
    public function create(): void
    {
        requiereLogin();
        view('propiedades', [
            'title'       => 'Publicar Propiedad | miarriendo.online',
            'ubicaciones' => Ubicacion::paraFormulario(),
        ]);
    }

    /** Procesa el formulario de publicación. */
    public function store(): void
    {
        requiereLogin();

        $titulo   = trim($_POST['titulo'] ?? '');
        $tipo     = trim($_POST['tipo_propiedad'] ?? '');
        $precio   = $_POST['precio_alquiler_mensual'] ?? '';
        $ciudadId = (int) ($_POST['ciudad_id'] ?? 0);
        $calle    = trim($_POST['calle'] ?? '');

        // Validaciones de servidor
        $error = null;
        if ($titulo === '' || $tipo === '' || $precio === '' || $ciudadId === 0 || $calle === '') {
            $error = 'Completa los campos obligatorios (título, tipo, precio, ciudad y dirección).';
        } elseif (!is_numeric($precio) || (float) $precio <= 0) {
            $error = 'El precio mensual debe ser un número mayor a 0.';
        } elseif (!Ubicacion::ciudadExiste($ciudadId)) {
            $error = 'La ciudad seleccionada no es válida.';
        }

        if ($error !== null) {
            view('propiedades', [
                'title'       => 'Publicar Propiedad | miarriendo.online',
                'error'       => $error,
                'ubicaciones' => Ubicacion::paraFormulario(),
            ]);
            return;
        }

        // Helpers para opcionales numéricos
        $numEntero = static fn($v) => ($v ?? '') !== '' ? (int) $v : null;
        $numFloat  = static fn($v) => ($v ?? '') !== '' ? (float) $v : null;

        $barrio  = trim($_POST['barrio'] ?? '') ?: null;
        $numExt  = trim($_POST['numero_exterior'] ?? '') ?: null;

        // 1) Coordenadas confirmadas por el usuario en el mapa (pin). Tienen prioridad.
        // 2) Si no llegan, se geocodifica en el servidor (fuera de la transacción).
        // Si todo falla, quedan en null y la publicación continúa igual.
        $coords = self::coordsConfirmadas($_POST['latitud'] ?? '', $_POST['longitud'] ?? '')
            ?? self::geocodificarDireccion($ciudadId, $calle, $numExt, $barrio);

        $pdo = Database::conexion();
        $pdo->beginTransaction();
        try {
            // 1) Crear la dirección
            $direccionId = Direccion::crear([
                'ciudad_id'       => $ciudadId,
                'calle'           => $calle,
                'numero_exterior' => $numExt,
                'barrio'          => $barrio,
                'codigo_postal'   => trim($_POST['codigo_postal'] ?? '') ?: null,
                'referencia'      => trim($_POST['referencia'] ?? '') ?: null,
                'latitud'         => $coords['lat'] ?? null,
                'longitud'        => $coords['lon'] ?? null,
            ]);

            // 2) Crear la propiedad ligada a esa dirección
            $propiedadId = Propiedad::crear([
                'propietario_id'          => (int) $_SESSION['usuario_id'],
                'direccion_id'            => $direccionId,
                'titulo'                  => $titulo,
                'descripcion'             => trim($_POST['descripcion'] ?? '') ?: null,
                'tipo_propiedad'          => $tipo,
                'num_habitaciones'        => $numEntero($_POST['num_habitaciones'] ?? ''),
                'num_banos'               => $numEntero($_POST['num_banos'] ?? ''),
                'area_m2'                 => $numFloat($_POST['area_m2'] ?? ''),
                'precio_alquiler_mensual' => (float) $precio,
                'deposito'                => $numFloat($_POST['deposito'] ?? ''),
                'disponible'              => isset($_POST['disponible']) ? 1 : 0,
                'amueblado'               => isset($_POST['amueblado']) ? 1 : 0,
                'mascotas_permitidas'     => isset($_POST['mascotas_permitidas']) ? 1 : 0,
                'clausulas_contrato'      => trim($_POST['clausulas_contrato'] ?? '') ?: null,
            ]);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            view('propiedades', [
                'title'       => 'Publicar Propiedad | miarriendo.online',
                'error'       => 'No se pudo guardar la propiedad. Intenta de nuevo.',
                'ubicaciones' => Ubicacion::paraFormulario(),
            ]);
            return;
        }

        // Las imágenes se procesan fuera de la transacción (mover archivos no es transaccional)
        $this->procesarImagenes($propiedadId);

        flash('publicado', $titulo);
        redirect('/propiedades/exito');
    }

    /** Pantalla de confirmación tras publicar una propiedad. */
    public function exito(): void
    {
        requiereLogin();
        $titulo = flash('publicado');
        if ($titulo === null) {
            // Acceso directo sin haber publicado: a la lista de propiedades.
            redirect('/panel?ver=mis-propiedades');
        }
        view('publicado', [
            'title'  => 'Propiedad publicada | miarriendo.online',
            'titulo' => $titulo,
        ]);
    }

    /**
     * Guarda las imágenes subidas (input name="imagenes[]") en disco y BD.
     * La primera imagen válida se marca como principal.
     */
    private function procesarImagenes(int $propiedadId): void
    {
        if (empty($_FILES['imagenes']) || !is_array($_FILES['imagenes']['name'])) {
            return;
        }

        $dir = BASE_PATH . '/public/uploads/propiedades';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // Extensión válida según el tipo real de imagen detectado
        $extensiones = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_WEBP => 'webp',
            IMAGETYPE_GIF  => 'gif',
        ];
        $maxBytes = 3 * 1024 * 1024; // 3 MB

        // Continúa la numeración por si la propiedad ya tenía fotos.
        $orden = ImagenPropiedad::maxOrden($propiedadId) + 1;
        $total = count($_FILES['imagenes']['name']);

        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['imagenes']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            if ($_FILES['imagenes']['size'][$i] > $maxBytes) {
                continue;
            }

            $tmp  = $_FILES['imagenes']['tmp_name'][$i];
            $info = @getimagesize($tmp); // verifica que sea una imagen real
            if ($info === false || !isset($extensiones[$info[2]])) {
                continue;
            }

            $ext     = $extensiones[$info[2]];
            $nombre  = uniqid('prop' . $propiedadId . '_', true) . '.' . $ext;
            $destino = $dir . '/' . $nombre;

            if (!move_uploaded_file($tmp, $destino)) {
                continue;
            }

            ImagenPropiedad::crear([
                'propiedad_id' => $propiedadId,
                'url_imagen'   => '/uploads/propiedades/' . $nombre,
                'descripcion'  => null,
                'orden'        => $orden,
                'es_principal' => 0,
            ]);
            $orden++;
        }

        // Garantiza que siempre haya una portada (la de menor orden).
        ImagenPropiedad::asegurarPrincipal($propiedadId);
    }

    /**
     * Valida las coordenadas que el usuario confirmó en el mapa (inputs ocultos).
     * Devuelve null si faltan o están fuera de rango.
     * @return array{lat:float,lon:float}|null
     */
    private static function coordsConfirmadas(string $lat, string $lon): ?array
    {
        if ($lat === '' || $lon === '' || !is_numeric($lat) || !is_numeric($lon)) {
            return null;
        }
        $lat = (float) $lat;
        $lon = (float) $lon;
        if ($lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
            return null;
        }
        return ['lat' => $lat, 'lon' => $lon];
    }

    /**
     * Arma el texto de la dirección y obtiene sus coordenadas con Nominatim.
     * @return array{lat:float,lon:float}|null
     */
    private static function geocodificarDireccion(int $ciudadId, string $calle, ?string $numExt, ?string $barrio): ?array
    {
        $ubic = Ubicacion::nombresPorCiudad($ciudadId);
        if ($ubic === null) {
            return null;
        }

        $partes = array_filter([
            trim($calle . ' ' . ($numExt ?? '')),
            $barrio,
            $ubic['ciudad'],
            $ubic['departamento'],
            'Colombia',
        ]);

        return Geocoder::geocodificar(implode(', ', $partes));
    }

    /** Detalle de una propiedad. */
    public function detalle(string $id): void
    {
        $propiedad = Propiedad::buscarPorId((int) $id);

        if ($propiedad === null || (int) $propiedad['archivada'] === 1) {
            http_response_code(404);
            view('404', ['title' => 'Propiedad no encontrada | 404']);
            return;
        }

        // Vista previa del contrato (plantilla + cláusulas del propietario)
        $direccion = trim($propiedad['calle'] . ' ' . ($propiedad['numero_exterior'] ?? ''))
            . (!empty($propiedad['barrio']) ? ', ' . $propiedad['barrio'] : '');
        $clausulas = ContratoPlantilla::clausulas([
            'arrendador' => trim($propiedad['propietario_nombre'] . ' ' . $propiedad['propietario_apellidos']),
            'inmueble'   => $direccion,
            'ciudad'     => $propiedad['ciudad'],
            'tipo'       => $propiedad['tipo_propiedad'],
            'precio'     => $propiedad['precio_alquiler_mensual'],
            'deposito'   => $propiedad['deposito'],
            'mascotas'   => $propiedad['mascotas_permitidas'],
            'amueblado'  => $propiedad['amueblado'],
            'extra'      => $propiedad['clausulas_contrato'] ?? '',
        ]);

        view('propiedad', [
            'title'     => $propiedad['titulo'] . ' | miarriendo.online',
            'propiedad' => $propiedad,
            'imagenes'  => ImagenPropiedad::porPropiedad((int) $id),
            'clausulas' => $clausulas,
            'exito'     => flash('propiedad_ok'),
        ]);
    }

    /** Formulario de edición de una propiedad (solo el dueño). */
    public function editar(string $id): void
    {
        $propiedad = $this->soloDueno((int) $id);
        if ($propiedad === null) {
            return;
        }
        view('editar_propiedad', [
            'title'     => 'Editar propiedad | miarriendo.online',
            'propiedad' => $propiedad,
            'imagenes'  => ImagenPropiedad::porPropiedad((int) $id),
            'exito'     => flash('propiedad_ok'),
        ]);
    }

    /** Procesa la edición de una propiedad. */
    public function actualizar(string $id): void
    {
        $propiedad = $this->soloDueno((int) $id);
        if ($propiedad === null) {
            return;
        }

        $titulo = trim($_POST['titulo'] ?? '');
        $tipo   = trim($_POST['tipo_propiedad'] ?? '');
        $precio = $_POST['precio_alquiler_mensual'] ?? '';
        $calle  = trim($_POST['calle'] ?? '');

        $error = null;
        if ($titulo === '' || $tipo === '' || $precio === '' || $calle === '') {
            $error = 'Completa los campos obligatorios (título, tipo, precio y dirección).';
        } elseif (!is_numeric($precio) || (float) $precio <= 0) {
            $error = 'El precio mensual debe ser un número mayor a 0.';
        }

        if ($error !== null) {
            view('editar_propiedad', [
                'title'     => 'Editar propiedad | miarriendo.online',
                'propiedad' => array_merge($propiedad, $_POST),
                'error'     => $error,
            ]);
            return;
        }

        $numEntero = static fn($v) => ($v ?? '') !== '' ? (int) $v : null;
        $numFloat  = static fn($v) => ($v ?? '') !== '' ? (float) $v : null;
        $barrio = trim($_POST['barrio'] ?? '') ?: null;
        $numExt = trim($_POST['numero_exterior'] ?? '') ?: null;

        // Re-geocodifica con la ciudad actual (la dirección pudo cambiar)
        $coords = self::geocodificarDireccion((int) $propiedad['ciudad_id'], $calle, $numExt, $barrio);

        $pdo = Database::conexion();
        $pdo->beginTransaction();
        try {
            Direccion::actualizar((int) $propiedad['direccion_id'], [
                'calle'           => $calle,
                'numero_exterior' => $numExt,
                'barrio'          => $barrio,
                'codigo_postal'   => trim($_POST['codigo_postal'] ?? '') ?: null,
                'referencia'      => trim($_POST['referencia'] ?? '') ?: null,
                'latitud'         => $coords['lat'] ?? $propiedad['latitud'],
                'longitud'        => $coords['lon'] ?? $propiedad['longitud'],
            ]);
            Propiedad::actualizar((int) $id, [
                'titulo'                  => $titulo,
                'descripcion'             => trim($_POST['descripcion'] ?? '') ?: null,
                'tipo_propiedad'          => $tipo,
                'num_habitaciones'        => $numEntero($_POST['num_habitaciones'] ?? ''),
                'num_banos'               => $numEntero($_POST['num_banos'] ?? ''),
                'area_m2'                 => $numFloat($_POST['area_m2'] ?? ''),
                'precio_alquiler_mensual' => (float) $precio,
                'deposito'                => $numFloat($_POST['deposito'] ?? ''),
                'disponible'              => isset($_POST['disponible']) ? 1 : 0,
                'amueblado'               => isset($_POST['amueblado']) ? 1 : 0,
                'mascotas_permitidas'     => isset($_POST['mascotas_permitidas']) ? 1 : 0,
                'clausulas_contrato'      => trim($_POST['clausulas_contrato'] ?? '') ?: null,
            ]);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            view('editar_propiedad', [
                'title'     => 'Editar propiedad | miarriendo.online',
                'propiedad' => array_merge($propiedad, $_POST),
                'error'     => 'No se pudieron guardar los cambios. Intenta de nuevo.',
            ]);
            return;
        }

        flash('propiedad_ok', 'Los cambios se guardaron correctamente.');
        redirect('/propiedad/' . (int) $id);
    }

    /** Archiva (borrado lógico) una propiedad del dueño. */
    public function eliminar(string $id): void
    {
        $propiedad = $this->soloDueno((int) $id);
        if ($propiedad === null) {
            return;
        }
        Propiedad::archivar((int) $id);
        flash('panel_ok', 'La propiedad "' . $propiedad['titulo'] . '" fue eliminada.');
        redirect('/panel?ver=mis-propiedades');
    }

    /** Añade fotos a una propiedad existente (solo el dueño). */
    public function agregarFotos(string $id): void
    {
        $propiedad = $this->soloDueno((int) $id);
        if ($propiedad === null) {
            return;
        }
        $this->procesarImagenes((int) $id);
        flash('propiedad_ok', 'Fotos actualizadas.');
        redirect('/propiedad/' . (int) $id . '/editar');
    }

    /** Elimina una foto de una propiedad (solo el dueño). */
    public function eliminarFoto(string $id, string $imgId): void
    {
        $propiedad = $this->soloDueno((int) $id);
        if ($propiedad === null) {
            return;
        }

        $img = ImagenPropiedad::buscarPorId((int) $imgId);
        if ($img !== null && (int) $img['propiedad_id'] === (int) $id) {
            // Borra el archivo del disco (si existe) y el registro
            $ruta = BASE_PATH . '/public' . $img['url_imagen'];
            if (is_file($ruta)) {
                @unlink($ruta);
            }
            ImagenPropiedad::eliminar((int) $imgId);
            ImagenPropiedad::asegurarPrincipal((int) $id); // re-asigna portada si hacía falta
        }

        redirect('/propiedad/' . (int) $id . '/editar');
    }

    /**
     * Carga la propiedad y verifica que el usuario en sesión sea su dueño.
     * Devuelve la propiedad (con ciudad_id_actual añadido) o null (403/404 ya respondido).
     */
    private function soloDueno(int $id): ?array
    {
        requiereLogin();
        $propiedad = Propiedad::buscarPorId($id);

        if ($propiedad === null || (int) $propiedad['archivada'] === 1) {
            http_response_code(404);
            view('404', ['title' => 'Propiedad no encontrada | 404']);
            return null;
        }
        if ((int) $_SESSION['usuario_id'] !== (int) $propiedad['propietario_id']) {
            http_response_code(403);
            view('error', ['title' => 'Sin acceso | 403', 'codigo' => '403', 'mensaje' => 'No puedes gestionar esta propiedad.']);
            return null;
        }
        return $propiedad;
    }

    /** Formulario para solicitar el arriendo de una propiedad (Fase 2). */
    public function solicitar(string $id): void
    {
        requiereLogin();
        $propiedad = Propiedad::buscarPorId((int) $id);

        if ($propiedad === null) {
            http_response_code(404);
            view('404', ['title' => 'Propiedad no encontrada | 404']);
            return;
        }

        // El dueño no puede arrendarse a sí mismo
        if ((int) $_SESSION['usuario_id'] === (int) $propiedad['propietario_id']) {
            redirect('/propiedad/' . (int) $id);
        }

        view('solicitar_arriendo', [
            'title'     => 'Solicitar arriendo | miarriendo.online',
            'propiedad' => $propiedad,
        ]);
    }

    /** Vista del mapa con la ruta hacia una propiedad. */
    public function ruta(): void
    {
        // El mapa usa OpenStreetMap (Leaflet), no requiere API key.
        $id = (int) ($_GET['id'] ?? 0);
        $propiedad = $id > 0 ? Propiedad::buscarPorId($id) : null;

        $destino = null;
        if ($propiedad !== null) {
            $calle = trim($propiedad['calle'] . ' ' . ($propiedad['numero_exterior'] ?? ''));
            $destino = [
                'texto' => $calle
                    . (!empty($propiedad['barrio']) ? ', ' . $propiedad['barrio'] : '')
                    . ', ' . $propiedad['ciudad'] . ', ' . $propiedad['departamento'],
                'lat'    => $propiedad['latitud']  !== null ? (float) $propiedad['latitud']  : null,
                'lon'    => $propiedad['longitud'] !== null ? (float) $propiedad['longitud'] : null,
                'titulo' => $propiedad['titulo'],
            ];
        }

        view('mapa', [
            'title'   => '¿Cómo llegar? | miarriendo.online',
            'destino' => $destino,
        ]);
    }
}
