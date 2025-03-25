# Separador de Sílabas en Español

Este script PHP proporciona una función para separar palabras en español en sus sílabas componentes. Además, incluye una función de prueba para verificar la precisión de la separación silábica en una lista de palabras predefinida.

## Funcionalidades

-   **`silabas($word, $sep = '-')`**:
    -   Toma una palabra en español como entrada y devuelve una cadena con las sílabas separadas por el separador especificado (por defecto, '-').
    -   Maneja diptongos, triptongos y hiatos según las reglas de la división silábica del español.
    -   Incluye soporte para caracteres acentuados y caracteres especiales como 'ch', 'll' y 'rr'.
    -   Lanza una excepción si encuentra un carácter no reconocido.
-   **`comprobarSilabas($array_palabras)`**:
    -   Toma un array asociativo donde las claves son palabras y los valores son las separaciones silábicas esperadas.
    -   Compara la salida de la función `silabas()` con los valores esperados y devuelve un array con los resultados de la comparación.

## Uso

1.  Clona el repositorio o descarga el archivo `separa_silabas.php`.
2.  Incluye el archivo `separa_silabas.php` en tu script PHP.
3.  Llama a la función `silabas()` con la palabra que deseas separar en sílabas.

    ```php
    <?php
    require_once 'separa_silabas.php';

    $palabra = "electroencefalografiquísimamente";
    $silabas = silabas($palabra);
    echo $silabas; // Salida: e-lec-tro-en-ce-fa-lo-gra-fi-quí-si-ma-men-te
    ?>
    ```

4.  (Opcional) Utiliza la función `comprobarSilabas()` para verificar la precisión de la separación silábica en una lista de palabras.

    ```php
    <?php
    require_once 'separa_silabas.php';

    $palabras = [
        "instituto" => "ins-ti-tu-to",
        "reunir" => "reu-nir",
        // ... más palabras
    ];

    $resultados = comprobarSilabas($palabras);

    foreach ($resultados as $palabra => $resultado) {
        echo "$palabra - $resultado\n";
    }
    ?>
    ```

## Requisitos

-   PHP 7.0 o superior (debido al uso de `mb_strtolower` con el juego de caracteres UTF-8).

## Contribución

¡Las contribuciones son bienvenidas! Si encuentras algún error o tienes sugerencias de mejora, por favor, abre un issue o envía un pull request.

## Licencia

Este proyecto está bajo la Licencia Apache 2.0. Consulta el archivo `LICENSE` para más detalles.
