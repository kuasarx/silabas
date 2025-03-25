<?php

function silabas($word, $sep = '-') {
    // Convierte la palabra a minúsculas
    $word = mb_strtolower($word, 'UTF-8');
    
    // Definición de categorías de letras
    $l = ['r', 'l'];
    $o = ['p', 'b', 'f', 't', 'd', 'c', 'k', 'g'];
    $c = ['b', 'c', 'ch', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'll', 'm', 'n', 'ñ', 'p', 'q', 'r', 'rr', 's', 't', 'v', 'x', 'y', 'z'];
    $a = ['a', 'e', 'o', 'á', 'é', 'ó', 'í', 'ú'];
    $i = ['i', 'u', 'ü'];
    
    // Inicialización de variables
    $letras = [];
    $estructura = '';
    $j = 0;
    
    // Primera parte: Construcción de letras y estructura
    while ($j < mb_strlen($word, 'UTF-8')) {
        if ($j == 0) {
            if (mb_substr($word, $j, 1, 'UTF-8') == 'p' && mb_substr($word, $j + 1, 1, 'UTF-8') == 's') {
                $letras[] = 'ps';
                $estructura .= 'C';
                $j += 2;
                continue;
            } elseif (mb_substr($word, $j, 1, 'UTF-8') == 'p' && mb_substr($word, $j + 1, 1, 'UTF-8') == 'n') {
                $letras[] = 'pn';
                $estructura .= 'C';
                $j += 2;
                continue;
            } elseif (mb_substr($word, $j, 1, 'UTF-8') == 'p' && mb_substr($word, $j + 1, 1, 'UTF-8') == 't') {
                $letras[] = 'pt';
                $estructura .= 'C';
                $j += 2;
                continue;
            } elseif (mb_substr($word, $j, 1, 'UTF-8') == 'g' && mb_substr($word, $j + 1, 1, 'UTF-8') == 'n') {
                $letras[] = 'gn';
                $estructura .= 'C';
                $j += 2;
                continue;
            }
        }
        if ($j < mb_strlen($word, 'UTF-8') - 1) {
            if (mb_substr($word, $j, 1, 'UTF-8') == 'c' && mb_substr($word, $j + 1, 1, 'UTF-8') == 'h') {
                $letras[] = 'ch';
                $estructura .= 'C';
                $j += 2;
                continue;
            } elseif (mb_substr($word, $j, 1, 'UTF-8') == 'l' && mb_substr($word, $j + 1, 1, 'UTF-8') == 'l') {
                $letras[] = 'll';
                $estructura .= 'C';
                $j += 2;
                continue;
            } elseif (mb_substr($word, $j, 1, 'UTF-8') == 'r' && mb_substr($word, $j + 1, 1, 'UTF-8') == 'r') {
                $letras[] = 'rr';
                $estructura .= 'C';
                $j += 2;
                continue;
            }
        }
        $char = mb_substr($word, $j, 1, 'UTF-8');
        if (in_array($char, $a)) {
            $letras[] = $char;
            $estructura .= 'A';
            $j += 1;
            continue;
        } elseif (in_array($char, $i)) {
            $letras[] = $char;
            $estructura .= 'I';
            $j += 1;
            continue;
        } elseif (in_array($char, $l)) {
            $letras[] = $char;
            $estructura .= 'L';
            $j += 1;
            continue;
        } elseif (in_array($char, $o)) {
            $letras[] = $char;
            $estructura .= 'O';
            $j += 1;
            continue;
        } elseif (in_array($char, $c)) {
            $letras[] = $char;
            $estructura .= 'C';
            $j += 1;
            continue;
        } else {
            throw new Exception("No se reconoce el carácter '$char' como una letra del castellano.");
        }
    }
    $estructura .= 'C';
    $letras[] = '';
    
    // Segunda parte: Separación en sílabas
    $salida = [];
    $j = 0;
    $silaba = '';
    while ($j < count($letras)) {
        if ($letras[$j] == '') {
            break;
        }
        $silaba .= $letras[$j];
        if ($estructura[$j] == 'A') {
            if ($estructura[$j + 1] == 'A') {
                $salida[] = $silaba;
                $silaba = '';
                $j += 1;
                continue;
            } elseif ($estructura[$j + 1] == 'I') {
                $j += 1;
                continue;
            } elseif ($estructura[$j + 1] == 'O') {
                if (in_array($estructura[$j + 2], ['A', 'I', 'L'])) {
                    if ($letras[$j + 1] == 'd' && $letras[$j + 2] == 'l') {
                        $salida[] = $silaba . $letras[$j + 1];
                        $silaba = '';
                        $j += 2;
                        continue;
                    }
                    $salida[] = $silaba;
                    $silaba = '';
                    $j += 1;
                    continue;
                } else {
                    if ($letras[$j + 2] == 's' && in_array($estructura[$j + 3], ['L', 'C', 'O'])) {
                        $salida[] = $silaba . $letras[$j + 1] . $letras[$j + 2];
                        $silaba = '';
                        $j += 3;
                        continue;
                    }
                    $salida[] = $silaba . $letras[$j + 1];
                    $silaba = '';
                    $j += 2;
                    continue;
                }
            } else {
                if ($j + 2 < count($letras)) {
                    if (in_array($estructura[$j + 2], ['A', 'I'])) {
                        $salida[] = $silaba;
                        $silaba = '';
                        $j += 1;
                        continue;
                    } else {
                        if ($letras[$j + 2] == 's' && in_array($estructura[$j + 3], ['L', 'C', 'O'])) {
                            $salida[] = $silaba . $letras[$j + 1] . $letras[$j + 2];
                            $silaba = '';
                            $j += 3;
                            continue;
                        }
                        $salida[] = $silaba . $letras[$j + 1];
                        $silaba = '';
                        $j += 2;
                        continue;
                    }
                } else {
                    $salida[] = $silaba . $letras[$j + 1];
                    $silaba = '';
                    $j += 2;
                    continue;
                }
            }
        } elseif ($estructura[$j] == 'I') {
            if (in_array($estructura[$j + 1], ['A', 'I'])) {
                $j += 1;
                continue;
            } elseif ($estructura[$j + 1] == 'O') {
                if (in_array($estructura[$j + 2], ['A', 'I', 'L'])) {
                    if ($letras[$j + 1] == 'd' && $letras[$j + 2] == 'l') {
                        $salida[] = $silaba . $letras[$j + 1];
                        $silaba = '';
                        $j += 2;
                        continue;
                    }
                    $salida[] = $silaba;
                    $silaba = '';
                    $j += 1;
                    continue;
                } else {
                    if ($letras[$j + 2] == 's' && in_array($estructura[$j + 3], ['L', 'C', 'O'])) {
                        $salida[] = $silaba . $letras[$j + 1] . $letras[$j + 2];
                        $silaba = '';
                        $j += 3;
                        continue;
                    }
                    $salida[] = $silaba . $letras[$j + 1];
                    $silaba = '';
                    $j += 2;
                    continue;
                }
            } else {
                if ($j + 2 < count($letras)) {
                    if (in_array($estructura[$j + 2], ['A', 'I'])) {
                        $salida[] = $silaba;
                        $silaba = '';
                        $j += 1;
                        continue;
                    } else {
                        if ($letras[$j + 2] == 's' && in_array($estructura[$j + 3], ['L', 'C', 'O'])) {
                            $salida[] = $silaba . $letras[$j + 1] . $letras[$j + 2];
                            $silaba = '';
                            $j += 3;
                            continue;
                        }
                        $salida[] = $silaba . $letras[$j + 1];
                        $silaba = '';
                        $j += 2;
                        continue;
                    }
                } else {
                    $salida[] = $silaba . $letras[$j + 1];
                    $silaba = '';
                    $j += 2;
                    continue;
                }
            }
        } elseif ($estructura[$j] == 'O') {
            if (in_array($estructura[$j + 1], ['A', 'I', 'L'])) {
                $j += 1;
                continue;
            } else {
                if ($letras[$j + 1] == '') {
                    $salida[] = $silaba;
                    break;
                }
                throw new Exception("Estructura de sílaba incorrecta en la palabra $word");
            }
        } else {
            if (in_array($estructura[$j + 1], ['A', 'I'])) {
                $j += 1;
                continue;
            } else {
                if ($letras[$j + 1] == '') {
                    $salida[] = $silaba;
                    break;
                } elseif ($letras[$j + 1] == 's') {
                    $salida[] = $silaba;
                    $silaba = '';
                    $j += 1;
                    continue;
                }
                throw new Exception("Estructura de sílaba incorrecta en la palabra $word");
            }
        }
    }
    
    // Une las sílabas con el separador especificado
    return implode($sep, $salida);
}

    // Lista de palabras de prueba
