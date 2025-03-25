<?php
class SeparadorSilabico {
    private $word;
    private $sep;
    private $letras = [];
    private $estructura = '';
    private $categorias = [
        'l' => ['r', 'l'],
        'o' => ['p', 'b', 'f', 't', 'd', 'c', 'k', 'g'],
        'c' => ['b', 'c', 'ch', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'll', 'm', 'n', 'ñ', 'p', 'q', 'r', 'rr', 's', 't', 'v', 'x', 'y', 'z'],
        'a' => ['a', 'e', 'o', 'á', 'é', 'ó', 'í', 'ú'],
        'i' => ['i', 'u', 'ü']
    ];

    public function __construct($word, $sep = '-') {
        $this->word = mb_strtolower($word, 'UTF-8');
        $this->sep = $sep;
    }

    public function separar() {
        $this->construirEstructura();
        return $this->separarEnSilabas();
    }

    private function construirEstructura() {
        $j = 0;
        while ($j < mb_strlen($this->word, 'UTF-8')) {
            $j = $this->procesarCaracteres($j);
        }
        $this->estructura .= 'C';
        $this->letras[] = '';
    }

    private function procesarCaracteres($j) {
        if ($j == 0) {
            $especiales = ['ps', 'pn', 'pt', 'gn'];
            foreach ($especiales as $esp) {
                if (mb_substr($this->word, $j, 2, 'UTF-8') == $esp) {
                    $this->letras[] = $esp;
                    $this->estructura .= 'C';
                    return $j + 2;
                }
            }
        }

        if ($j < mb_strlen($this->word, 'UTF-8') - 1) {
            $digrafos = ['ch', 'll', 'rr'];
            foreach ($digrafos as $dig) {
                if (mb_substr($this->word, $j, 2, 'UTF-8') == $dig) {
                    $this->letras[] = $dig;
                    $this->estructura .= 'C';
                    return $j + 2;
                }
            }
        }

        $char = mb_substr($this->word, $j, 1, 'UTF-8');
        foreach ($this->categorias as $tipo => $lista) {
            if (in_array($char, $lista)) {
                $this->letras[] = $char;
                $this->estructura .= strtoupper($tipo);
                return $j + 1;
            }
        }

        throw new Exception("No se reconoce el carácter '$char' como una letra del castellano.");
    }

    private function separarEnSilabas() {
        $salida = [];
        $j = 0;
        $silaba = '';

        while ($j < count($this->letras)) {
            if ($this->letras[$j] == '') {
                break;
            }
            $silaba .= $this->letras[$j];
            $j = $this->procesarSilaba($j, $silaba, $salida);
        }

        return implode($this->sep, $salida);
    }

    private function procesarSilaba($j, &$silaba, &$salida) {
        switch ($this->estructura[$j]) {
            case 'A':
                return $this->procesarA($j, $silaba, $salida);
            case 'I':
                return $this->procesarI($j, $silaba, $salida);
            case 'O':
                return $this->procesarO($j, $silaba, $salida);
            default:
                return $this->procesarConsonante($j, $silaba, $salida);
        }
    }

    private function procesarA($j, &$silaba, &$salida) {
        if ($this->estructura[$j + 1] == 'A') {
            $salida[] = $silaba;
            $silaba = '';
            return $j + 1;
        } elseif ($this->estructura[$j + 1] == 'I') {
            return $j + 1;
        } elseif ($this->estructura[$j + 1] == 'O') {
            return $this->procesarAO($j, $silaba, $salida);
        } else {
            return $this->procesarAC($j, $silaba, $salida);
        }
    }

    private function procesarI($j, &$silaba, &$salida) {
        if (in_array($this->estructura[$j + 1], ['A', 'I'])) {
            return $j + 1;
        } elseif ($this->estructura[$j + 1] == 'O') {
            return $this->procesarIO($j, $silaba, $salida);
        } else {
            return $this->procesarIC($j, $silaba, $salida);
        }
    }

    private function procesarO($j, &$silaba, &$salida) {
        if (in_array($this->estructura[$j + 1], ['A', 'I', 'L'])) {
            return $j + 1;
        } else {
            if ($this->letras[$j + 1] == '') {
                $salida[] = $silaba;
                return $j + 1;
            }
            throw new Exception("Estructura de sílaba incorrecta en la palabra {$this->word}");
        }
    }

