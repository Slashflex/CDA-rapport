<?php
require_once '../../loadTwig.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php foreach ($invoice['BonDeCommande'] as $key => $value): ?>
    <table style="margin-bottom: 3rem">
        <caption>
            <strong>Bon de commande : <?= $value ?></strong>
        </caption>
        <thead>
            <tr>
                <?php foreach ($invoice['ColumnName'] as $key => $value): ?>
                <th><?= $value ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php for ($j = 0; $j < count($invoice['Value']); $j++): ?>
            <tr>
                <?php foreach ($invoice['Value'][$j] as $key => $value): ?>
                <td><?= $value ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>
    <p>Sous-total : 30€</p>
    <?php endforeach; ?>
</body>

</html>