$palabras = [
    "electroencefalografiquísimamente" => "e-lec-tro-en-ce-fa-lo-gra-fi-quí-si-ma-men-te",
    "neurorreflejoterapiquísimamente" => "neu-ro-rre-fle-jo-te-ra-pi-quí-si-ma-men-te",
    "hispanonorteamericanísimamente" => "his-pa-no-nor-te-a-me-ri-ca-ní-si-ma-men-te",
    "instituto" => "ins-ti-tu-to",
    "reunir" => "reu-nir",
    "deshacer" => "des-ha-cer",
    "casa" => "ca-sa",
    "aire" => "ai-re",
    "caos" => "ca-os",
    "guai" => "guai",
    "ahí" => "a-hí",
    "prohibir" => "pro-hi-bir",
    "guion" => "guion",
    "alcohol" => "al-co-hol",
    "rehúso" => "re-hú-so",
    "aéreo" => "a-é-re-o",
    "buey" => "buey",
    "ciudad" => "ciu-dad",
    "héroe" => "hé-ro-e",
    "bioquímica" => "bio-quí-mi-ca",
    "paraguas" => "pa-ra-guas",
    "zoológico" => "zo-o-ló-gi-co",
    "poeta" => "po-e-ta",
    "teoría" => "te-o-rí-a",
    "radiante" => "ra-dian-te",
    "inmueble" => "in-mue-ble",
    "desahucio" => "de-sa-hu-cio",
    "oír" => "o-ír",
    "construir" => "cons-truir",
    "vehículo" => "ve-hí-cu-lo",
    "pingüino" => "pin-güi-no",
    "ahínco" => "a-hín-co"
];

// Función para comprobar las separaciones silábicas
function comprobarSilabas($array_palabras) {
    $resultados = [];
    
    foreach ($array_palabras as $palabra => $silabas_esperadas) {
        // Llamamos a la función silabas() para obtener la separación
        $silabas_obtenidas = silabas($palabra, '-');
        
        // Comparamos el resultado con el valor esperado en el array
        if ($silabas_obtenidas === $silabas_esperadas) {
            $resultados[$palabra] = "Correcto: $silabas_obtenidas coincide con $silabas_esperadas";
        } else {
            $resultados[$palabra] = "Incorrecto: $silabas_obtenidas no coincide con $silabas_esperadas";
        }
    }
    
    return $resultados;
}

// Ejemplo de uso (asumiendo que silabificarBasico() existe)
$resultados = comprobarSilabas($palabras);

// Imprimir resultados
foreach ($resultados as $palabra => $resultado) {
    echo "$palabra - $resultado". "\n";
}
