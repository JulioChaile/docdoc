<?php
?>
<p><?= $contenido ?></p>

<br>
<p>Puede descargar los archivos adjuntos desde los siguientes links:</p>
<br>
<?php foreach ($links as $key => $value): ?>
    <p>
        <a href="https://io.docdoc.com.ar/api/multimedia?file=<?php echo $value ?>&download=true" download="`archivo-${<?php echo $key ?>+1}">
            Archivo-<?= $key + 1 ?>
        </a>
    </p>
    <br>
<?php endforeach ?>