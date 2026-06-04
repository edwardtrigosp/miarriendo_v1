// Ruta hacia la propiedad con OpenStreetMap (Leaflet) + OSRM + Nominatim.
// 100% gratis: no requiere API key ni facturación.

let map;
let rutaLayer = null;       // capa de la línea de ruta
let origenMarker = null;
let destinoMarker = null;
let origenCoords = null;    // { lat, lon } del punto de partida
let destinoFijo = null;     // { lat, lon } de la propiedad (si vino del backend)
let modoClicMapa = false;   // true mientras se espera un clic en el mapa
let debounceId = null;      // temporizador para el autocompletado

// Mapea nuestros modos al perfil de OSRM
var PERFIL_OSRM = { DRIVING: 'driving', WALKING: 'walking', BICYCLING: 'cycling', TRANSIT: 'driving' };

document.addEventListener('DOMContentLoaded', function () {
    if (typeof L === 'undefined' || !document.getElementById('map_canvas')) {
        return;
    }
    iniciarMapa();
    conectarControles();
    prepararDestino();
});

// ------------------------------------------------------------------
// Destino precargado desde la propiedad (window.RUTA_DESTINO)
// ------------------------------------------------------------------
function prepararDestino() {
    if (typeof window.RUTA_DESTINO === 'undefined' || !window.RUTA_DESTINO) {
        return;
    }
    var d = window.RUTA_DESTINO;

    // Si la propiedad tiene coordenadas guardadas, las usamos directamente
    if (typeof d.lat === 'number' && typeof d.lon === 'number') {
        destinoFijo = { lat: d.lat, lon: d.lon };
        marcarDestino(destinoFijo, d.titulo || 'Propiedad');
        map.setView([d.lat, d.lon], 15);
        return;
    }

    // Si no, geocodificamos el texto para centrar y marcar el destino
    if (d.texto) {
        geocode(d.texto).then(function (coords) {
            if (coords) {
                destinoFijo = coords;
                marcarDestino(coords, d.titulo || 'Propiedad');
                map.setView([coords.lat, coords.lon], 15);
            }
        });
    }
}

function marcarDestino(coords, titulo) {
    if (destinoMarker) { map.removeLayer(destinoMarker); }
    destinoMarker = L.circleMarker([coords.lat, coords.lon], {
        radius: 9, color: '#8917D4', fillColor: '#ffffff', fillOpacity: 1, weight: 3
    }).addTo(map).bindPopup(titulo).openPopup();
}

// ------------------------------------------------------------------
// Mapa base
// ------------------------------------------------------------------
function iniciarMapa() {
    map = L.map('map_canvas', { zoomControl: true }).setView([4.6097, -74.0817], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; colaboradores de OpenStreetMap'
    }).addTo(map);

    // Clic en el mapa para fijar el punto de partida
    map.on('click', function (e) {
        if (modoClicMapa) {
            fijarPorClic(e.latlng);
        }
    });
}

// ------------------------------------------------------------------
// Controles del panel
// ------------------------------------------------------------------
function conectarControles() {
    var origen = document.getElementById('origen');
    var form = document.getElementById('ruta_form');

    origen.addEventListener('focus', function () { mostrarDropdown(true); });
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.ruta_origin_group')) {
            mostrarDropdown(false);
        }
    });

    // Al escribir: olvida coordenadas previas y busca sugerencias (con debounce)
    origen.addEventListener('input', function () {
        origenCoords = null;
        clearTimeout(debounceId);
        var texto = origen.value;
        debounceId = setTimeout(function () { buscarSugerencias(texto); }, 350);
    });

    document.getElementById('opt_gps').addEventListener('click', function () {
        usarGPS();
        mostrarDropdown(false);
    });
    document.getElementById('opt_map').addEventListener('click', function () {
        activarClicMapa();
        mostrarDropdown(false);
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        calcularRuta();
    });
}

function mostrarDropdown(ver) {
    var d = document.getElementById('origen_dropdown');
    if (d) { d.hidden = !ver; }
}

