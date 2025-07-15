<p>Seleccione una tabla para ver los registros:</p>
<form method="post" action="index.php">
    <select name="selectedTable">
        <option value="">-- Selecciona una tabla --</option>
        <?php
        if (isset($data) && is_array($data)) {
            $tabla_actual = isset($_POST['selectedTable']) ? $_POST['tabla_seleccionada'] : '';
            foreach ($data as $tabla) {
                $selected = ($tabla === $tabla_actual) ? 'selected' : '';
                echo "<option value='{$tabla}' {$selected}>{$tabla}</option>";
            }
        } else {
            echo "<option value=''>No hay tablas disponibles</option>";
        }
        ?>
    </select>
    <button type="submit" name="accion" value="getRegister">Seleccionar</button>
</form>

<?php
if (isset($registros) && is_array($registros) && !empty($registros)): ?>
    <h2>Registros de la tabla: <?= htmlspecialchars($_POST['selectedTable'] ?? '') ?></h2>
    <table>
        <thead>
            <tr>
                <?php foreach (array_keys($registros[0]) as $columna): ?>
                    <th><?= ucfirst(htmlspecialchars($columna)) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($registros as $fila): ?>
                <tr>
                    <?php foreach ($fila as $valor): ?>
                        <td>
                            <?= htmlspecialchars($valor ?? 'NULL') ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            <tr>
                <form id="formAddRow" method="POST" action="index.php">
                    <input type="hidden" name="insertTable" value="<?= htmlspecialchars($_POST['selectedTable']) ?>">
                    <?php foreach (array_keys($registros[0]) as $columna): ?>
                        <td> 
                            <input name="<?= $columna ?>" placeholder="añadir <?= ucfirst($columna) ?>"/>
                        </td>
                    <?php endforeach; ?>
                </form>
            </tr>
        </tbody>
    </table>
    <button form="formAddRow" type="submit" name="accion" value="addRow" >Añadir fila</button>
<?php elseif (isset($_POST['selectedTable']) && !empty($_POST['selectedTable'])): ?>
    <p>No se encontraron registros en la tabla seleccionada.</p>
<?php endif; ?>