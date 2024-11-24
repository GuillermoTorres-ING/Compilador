<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delincuente++</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            text-align: center;
        }
        main {
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
        }
        textarea {
            width: 100%;
            height: 200px;
            margin-bottom: 15px;
            font-size: 16px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .output {
            margin-top: 20px;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .output h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        pre {
            background: #eee;
            padding: 15px;
            border-radius: 5px;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>Delincuente++</h1>
    </header>
    <main>
        <form method="POST">
            <h2>Escribe tu labia de fuente:</h2>
            <textarea id="codigo" name="codigoFuente" placeholder="Escribe tu labia de fuente aquí..."><?php echo isset($_POST['codigoFuente']) ? htmlspecialchars($_POST['codigoFuente']) : ''; ?></textarea>
            <button type="submit">Compilar</button>
            <button type="button" onclick="limpiar()">Liquidar</button>
        </form>

        <div id="lexico-output"></div>
        <div id="tabla-simbolos-output"></div>
        <div id="codigo-intermedio-output"></div>

        <script>
            function limpiar() {
                // Limpiar el textarea
                document.getElementById('codigo').value = '';

                // Limpiar los contenedores de resultados
                document.getElementById('lexico-output').innerHTML = '';
                document.getElementById('tabla-simbolos-output').innerHTML = '';
                document.getElementById('codigo-intermedio-output').innerHTML = '';
            }
        </script>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigoFuente'])) {
            require 'CompiladorClass.php'; // Asegúrate de tener este archivo en el mismo directorio.

            $codigo = $_POST['codigoFuente'];

            try {
                $compilador = new CompiladorPersonalizado($codigo);
                $compilador->analizarLexico();
                $compilador->analizarSintactico();
                $resultados = $compilador->mostrarResultados();

                // Análisis Léxico
                echo '<div id="lexico-output" class="output">';
                echo '<h2>Análisis Léxico</h2>';
                echo '<table>';
                echo '<tr><th>Tipo</th><th>Valor</th></tr>';
                foreach ($resultados['lexico'] as $token) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($token['tipo']) . '</td>';
                    echo '<td>' . htmlspecialchars($token['valor']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '</div>';

                // Tabla de Símbolos
                echo '<div id="tabla-simbolos-output" class="output">';
                echo '<h2>Tabla de Símbolos</h2>';
                echo '<table>';
                echo '<tr><th>Identificador</th><th>Tipo</th><th>Valor</th></tr>';
                foreach ($resultados['tablaSimbolos'] as $id => $simbolo) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($id) . '</td>';
                    echo '<td>' . htmlspecialchars($simbolo['tipo']) . '</td>';
                    echo '<td>' . htmlspecialchars($simbolo['valor']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
                echo '</div>';

                // Código Intermedio
                echo '<div id="codigo-intermedio-output" class="output">';
                echo '<h2>Código Intermedio</h2>';
                echo '<pre>' . htmlspecialchars(implode("\n", $resultados['codigoIntermedio'])) . '</pre>';
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="output error">';
                echo '<h2>Error</h2>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
        }
        ?>
    </main>
</body>
</html>