// ------------------------------------------------------------------
// Autocompletado de direcciones (Nominatim)
// ------------------------------------------------------------------
function buscarSugerencias(texto) {
    var lista = document.getElementById('origen_suggestions');
    lista.innerHTML = '';
    if (texto.trim().length < 3) {
        return;
    }

    var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=5&countrycodes=co&q='
        + encodeURIComponent(texto);

    fetch(url, { headers: { 'Accept-Language': 'es' } })
        .then(function (r) { return r.json(); })
        .then(function (resultados) {
            lista.innerHTML = '';
            resultados.forEach(function (lugar) {
                var partes = lugar.display_name.split(',');
                var principal = partes.shift().trim();
                var secundario = partes.join(',').trim();

                var li = document.createElement('li');
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'ruta_suggestion';
                btn.innerHTML =
                    '<span class="ruta_suggestion_icon"><span class="material-symbols-outlined">location_on</span></span>' +
                    '<span class="ruta_suggestion_text">' +
                        '<strong>' + principal + '</strong>' +
                        '<small>' + secundario + '</small>' +
                    '</span>';
                btn.addEventListener('click', function () {
                    document.getElementById('origen').value = lugar.display_name;
                    origenCoords = { lat: parseFloat(lugar.lat), lon: parseFloat(lugar.lon) };
                    mostrarDropdown(false);
                });
                li.appendChild(btn);
                lista.appendChild(li);
            });
        })
        .catch(function () { /* silencioso: sin sugerencias */ });
}

// ------------------------------------------------------------------
// Opción "Permitir acceso a ubicación" (GPS del dispositivo)
// ------------------------------------------------------------------
var ubicacionMarker = null;

function usarGPS() {
    var origen = document.getElementById('origen');
    var estado = document.getElementById('ruta_estado');

    if (!navigator.geolocation) {
        estado.textContent = 'Tu navegador no soporta geolocalización.';
        return;
    }

    // La geolocalización solo funciona en contexto seguro (HTTPS o localhost)
    if (window.isSecureContext === false) {
        estado.textContent = 'La ubicación requiere HTTPS. Abre el sitio como https:// o usa localhost.';
        return;
    }

    origen.value = 'Obteniendo tu ubicación…';
    estado.textContent = 'Solicitando permiso de ubicación…';

    navigator.geolocation.getCurrentPosition(
        function (pos) {
            origenCoords = { lat: pos.coords.latitude, lon: pos.coords.longitude };
            estado.textContent = '';

            // Centra el mapa y marca la ubicación del dispositivo
            map.setView([origenCoords.lat, origenCoords.lon], 16);
            if (ubicacionMarker) { map.removeLayer(ubicacionMarker); }
            ubicacionMarker = L.circleMarker([origenCoords.lat, origenCoords.lon], {
                radius: 9, color: '#8917D4', fillColor: '#8917D4', fillOpacity: 0.9, weight: 3
            }).addTo(map).bindPopup('Estás aquí').openPopup();

            // Muestra la dirección legible del punto
            origen.value = 'Obteniendo dirección…';
            reverseGeocode(origenCoords, function (nombre) {
                origen.value = nombre || 'Tu ubicación actual (GPS)';
            });
        },
        function (err) {
            origen.value = '';
            origenCoords = null;

            // Mensaje específico según el tipo de error
            switch (err.code) {
                case err.PERMISSION_DENIED:
                    estado.textContent = 'Bloqueaste el permiso de ubicación. Actívalo en el icono 🔒 de la barra del navegador.';
                    break;
                case err.POSITION_UNAVAILABLE:
                    estado.textContent = 'No se pudo determinar tu ubicación. Revisa el GPS o la conexión.';
                    break;
                case err.TIMEOUT:
                    estado.textContent = 'La ubicación tardó demasiado. Intenta de nuevo.';
                    break;
                default:
                    estado.textContent = 'No pudimos acceder a tu ubicación. Escribe el punto de partida.';
            }
        },
        {
            enableHighAccuracy: true, // usa GPS del dispositivo si está disponible
            timeout: 10000,           // espera máx. 10 s
            maximumAge: 0             // no usar una ubicación cacheada
        }
    );
}

// ------------------------------------------------------------------
// Opción "Fijar ubicación en el mapa"
// ------------------------------------------------------------------
function activarClicMapa() {
    modoClicMapa = true;
    document.getElementById('ruta_estado').textContent = 'Haz clic en el mapa para fijar tu punto de partida.';
}

function fijarPorClic(latlng) {
    origenCoords = { lat: latlng.lat, lon: latlng.lng };
    modoClicMapa = false;
    document.getElementById('ruta_estado').textContent = '';
    reverseGeocode(origenCoords, function (nombre) {
        document.getElementById('origen').value = nombre || 'Punto seleccionado en el mapa';
    });
}

// Convierte coordenadas en una dirección legible (Nominatim)
function reverseGeocode(coords, callback) {
    var url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + coords.lat + '&lon=' + coords.lon;
    fetch(url, { headers: { 'Accept-Language': 'es' } })
        .then(function (r) { return r.json(); })
        .then(function (data) { callback(data && data.display_name ? data.display_name : null); })
        .catch(function () { callback(null); });
}

