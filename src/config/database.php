<?php

/**
 * Conexión a la base de datos (PDO, patrón singleton).
 *
 * Lee la configuración del .env mediante el helper env().
 * Uso:
 *   $pdo = Database::conexion();
 *   $stmt = $pdo->query('SELECT * FROM usuarios');
 */
class Database
{
    private static ?PDO $pdo = null;

    /** Devuelve una única instancia de PDO (la reutiliza en toda la petición). */
    public static function conexion(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $host = env('DB_HOST', 'db');
        $port = env('DB_PORT', '3306');
        $name = env('DB_NAME', 'miarriendo');
        $user = env('DB_USER', 'root');
        $pass = env('DB_PASSWORD', '');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // lanza excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // arrays asociativos
                PDO::ATTR_EMULATE_PREPARES   => false,                    // prepares reales (más seguro)
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Error de conexión a la base de datos: ' . $e->getMessage());
        }

        return self::$pdo;
    }
}
