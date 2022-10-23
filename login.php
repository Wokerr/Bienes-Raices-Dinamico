<?php 
    require 'includes/config/database.php';
    $db = conectarDB();

    $errores = [];
    //Autenticar usuario
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // echo"<pre>";
        // var_dump($_POST);
        // echo"</pre>";
        
        $email= mysqli_real_escape_string($db, filter_var($_POST['email'], FILTER_VALIDATE_EMAIL));

        $password = mysqli_real_escape_string($db, $_POST['password']);

        if(!$email) {
            $errores[] = "El email es obligatorio o no es valido";
        }

        if(!$password) {
            $errores[] = "El password es obligatorio";
        }

        if(empty($errore)) {
            $query = "SELECT * FROM usuarios WHERE email = '${email}'";
            $resultado = mysqli_query($db, $query);

            if( $resultado->num_rows ) {
                $usuario = mysqli_fetch_assoc($resultado);
                
                $auth = password_verify($password, $usuario['password']);

                if($auth) {
                   session_start();
                    $_SESSION['usuario'] = $usuario['email'];
                    $_SESSION['login'] = true;


                    header('location: /admin');
                } else {
                   $errores[] = 'El password es incorrecto';
                }
            }else {
                $errores[] = 'El Usuario no existe';
            }
        }
    }


    //Incluye Header
    require 'includes/funciones.php';
    incluirTemplate('header'); ?>
    
    <main class="contenedor seccion contenido-centrado">
        <h1>Iniciar Sesion</h1>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>

        <form method="POST" class="fomulario">
        <fieldset>
                <legend>Email y Password</legend>

                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" placeholder="Tu Email">

                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Tu Password">
            </fieldset>
            <input type="submit" value="Iniciar Sesion" class="boton boton-verde">
        </form>
    </main>

<?php
incluirTemplate('footer'); 
?>   