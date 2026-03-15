<?php
for ($i = 2; $i <= 10; $i += 2) {
    if ($i == 6) {
        continue; // no imprime el 6
    }
    echo $i . " ";
}
?>
<br>
<?php
$equipo = ["Leo", "Cris", "Ney"];

$equipo[] = "Lamine";

$equipo[1] = "Mbappé";

foreach ($equipo as $jugador) {
    echo $jugador . " ";
}

print_r($_SERVER['SERVER_ADDR']);
?>
