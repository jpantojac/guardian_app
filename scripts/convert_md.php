<?php
$colFile = 'storage/app/private/scribe/collection.json';
if (!file_exists($colFile)) {
    die("Collection file not found\n");
}
$col = json_decode(file_get_contents($colFile), true);
$md = "# Documentación de API - " . $col['info']['name'] . "\n\n";

function indentMultiline($text, $indentSpaces = 18) {
    if (empty($text)) return '';
    $indentStr = str_repeat(" ", $indentSpaces);
    
    $lines = explode("\n", $text);
    $output = "";
    
    foreach ($lines as $i => $line) {
        if ($i == 0) {
            $output .= $line . "\n";
        } else {
            $output .= $indentStr . $line . "\n";
        }
    }
    return rtrim($output); 
}

if (isset($col['item'])) {
    foreach ($col['item'] as $group) {
        $md .= "## " . $group['name'] . "\n\n";
        
        if (isset($group['item'])) {
            foreach ($group['item'] as $endpoint) {
                $req = $endpoint['request'];
                $md .= "### " . $endpoint['name'] . "\n\n";
                
                if (isset($req['description'])) {
                    $md .= $req['description'] . "\n\n";
                }
                
                // MULTILINE TABLE START
                $md .= "-----------------------------------------------------------------------\n";
                $md .= "Atributo          Detalle\n";
                $md .= "----------------- -----------------------------------------------------\n";
                
                // METHOD
                $md .= "**Método**        " . $req['method'] . "\n\n";
                
                // URL
                $md .= "**URL**           `" . $req['url']['raw'] . "`\n\n";
                
                // HEADERS
                if (isset($req['header']) && count($req['header']) > 0) {
                    $headerStr = "";
                    foreach ($req['header'] as $i => $h) {
                        $headerStr .= "- `" . $h['key'] . "`: " . $h['value'] . ($i < count($req['header']) - 1 ? "\n" : "");
                    }
                    $md .= "**Headers**       " . indentMultiline($headerStr) . "\n\n";
                }
                
                // PARAMETROS (URLENCODED)
                if (isset($req['body'])) {
                    if ($req['body']['mode'] === 'urlencoded' && isset($req['body']['urlencoded'])) {
                        $paramStr = "";
                        foreach ($req['body']['urlencoded'] as $i => $p) {
                            $desc = isset($p['description']) ? str_replace("\n", " ", $p['description']) : '';
                            $paramStr .= "- `" . $p['key'] . "` ($desc)" . ($i < count($req['body']['urlencoded']) - 1 ? "\n" : "");
                        }
                        $md .= "**Parametros**    " . indentMultiline($paramStr) . "\n\n";
                    } elseif ($req['body']['mode'] === 'formdata' && isset($req['body']['formdata'])) {
                        $paramStr = "";
                        foreach ($req['body']['formdata'] as $i => $p) {
                            $desc = isset($p['description']) ? str_replace("\n", " ", $p['description']) : '';
                            $type = isset($p['type']) ? $p['type'] : 'text';
                            $paramStr .= "- `" . $p['key'] . "` [$type] ($desc)" . ($i < count($req['body']['formdata']) - 1 ? "\n" : "");
                        }
                        $md .= "**Form-Data**     " . indentMultiline($paramStr) . "\n\n";
                    }
                }
                
                // QUERY PARAMETERS
                if (isset($req['url']['query']) && count($req['url']['query']) > 0) {
                    $queryStr = "";
                    foreach ($req['url']['query'] as $i => $q) {
                        $desc = isset($q['description']) ? str_replace("\n", " ", $q['description']) : '';
                        $queryStr .= "- `" . $q['key'] . "` ($desc)" . ($i < count($req['url']['query']) - 1 ? "\n" : "");
                    }
                    $md .= "**Query Params**  " . indentMultiline($queryStr) . "\n\n";
                }
                
                // EJEMPLO DE RESPUESTA
                if (isset($endpoint['response']) && count($endpoint['response']) > 0) {
                    $jsonStr = "```json\n" . trim($endpoint['response'][0]['body']) . "\n```";
                    $md .= "**Ejemplo**       " . indentMultiline($jsonStr) . "\n\n";
                }
                
                $md .= "-----------------------------------------------------------------------\n\n";
            }
        }
    }
}

file_put_contents('docs/API_DOCUMENTATION.md', $md);
echo "API_DOCUMENTATION.md successfully exported with Multiline Tables format.\n";
