<?php
use common\components\FechaHelper;

?>
<h2>Los siguientes movimientos estan prontos a vencerse</h2>
<?php foreach ($movimientos as $movimiento): ?>
    <p>
        <p><?php switch ($movimiento->Color):
            case 'negative':
                echo 'Perentorio';
                break;
            
            case 'primary':
                echo 'Gestion Estudio';
                break;
                
            case 'warning':
                echo 'Gestion Externa';
                break;

            default:
                echo '---';
        endswitch;?></p>
        <p><b><?=$movimiento->Caratula?></b><br>- <?=$movimiento->Juzgado?> <?=$movimiento->NroExpediente?><br> - <?=$movimiento->Detalle?></p>
        -----
    </p>
    <br>
<?php endforeach; ?>