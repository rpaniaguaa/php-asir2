<?php 
    require 'conexion13.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $id=$_POST['id'];
        $vers=trim($_POST['version'] ?? '');
        $licencia=$_POST['options'];

        $sql="UPDATE software SET version=:vers, licencia=:licencia WHERE id=:id";
        $stmt=$pdo->prepare($sql);  

        if (empty($vers)){
            die("Error: No debe haber campos vacios");
        }

        try {
           
            $stmt->execute([
                ':vers'=> $vers,
                ':licencia'=>$licencia,
                ':id'=>$id
            ]);

            header('Location: listado_sw.php?msg=actualizado');
            exit;
            
        }catch(PDOException $e){
            echo "Error:".$e;
        }  

    }
?>