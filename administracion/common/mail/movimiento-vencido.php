<?php
use common\components\FechaHelper;

?>
<h2>Los siguientes movimientos estan prontos a vencerse</h2>
<?php foreach ($movimientos as $movimiento): ?>
    <p>
        <ul>
            <li>Detalle: <?=$movimiento->Detalle?></li>
            <li>Caso: <?=$movimiento->Caratula?></li>
            <li>Juzgado: <?=$movimiento->Juzgado?></li>
            <li>Jurisdiccion: <?=$movimiento->Jurisdiccion?></li>
            <li>Fecha de vencimiento: <?=FechaHelper::formatearDateLocal($movimiento->FechaEsperada)?></li>
            <li>NroExpediente: <?=$movimiento->NroExpediente?>
    </p>
<?php endforeach; ?>