    private function procesarConsonante($j, &$silaba, &$salida) {
        if (in_array($this->estructura[$j + 1], ['A', 'I'])) {
            return $j + 1;
        } else {
            if ($this->letras[$j + 1] == '') {
                $salida[] = $silaba;
                return $j + 1;
            } elseif ($this->letras[$j + 1] == 's') {
                $salida[] = $silaba;
                $silaba = '';
                return $j + 1;
            }
            throw new Exception("Estructura de sílaba incorrecta en la palabra {$this->word}");
        }
    }

    private function procesarAO($j, &$silaba, &$salida) {
        if (in_array($this->estructura[$j + 2], ['A', 'I', 'L'])) {
            if ($this->letras[$j + 1] == 'd' && $this->letras[$j + 2] == 'l') {
                $salida[] = $silaba . $this->letras[$j + 1];
                $silaba = '';
                return $j + 2;
            }
            $salida[] = $silaba;
            $silaba = '';
            return $j + 1;
        } else {
            if ($this->letras[$j + 2] == 's' && in_array($this->estructura[$j + 3], ['L', 'C', 'O'])) {
                $salida[] = $silaba . $this->letras[$j + 1] . $this->letras[$j + 2];
                $silaba = '';
                return $j + 3;
            }
            $salida[] = $silaba . $this->letras[$j + 1];
            $silaba = '';
            return $j + 2;
        }
    }

    private function procesarAC($j, &$silaba, &$salida) {
        if ($j + 2 < count($this->letras)) {
            if (in_array($this->estructura[$j + 2], ['A', 'I'])) {
                $salida[] = $silaba;
                $silaba = '';
                return $j + 1;
            } else {
                if ($this->letras[$j + 2] == 's' && in_array($this->estructura[$j + 3], ['L', 'C', 'O'])) {
                    $salida[] = $silaba . $this->letras[$j + 1] . $this->letras[$j + 2];
                    $silaba = '';
                    return $j + 3;
                }
                $salida[] = $silaba . $this->letras[$j + 1];
                $silaba = '';
                return $j + 2;
            }
        } else {
            $salida[] = $silaba . $this->letras[$j + 1];
            $silaba = '';
            return $j + 2;
        }
    }

    private function procesarIO($j, &$silaba, &$salida) {
        if (in_array($this->estructura[$j + 2], ['A', 'I', 'L'])) {
            if ($this->letras[$j + 1] == 'd' && $this->letras[$j + 2] == 'l') {
                $salida[] = $silaba . $this->letras[$j + 1];
                $silaba = '';
                return $j + 2;
            }
            $salida[] = $silaba;
            $silaba = '';
            return $j + 1;
        } else {
            if ($this->letras[$j + 2] == 's' && in_array($this->estructura[$j + 3], ['L', 'C', 'O'])) {
                $salida[] = $silaba . $this->letras[$j + 1] . $this->letras[$j + 2];
                $silaba = '';
                return $j + 3;
            }
            $salida[] = $silaba . $this->letras[$j + 1];
            $silaba = '';
            return $j + 2;
        }
    }

