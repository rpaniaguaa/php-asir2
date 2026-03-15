<?php
    function porcentajeUso($total,$usado){
        if ($total == 0){
            return "Error, espacio insuficiente<br>";
        }else {
            return ($usado * 100) / $total;
        }
    }

    function estadoDisco($porcentaje){
        if ($porcentaje < 70){
            return "<strong style='color: green'>OK</strong>";
        }elseif($porcentaje >= 70 && $porcentaje <= 90){
            return "<strong style='color:orange'>ALERTA</strong>";
        }
        elseif ($porcentaje > 90) {
            return "<strong style='color:red'>CRÍTICO</strong>";
        }
    }

    function mostrarInformeDisco($nombreServidor,$estado,$porcentaje){
        echo "Servidor ".$nombreServidor." - Uso: ".$porcentaje."% - Estado: ".$estado;
    }
    $total=500;
    $usado=425;
   
    echo "Total: ".$total."<br>";
    echo "Usado: ".$usado."<br>";

    echo mostrarInformeDisco("/deb/sda1",estadoDisco(porcentajeUso($total,$usado)),porcentajeUso($total,$usado))."<br>";

    $total=700;
    $usado=325;
    echo "<br>";
    echo "Total: ".$total."<br>";
    echo "Usado: ".$usado."<br>";

    echo mostrarInformeDisco("/deb/sdb2",estadoDisco(porcentajeUso($total,$usado)),porcentajeUso($total,$usado));


?>