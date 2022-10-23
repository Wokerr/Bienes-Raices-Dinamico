<?php 
require '../../includes/funciones.php';
$auth = estaAutenticado();

if(!$auth) {
    header('location: /');
}


    //Validar ID valido
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if(!$id) {
        header('Location: /admin');
    }

    require '../../includes/config/database.php';

    $db=conectarDB();

    $consulta= "SELECT * FROM propiedades WHERE id = ${id}";
    $resultado = mysqli_query($db, $consulta);
    $propiedad = mysqli_fetch_assoc($resultado);

    // echo "<pre>";
    // var_dump($propiedad);
    // echo "</pre>";


    echo $consulta;

    $consulta = "SELECT * FROM vendedores";
    $resultado= mysqli_query($db, $consulta);
    //Arreglo con mensajes de errores
    $errores = [];

        $titulo = $propiedad['titulo'];
        $precio = $propiedad['precio'];
        $descripcion = $propiedad['descripcion'];
        $habitaciones = $propiedad['habitaciones'];    
        $wc = $propiedad['wc'];
        $estacionamiento = $propiedad['estacionamiento'];
        $vendedorId = $propiedad['vendedores_id'];
        $imagenPropiedad =$propiedad['imagen'];

    //Ejecytar el codigo despues que el usuario envia el formulario
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";
    
        // echo "<pre>";
        // var_dump($_FILES);
        // echo "</pre>";

        
        $titulo = mysqli_real_escape_string( $db, $_POST['titulo'] );    
        $precio = mysqli_real_escape_string( $db, $_POST['precio'] );   
        $descripcion = mysqli_real_escape_string( $db, $_POST['descripcion'] );   
        $habitaciones = mysqli_real_escape_string( $db, $_POST['habitaciones'] );   
        $wc = mysqli_real_escape_string( $db, $_POST['wc'] );   
        $estacionamiento = mysqli_real_escape_string( $db, $_POST['estacionamiento'] );   
        $vendedorId = mysqli_real_escape_string( $db, $_POST['vendedor'] );   
        $creado = date('Y/m/d');

        // Variable de las imagenes
        $imagen = $_FILES['imagen'];  

        var_dump($imagen['name']);

        if(!$titulo) {
            $errores[] = "Debes añadir un titulo";
        }
        if(!$precio) {
            $errores[] = "Debes añadir un precio";
        }
        if(strlen($descripcion) < 50) {
            $errores[] = "La descripcion es obligatoria y debe tener al menos 50 caracteres";
        }
        if(!$habitaciones) {
            $errores[] = 'El numero de habitaciones es obligatorio';
        }
        if(!$wc) {
            $errores[] = 'El numero de baños es obligatorio';
        }
        if(!$estacionamiento) {
            $errores[] = 'El numero de estacionamiento es obligatorio';
        }
        if(!$vendedorId) {
            $errores[] = 'Selecciona un vendedor';
        }
        

        // Validar tamaño
        $medida = 1000 * 1000;
        
        if($imagen['size'] > $medida) {
            $errores[] = 'La imagen es muy pesada';
        }
 
        // Revisar que el arreglo de errores esté vacio

        if(empty($errores)) {

            // Subida de archivos

            //Crear una carpeta
            $carpetaImagenes = '../../imagenes/';

            if(!is_dir($carpetaImagenes)){
                mkdir($carpetaImagenes);
            }

            $nombreImagen = '';

            if($imagen['name']) {
                unlink($carpetaImagenes . $propiedades['imagen']);

                $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";

                move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . "$nombreImagen");
        
            } else {
                $nombreImagen = $propiedad['imagen'];
            }          

            $query = "UPDATE propiedades SET titulo = '${titulo}', precio = '${precio}', imagen = '${nombreImagen}', descripcion = '${descripcion}', habitaciones = ${habitaciones}, wc = ${wc}, estacionamiento = ${estacionamiento}, vendedores_id = ${vendedorId} WHERE id = ${id}" ;
            // echo $query;
            

            $resultado = mysqli_query($db, $query);
    
            if($resultado) {
                header('Location: /admin?resultado=2');
            }
        };
        }

        //Insertar en la base de datos

       

    
    incluirTemplate('header'); ?>

    <main class="contenedor seccion">
        <h1>Actualizar propiedad</h1>

        <a href="/admin" class="boton-verde boton">Volver</a>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach ?>    


        <form class="fomulario" method="POST" enctype="multipart/form-data">
            <fieldset>
                <legend>Informacion General</legend>

                <label for="titulo">Titulo</label>
                <input type="text" name="titulo" id="titulo" placeholder="Titulo Propiedad" value="<?php echo $titulo; ?>">

                <label for="precio">Precio</label>
                <input type="number" name="precio" id="precio" placeholder="Precio de la Propiedad" value="<?php echo $precio; ?>">

                <label for="imagen">Imagen</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

                <img src="/imagenes/<?php echo $imagenPropiedad; ?>" class="imagen-small">

                <label for="descripcion">Descripcion</label>
                <textarea id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>

            </fieldset>

            <fieldset>
                <legend>Informacion de la Propiedad</legend>

                <label for="habitaciones">Habitaciones</label>
                <input type="number" name="habitaciones" id="habitaciones" placeholder="Ej: 1" min = "1" max="9" value="<?php echo $habitaciones; ?>">

                <label for="wc">Baños</label>
                <input type="number" name="wc" id="wc" placeholder="Ej: 1" min = "1" max="9" value="<?php echo $wc; ?>">

                <label for="estacionamiento">Estacionamiento</label>
                <input type="number" name="estacionamiento" id="estacionamiento" placeholder="Ej: 1" min = "0" max="9" value="<?php echo $estacionamiento; ?>">

            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>

                <select name="vendedor">
                    <option value="" selected disabled>--Nombre--</option>
                    <?php while($row = mysqli_fetch_assoc($resultado)) : ?>
                        <option <?php echo $vendedorId === $row['id'] ? 'selected' : '';?> value="<?php echo $row['id'];?>"><?php echo $row['nombre']." ".$row['apellido']; ?></option>
                    <?php endwhile; ?>
                </select>
            </fieldset>

            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </form>

    </main>

    <?php incluirTemplate('footer'); ?> 