<?php
class CompiladorPersonalizado {
    private $codigoFuente;
    private $tokens = [];
    private $tablaSimbolos = [];
    private $codigoIntermedio = [];

    private $palabrasReservadas = [
        'rotonda' => 'FOR',
        'romper' => 'BREAK',
        'dameto' => 'PRINT',
        'si' => 'IF',
        'quizas' => 'ELSE',
        'menudo' => 'FLOAT',
        'papeleta' => 'INT',
        'labia' => 'CHAR'
    ];

    private $operadores = ['+', '-', '*', '/', '=', '==', '!=', '<', '>', '<=', '>='];
    private $simbolosEspeciales = ['(', ')', '{', '}', ';'];

    public function __construct($codigoFuente) {
        $this->codigoFuente = $codigoFuente;
    }

    public function analizarLexico() {
        $lineas = explode("\n", $this->codigoFuente);

        foreach ($lineas as $numLinea => $linea) {
            $tokens = preg_split('/(\s+|[' . preg_quote(implode('', $this->simbolosEspeciales)) . '])/', $linea, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

            foreach ($tokens as $token) {
                $token = trim($token);

                if (empty($token)) {
                    continue;
                }

                // Reconocer el incremento y decremento (i++ o ++i)
                if (preg_match('/^\w\+\+$/', $token) || preg_match('/^\+\+\w$/', $token)) {
                    $this->tokens[] = ['tipo' => 'OPERADOR', 'valor' => 'INCREMENTO'];
                }
                // Reconocer el decremento (i-- o --i)
                elseif (preg_match('/^\w--$/', $token) || preg_match('/^--\w$/', $token)) {
                    $this->tokens[] = ['tipo' => 'OPERADOR', 'valor' => 'DECREMENTO'];
                }
                // Clasificación de los tokens
                elseif (isset($this->palabrasReservadas[$token])) {
                    $this->tokens[] = ['tipo' => 'RESERVADA', 'valor' => $token];
                } elseif (in_array($token, $this->operadores)) {
                    $this->tokens[] = ['tipo' => 'OPERADOR', 'valor' => $token];
                } elseif (in_array($token, $this->simbolosEspeciales)) {
                    $this->tokens[] = ['tipo' => 'SIMBOLO', 'valor' => $token];
                } elseif (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $token)) {
                    $this->tokens[] = ['tipo' => 'IDENTIFICADOR', 'valor' => $token];
                } elseif (preg_match('/^\d+(\.\d+)?$/', $token)) {
                    $this->tokens[] = ['tipo' => 'NUMERO', 'valor' => $token];
                } else {
                    throw new Exception("Token no reconocido: '$token' en la línea " . ($numLinea + 1));
                }
            }
        }
    }

    public function analizarSintactico() {
        foreach ($this->tokens as $i => $token) {
            if ($token['tipo'] === 'RESERVADA' && $token['valor'] === 'papeleta') { // INT declaration
                if (!isset($this->tokens[$i + 1]) || $this->tokens[$i + 1]['tipo'] !== 'IDENTIFICADOR') {
                    throw new Exception("Error Sintáctico: Se esperaba un IDENTIFICADOR después de 'papeleta'");
                }

                $identificador = $this->tokens[$i + 1]['valor'];
                $this->tablaSimbolos[$identificador] = ['tipo' => 'INT', 'valor' => null];

                // Verificar si hay una asignación
                if (isset($this->tokens[$i + 2]) && $this->tokens[$i + 2]['valor'] === '=') {
                    if (isset($this->tokens[$i + 3]) && $this->tokens[$i + 3]['tipo'] === 'NUMERO') {
                        $valor = $this->tokens[$i + 3]['valor'];
                        $this->tablaSimbolos[$identificador]['valor'] = $valor;
                    } else {
                        throw new Exception("Error Sintáctico: Se esperaba un valor numérico después de '='.");
                    }
                }
            }

            if ($token['tipo'] === 'RESERVADA' && $token['valor'] === 'dameto') { // PRINT statement
                if (!isset($this->tokens[$i + 1])) {
                    throw new Exception("Error Sintáctico: Se esperaba un valor después de 'dameto'");
                }
            }
        }
    }

    public function mostrarResultados() {
        // Generar código intermedio
        foreach ($this->tokens as $token) {
            if ($token['tipo'] === 'RESERVADA' && $token['valor'] === 'dameto') {
                $this->codigoIntermedio[] = "PRINT";
            } elseif ($token['tipo'] === 'RESERVADA' && $token['valor'] === 'papeleta') {
                $this->codigoIntermedio[] = "DECLARE INT";
            } else {
                $this->codigoIntermedio[] = $token['valor'];
            }
        }

        return [
            'lexico' => $this->tokens,
            'tablaSimbolos' => $this->tablaSimbolos,
            'codigoIntermedio' => $this->codigoIntermedio
        ];
    }
}
?>
