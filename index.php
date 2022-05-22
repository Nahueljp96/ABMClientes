<?php
ini_set('display_erros',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

if(file_exists("archivo.txt")){
    //si el archivo existe, cargo los clientes en la variable aClientes
    $strJson = file_get_contents("archivo.txt");
    $aClientes = json_decode($strJson, true);
} else{
    //si el archivo no existe es porque no hay clientes
    $aClientes = array();
}
if(isset($_GET["id"])){
    $id = $_GET["id"];
} else {
    $id="";
}
if(isset($_GET["do"]) && $_GET["do"]== "eliminar"){
    unset($aClientes[$id]);
    //Convertir aClientes en json
    $strJson = json_encode(($aClientes));
    //Almacenar el json en el archivo
    file_put_contents("archivo.txt", $strJson);

    header("location: index.php"); //esta linea es para que limpie la pagina
}

if($_POST){
    $dni=$_POST["txtDni"];
    $nombre=$_POST["txtNombre"];
    $telefono=$_POST["txtTelefono"];
    $correo=$_POST["txtCorreo"];
    $nombreImagen="";
    $ruta="imagenes\\"; //directorio donde se guardan las imagenes
    
    if(array_key_exists("archivo", $_FILES)){ //array_key pregunta si "archivo" fue subido
        if($_FILES["archivo"]["error"]===UPLOAD_ERR_OK){ //esta linea valida si el archivo fue subido correctamente
            $nombreAleatorio = date("Ymdhmsi");         //genera el nombre aleatorio
            $archivo_tmp=$_FILES["archivo"]["tmp_name"]; //nombre del archivo en memoria
            $nombre_archivo=$_FILES["archivo"]["name"]; //nombre original del archivo
            $extension = pathinfo($_FILES["archivo"]["name"],PATHINFO_EXTENSION); //obtiene la extensión del nombre
           
            if($extension =="jpg" || $extension =="png" || $extension =="jpeg") //valida extensión del nombre del archivo deseado
                move_uploaded_file($archivo_tmp, "$ruta$nombreAleatorio.$extension"); //guarda el archivo en el disco
                $nombreImagen="$nombreAleatorio.$extension"; //asigna el nuevo nombre de la imagen para mostrarlo
        }
    }
    $aClientes[] =array("dni"=>$dni,
                        "nombre"=>$nombre,
                        "telefono"=>$telefono,
                        "correo"=>$correo,
                        "imagen"=>$nombreImagen
);
    //convertir el arrray de clientes json
    $strJson = json_encode($aClientes);

    //Almacenar en un archito.txt el json con file_put_contents
    file_put_contents("archivo.txt",$strJson);

}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="css/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">"
    <title>AbmClientes</title>
</head>
<script type="text/javascript"> //Scrip de java para hacer el cartel de confirmar
    function confirmDelete() //creamos una función "confirmDelete"
    {
        var respuesta= confirm("¿Estas seguro que deseas eliminar al cliente?"); //declaramos la variable "respuesta"
        
        if (respuesta==true) //el return es para que nos devuelva el valor y utilizarlo en la llamada de la función
        {
            return true;
        }
         else
        {
            return false;
        }  
    }
</script>      
<body>
    <main class="container">
        <div class="row">
            <div class="col-12 text-center my-5">
                <h1>Registro de Clientes</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-5">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div>
                        <label for="txtDni">Dni:*</label>
                        <input type="text" name="txtDni" id="txtDni" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["dni"] : ""; ?>">

                    </div>    
                    <div>
                        <label for="txtNombre">Nombre:*</label>
                        <input type="text" name="txtNombre" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["nombre"] : ""; ?>">
                    </div>    
                    <div>
                        <label for="txtTelefono">Teléfono:*</label>
                        <input type="number" name="txtTelefono" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["telefono"] : ""; ?>">
                    </div>    
                    <div>
                        <label for="txtCorreo">Correo:*</label>
                        <input type="email" name="txtCorreo" class="form-control" required value="<?php echo isset($aClientes[$id])? $aClientes[$id]["correo"] : ""; ?>">
                    </div>
                    <div>   
                    <label for="">Archivo adjunto</label>
                    <input type="file" name="archivo" id="archivo" accept=".png">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary m-1">Guardar</button>
                        <button type="summit" class="btn btn-danger m-1">Nuevo</button>
                    </div> 
                </form>
            </div>
            <div class="col-7">
            <table class="table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Telefono</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <?php
                    
                    foreach($aClientes as $pos => $cliente): ?>
                        <tr>
                            <td><img src="imagenes/<?php echo $cliente["imagen"]; ?>" class="img-thumbnail"></td>
                            <td><?php echo $cliente["dni"]; ?></td>
                            <td><?php echo $cliente["nombre"]; ?></td>
                            <td><?php echo $cliente["telefono"]; ?></td>
                            <td><?php echo $cliente["correo"]; ?></td>
                            <td>
                                <a href="?id=<?php echo $pos; ?>"><i class="fa-solid fa-pen-to-square"></a></i>
                                <a href="?id=<?php echo $pos; ?>&do=eliminar" onclick="return confirmDelete()"><i class="fa-solid fa-trash-can"></i> <!--"onclick" es un evento por el cual le vuelve el valor "return" de la función y nos da el cartel de si estas seguro-->
                            </td>
                        </tr>
                     <?php endforeach; ?>
    
            </table>
        </div>
        

        </div>
    </main>
    
</body>
</html>