    private function procesarIC($j, &$silaba, &$salida) {
        if ($j + 2 < count($this->letras)) {
            if (in_array($this->estructura[$j + 2], ['A', 'I'])) {
                $salida[] = $silaba;
                $silaba = '';
                return $j + 1;
            } else {
                if ($this->letras[$j + 2] == 's' && in_array($this->estructura[$j + 3], ['L', 'C', 'O'])) {
                    $salida[] = $silaba . $this->letras[$j + 1] . $this->letras[$j + 2];
                    $silaba = '';
                    return $j + 3;
                }
                $salida[] = $silaba . $this->letras[$j + 1];
                $silaba = '';
                return $j + 2;
            }
        } else {
            $salida[] = $silaba . $this->letras[$j + 1];
            $silaba = '';
            return $j + 2;
        }
    }
}


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
    "ahínco" => "a-hín-co",
    "casa" => "ca-sa",
    "perro" => "pe-rro",
    "cantar" => "can-tar",
    "construcción" => "cons-truc-ción",
    "extraordinario" => "ex-tra-or-di-na-rio",
    "aéreo" => "a-é-re-o",
    "psicología" => "psi-co-lo-gí-a",
    "subrayar" => "su-bra-yar",
    "transatlántico" => "tran-sa-tlán-ti-co",
    "ahí" => "a-hí",
    "cooperar" => "co-o-pe-rar",
    "chiita" => "chi-i-ta",
    "alcohol" => "al-co-hol",
    "caída" => "ca-í-da",
    "búho" => "bú-ho",
    "día" => "dí-a",
    "continúa" => "con-ti-nú-a",
    "averigüéis" => "a-ve-ri-güéis",  // Triptongo
    "uruguay" => "u-ru-guay",      // Triptongo
    "buey" => "buey",            // Triptongo (monosílabo)
    "limpiais" => "lim-piais",       //Triptongo
    "radio" => "ra-dio",
    "canción" => "can-ción",
    "cuidado" => "cui-da-do",
    "ciudad" => "ciu-dad",
    "fuimos" => "fui-mos",
    "jesuita" => "je-sui-ta",
    "veintiún" => "vein-ti-ún",
    "veintidós" => "vein-ti-dós",
    "ruido" => "rui-do",
    "construido" => "cons-trui-do",
    "destruido" => "des-trui-do",
    "huida" => "hui-da",
    "huir" => "huir",
    "incluido" => "in-clui-do",
    "excluido" => "ex-clui-do",
    "pingüino" => "pin-güi-no",
    "cigüeña" => "ci-güe-ña",
    "ambigüedad" => "am-bi-güe-dad",
    "antigüedad" => "an-ti-güe-dad",
    "lingüística" => "lin-güís-ti-ca",
    "presidente" => "pre-si-den-te",
    "suboficial" => "su-bo-fi-cial",
    "inacción" => "i-nac-ción",
    "deshacer" => "des-ha-cer",
    "cohibir" => "cohi-bir",
    "prohibido" => "prohi-bi-do",
    "ahumar" => "ahu-mar",
    "ahorrar" => "a-ho-rrar",
    "zanahoria" => "za-na-ho-ria",
    "ahínco" => "a-hín-co",
    "israel" => "is-ra-el",
    "bahía" => "ba-hí-a",
    "dehesa" => "de-he-sa",
    "vehículo" => "ve-hí-cu-lo",
    "ahijado" => "a-hi-ja-do",
    "cohete" => "co-he-te",
    "albahaca" => "al-ba-ha-ca",
    "exhalar" => "ex-ha-lar",
    "inhumar" => "in-hu-mar",
    "exhaustivo" => "ex-haus-ti-vo",
    "exhibición" => "ex-hi-bi-ción",
    "inhóspito" => "in-hós-pi-to",
    "malhumor" => "mal-hu-mor",
    "bienhechor" => "bien-he-chor",
    "enhorabuena" => "en-ho-ra-bue-na",
    "nohostiense" => "no-hos-tien-se", //Caso especial.
    "constante" => "cons-tan-te",
    "instaurar" => "ins-tau-rar",
    "obstruir" => "obs-truir",
    "subscribir" => "subs-cri-bir",
    "circunscribir" => "cir-cuns-cri-bir",
    "adscribir" => "ads-cri-bir",
    "perspicaz" => "pers-pi-caz",
    "examen" => "e-xa-men",
    "exámenes" => "e-xá-me-nes",
    "carácter" => "ca-rác-ter",
    "caracteres" => "ca-rac-te-res",
    "especímenes" => "es-pe-cí-me-nes",
    "espécimen" => "es-pé-ci-men",
    "régimen" => "ré-gi-men",
    "regímenes" => "re-gí-me-nes",
    "feliz" => "fe-liz",
    "oír" => "o-ír",
    "raíz" => "ra-íz",
    "reír" => "re-ír",
    "país" => "pa-ís",
    "caos" => "ca-os",
    "aorta" => "a-or-ta",
    "faraón" => "fa-ra-ón",
    "bilbaíno" => "bil-ba-í-no",
    "poseer" => "po-se-er",
    "creer" => "cre-er",
    "leer" => "le-er",
    "proveer" => "pro-ve-er",
    "jaén" => "ja-én",
    "campeón" => "cam-pe-ón",
    "poeta" => "po-e-ta",
    "teatro" => "te-a-tro",
    "línea" => "lí-ne-a",
    "héroe" => "hé-ro-e",
    "mediterráneo" => "me-di-te-rrá-ne-o",
    "instantáneo" => "ins-tan-tá-ne-o"
];

// Uso de la clase
try {
    $separador = new SeparadorSilabico("palabra");
    //echo $separador->separar();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Función para comprobar las separaciones silábicas
function comprobarSilabas($array_palabras) {
    $resultados = [];
    
    foreach ($array_palabras as $palabra => $silabas_esperadas) {
        // Llamamos a la función silabas() para obtener la separación
        $separador = new SeparadorSilabico($palabra, '-');
        $silabas_obtenidas = $separador->separar();
        
        // Comparamos el resultado con el valor esperado en el array
        if ($silabas_obtenidas === $silabas_esperadas) {
            //$resultados[$palabra] = "Correcto: $silabas_obtenidas coincide con $silabas_esperadas";
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
