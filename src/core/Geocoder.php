<?php

/**
 * Geocodificador del lado del servidor usando Nominatim (OpenStreetMap).
 * 100% gratis, sin API key. Convierte una dirección de texto en coordenadas.
 *
 * Política de uso de Nominatim: máx. 1 petición/seg y un User-Agent válido.
 * Si algo falla (sin internet, sin resultados, timeout), devuelve null y la
 * publicación continúa: las coordenadas quedan vacías (no es un error fatal).
 */
class Geocoder
{
    private const ENDPOINT   = 'https://nominatim.openstreetmap.org/search';
    private const USER_AGENT = 'miarriendo.online/1.0 (edwardtrigosp@gmail.com)';
    private const TIMEOUT    = 8; // segundos

    /**
     * Geocodifica una dirección dentro de Colombia.
     *
     * @return array{lat:float,lon:float}|null
     */
    public static function geocodificar(string $direccion): ?array
    {
        $direccion = trim($direccion);
        if ($direccion === '') {
            return null;
        }

        $url = self::ENDPOINT . '?' . http_build_query([
            'format'       => 'json',
            'limit'        => 1,
            'countrycodes' => 'co',
            'q'            => $direccion,
        ]);

        $respuesta = self::pedir($url);
        if ($respuesta === null) {
            return null;
        }

        $datos = json_decode($respuesta, true);
        if (!is_array($datos) || empty($datos[0]['lat']) || empty($datos[0]['lon'])) {
            return null;
        }

        return [
            'lat' => (float) $datos[0]['lat'],
            'lon' => (float) $datos[0]['lon'],
        ];
    }

    /** Hace la petición HTTP (cURL si está disponible, si no file_get_contents). */
    private static function pedir(string $url): ?string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT      => self::USER_AGENT,
                CURLOPT_HTTPHEADER     => ['Accept-Language: es'],
                CURLOPT_TIMEOUT        => self::TIMEOUT,
                CURLOPT_FOLLOWLOCATION => true,
            ]);
            $cuerpo = curl_exec($ch);
            $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return ($cuerpo !== false && $codigo === 200) ? $cuerpo : null;
        }

        $contexto = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => "User-Agent: " . self::USER_AGENT . "\r\nAccept-Language: es\r\n",
                'timeout' => self::TIMEOUT,
            ],
        ]);
        $cuerpo = @file_get_contents($url, false, $contexto);
        return $cuerpo !== false ? $cuerpo : null;
    }
}
