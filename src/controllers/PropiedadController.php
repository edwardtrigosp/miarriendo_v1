<?php

/**
 * Controlador de propiedades (listar, publicar, ver ruta).
 */
class PropiedadController
{
    /** Listado de arriendos disponibles (desde la base de datos). */
    public function index(): void
    {
        $filas = Propiedad::listarDisponibles();

        // Adaptar los datos a lo que espera la vista
        $propiedades = array_map(static function (array $p): array {
            $direccion = trim($p['calle'] . ' ' . ($p['numero_exterior'] ?? '')) . ', ' . $p['ciudad'];
            return [
                'id'        => $p['propiedad_id'],
                'titulo'    => $p['titulo'],
                'precio'    => $p['precio_alquiler_mensual'],
                'direccion' => $direccion,
                'imagen'    => $p['imagen'] ?: 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=500&q=80',
                'estado'    => 'Disponible',
            ];
        }, $filas);

        view('arriendos', [
            'title'       => 'Explorar Arriendos | miarriendo.online',
            'propiedades' => $propiedades,
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

        $pdo = Database::conexion();
        $pdo->beginTransaction();
        try {
            // 1) Crear la dirección
            $direccionId = Direccion::crear([
                'ciudad_id'       => $ciudadId,
                'calle'           => $calle,
                'numero_exterior' => trim($_POST['numero_exterior'] ?? '') ?: null,
                'barrio'          => trim($_POST['barrio'] ?? '') ?: null,
                'codigo_postal'   => trim($_POST['codigo_postal'] ?? '') ?: null,
                'referencia'      => trim($_POST['referencia'] ?? '') ?: null,
            ]);

            // 2) Crear la propiedad ligada a esa dirección
            Propiedad::crear([
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

        redirect('/arriendos');
    }

    /** Vista del mapa con la ruta hacia una propiedad. */
    public function ruta(): void
    {
        // El mapa usa OpenStreetMap (Leaflet), no requiere API key.
        view('mapa', ['title' => '¿Cómo llegar? | miarriendo.online']);
    }
}