// Geocodifica un texto a coordenadas (Nominatim). Devuelve una promesa.
function geocode(texto) {
    var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=co&q='
        + encodeURIComponent(texto);
    return fetch(url, { headers: { 'Accept-Language': 'es' } })
        .then(function (r) { return r.json(); })
        .then(function (a) {
            return (a && a[0]) ? { lat: parseFloat(a[0].lat), lon: parseFloat(a[0].lon) } : null;
        });
}

// ------------------------------------------------------------------
// Calcular y dibujar la ruta (OSRM)
// ------------------------------------------------------------------
function calcularRuta() {
    var estado  = document.getElementById('ruta_estado');
    var destino = document.getElementById('destino').value.trim();
    var modo    = document.getElementById('modo_viaje').value;
    var origenTexto = document.getElementById('origen').value.trim();

    if (!destino) {
        estado.textContent = 'Indica el destino.';
        return;
    }
    if (!origenCoords && !origenTexto) {
        estado.textContent = 'Indica un punto de partida o usa tu ubicación.';
        return;
    }

    estado.textContent = 'Calculando ruta…';

    // Resuelve coordenadas de origen (si solo hay texto) y de destino, luego ruta.
    // Si el destino vino fijado por la propiedad, usamos esas coordenadas directamente.
    var promesaOrigen  = origenCoords ? Promise.resolve(origenCoords) : geocode(origenTexto);
    var promesaDestino = destinoFijo  ? Promise.resolve(destinoFijo)  : geocode(destino);

    Promise.all([promesaOrigen, promesaDestino])
        .then(function (puntos) {
            var o = puntos[0];
            var d = puntos[1];
            if (!o) { estado.textContent = 'No encontramos el punto de partida.'; return; }
            if (!d) { estado.textContent = 'No encontramos el destino.'; return; }
            pedirRuta(o, d, PERFIL_OSRM[modo] || 'driving');
        })
        .catch(function () { estado.textContent = 'Error al buscar las direcciones.'; });
}

function pedirRuta(o, d, perfil) {
    var estado = document.getElementById('ruta_estado');
    var url = 'https://router.project-osrm.org/route/v1/' + perfil + '/'
        + o.lon + ',' + o.lat + ';' + d.lon + ',' + d.lat
        + '?overview=full&geometries=geojson';

    fetch(url)
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.routes || !data.routes.length) {
                estado.textContent = 'No se pudo calcular la ruta.';
                return;
            }
            var ruta = data.routes[0];
            dibujarRuta(ruta.geometry, o, d);

            document.getElementById('ruta_tiempo').textContent = formatearDuracion(ruta.duration);
            document.getElementById('ruta_distancia').textContent = (ruta.distance / 1000).toFixed(1) + ' km';

            // Enlaces para navegar con apps externas (usan el destino)
            document.getElementById('btn_waze').href =
                'https://www.waze.com/ul?ll=' + d.lat + ',' + d.lon + '&navigate=yes';
            document.getElementById('btn_gmaps').href =
                'https://www.google.com/maps/dir/?api=1&destination=' + d.lat + ',' + d.lon;

            document.getElementById('ruta_resultado').hidden = false;
            estado.textContent = '';
        })
        .catch(function () { estado.textContent = 'Error de conexión al calcular la ruta.'; });
}

function dibujarRuta(geometry, o, d) {
    // Limpia ruta y marcadores anteriores
    if (rutaLayer)   { map.removeLayer(rutaLayer); }
    if (origenMarker)  { map.removeLayer(origenMarker); }
    if (destinoMarker) { map.removeLayer(destinoMarker); }

    rutaLayer = L.geoJSON(geometry, {
        style: { color: '#8917D4', weight: 6, opacity: 0.85 }
    }).addTo(map);

    origenMarker = L.circleMarker([o.lat, o.lon], {
        radius: 8, color: '#8917D4', fillColor: '#8917D4', fillOpacity: 1
    }).addTo(map).bindPopup('Punto de partida');

    destinoMarker = L.circleMarker([d.lat, d.lon], {
        radius: 8, color: '#8917D4', fillColor: '#ffffff', fillOpacity: 1, weight: 3
    }).addTo(map).bindPopup('Destino');

    map.fitBounds(rutaLayer.getBounds(), { padding: [50, 50] });
}

// Convierte segundos en un texto tipo "1 h 12 min" o "25 min"
function formatearDuracion(segundos) {
    var min = Math.round(segundos / 60);
    if (min < 60) {
        return min + ' min';
    }
    var h = Math.floor(min / 60);
    var m = min % 60;
    return h + ' h ' + (m > 0 ? m + ' min' : '');
}
