<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
requireRole(['1']);
$dato = new user();

if (isset($_POST['Registrar'])) {
    $num_doc = $_POST['num_doc'];
    $tipo_doc = $_POST['tipo_doc'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $nombre_completo = $nombres . ' ' . $apellidos;
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $id_rol = $_POST['id_rol'];
    $contraseña = $_POST['contraseña'];

    $dato->crearUsuario($num_doc, $tipo_doc, $nombre_completo, $contraseña, $email, $telefono, $id_rol);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link rel="stylesheet" href="../../assets/css/links/agregarUsuario.css">
    <style>
/* Estilos generales */
.contenedor {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}

form {
    background-color: #ffffff;
    width: 100%;
    max-width: 1300px;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    font-family: 'Arial', sans-serif;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    transition: all 0.3s ease;
}

/* Estilos de etiquetas */
label {
    font-size: 1rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 0.5rem;
}

/* Estilos de los campos */
input[type="text"],
input[type="password"],
input[type="email"],
input[type="tel"],
select {
    padding: 0.6rem;
    font-size: 1rem;
    border-radius: 6px;
    border: 1px solid #ccc;
    width: 100%;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input[type="text"]:focus,
input[type="password"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
select:focus {
    border-color: #0073e6;
    box-shadow: 0px 0px 6px rgba(0, 115, 230, 0.2);
}

/* Botón de enviar */
input[type="submit"] {
    background-color: #0073e6;
    color: white;
    border: none;
    padding: 0.8rem 2rem;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    border-radius: 6px;
    transition: background-color 0.3s ease, transform 0.3s ease;
    align-self: flex-end;
}

input[type="submit"]:hover {
    background-color: #005bb5;
    transform: scale(1.02);
}

/* Estilos de las filas y grupos del formulario */
.form-row {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.form-group {
    flex: 1 1 30%;
    min-width: 200px;
    display: flex;
    flex-direction: column;
}

/* Secciones condicionales */
.conditional-sections {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.conditional-sections > div {
    flex: 1 1 30%;
    min-width: 200px;
    display: flex;
    flex-direction: column;
}

#coordinacionDiv,
#agregarCategoriaDiv,
#categoriasOpcionesDiv {
    display: none;
}

#categoriasOpcionesDiv {
    margin-top: 1rem;
}

/* Estilos para las categorías */
#categoriaOpciones {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.categoria-checkbox {
    flex: 1 1 30%;
    display: flex;
    align-items: center;
}

.categoria-checkbox input[type="checkbox"] {
    margin-right: 0.5rem;
}

@media (max-width: 768px) {
    .form-group, .conditional-sections > div {
        flex: 1 1 100%;
    }

    input[type="submit"] {
        width: 100%;
    }
}
/* Estilos generales */
.contenedor {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
    background-color: #f5f7fa; /* Fondo claro */
    min-height: 100vh;
}

form {
    background-color: #ffffff;
    width: 100%;
    padding: 2rem;
    border-radius: 20px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.15);
    font-family: 'Arial', sans-serif;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    transition: transform 0.3s ease;
}

/* Estilos de etiquetas */
label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #5a5a5a;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Estilos de los campos */
input[type="text"],
input[type="password"],
input[type="email"],
input[type="tel"],
select {
    padding: 0.8rem;
    font-size: 1rem;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
    background-color: #f9f9f9;
    color: #333;
    outline: none;
    transition: all 0.3s ease;
    box-shadow: inset 0px 4px 8px rgba(0, 0, 0, 0.05);
}

input[type="text"]:focus,
input[type="password"]:focus,
input[type="email"]:focus,
input[type="tel"]:focus,
select:focus {
    border-color: #0073e6;
    box-shadow: 0px 0px 6px rgba(0, 115, 230, 0.4);
}

/* Botón de enviar */
input[type="submit"] {
    background-color: #253f56;
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    font-size: 1rem;
    font-weight: bold;
    cursor: pointer;
    border-radius: 20px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    align-self: center;
    width: 50%;
}

input[type="submit"]:hover {
    background-color: #253f56;
    transform: translateY(-2px);
    box-shadow: 0px 4px 12px rgba(0, 91, 181, 0.2);
}

input[type="submit"]:active {
    transform: translateY(1px);
}

/* Estilos de las filas y grupos del formulario */
.form-row {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.form-group {
    flex: 1 1 45%;
    min-width: 200px;
    display: flex;
    flex-direction: column;
}

/* Secciones condicionales */
.conditional-sections {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 1rem;
}

.conditional-sections > div {
    flex: 1 1 45%;
    min-width: 200px;
    display: flex;
    flex-direction: column;
}

/* Opciones de categorías */
#categoriasOpcionesDiv {
    margin-top: 1rem;
    padding: 1rem;
    background-color: #f1f1f1;
    border-radius: 12px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.05);
}

/* Estilos para las categorías */
#categoriaOpciones {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.categoria-checkbox {
    flex: 1 1 45%;
    display: flex;
    align-items: center;
}

.categoria-checkbox input[type="checkbox"] {
    margin-right: 0.5rem;
    transform: scale(1.2);
}

@media (max-width: 768px) {
    .form-group, .conditional-sections > div {
        flex: 1 1 100%;
    }

    input[type="submit"] {
        width: 100%;
    }
}

    </style>
</head>
<body>
    <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php';
    ?>
    <div class="contenedor">
        <form action="" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="documento">Número de documento:</label>
                    <input type="text" id="documento" name="num_doc" placeholder="Ingrese el número de documento" required>
                </div>
                <div class="form-group">
                    <label for="tipo_doc">Tipo de documento</label>
                    <select name="tipo_doc" id="tipo_doc">
                        <option value="Cédula de ciudadanía">Cédula de ciudadanía</option>
                        <option value="Cédula de extranjería">Cédula de extranjería</option>
                        <option value="Pasaporte">Pasaporte</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" name="contraseña" id="contraseña" required placeholder="Ingrese la contraseña">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombres:</label>
                    <input type="text" id="nombre" name="nombres" placeholder="Ingrese el primer nombre" required>
                </div>
                <div class="form-group">
                    <label for="Apellido">Apellidos:</label>
                    <input type="text" id="Apellido" name="apellidos" placeholder="Ingrese el primer Apellido" required>
                </div>
                <div class="form-group">
                    <label for="email">Correo:</label>
                    <input type="email" name="email" id="email" required placeholder="Ingrese su correo">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="tel" name="telefono" id="telefono" placeholder="Ingrese un número de teléfono" required>
                </div>
                <div class="form-group">
                    <label for="rol">Rol:</label>
                    <select id="rol" name="id_rol" required onchange="mostrarCoordinaciones()">
                        <option value="">Seleccione un rol</option>
                        <option value="1">Administrador</option>
                        <option value="2">Gestor</option>
                        <option value="3">Planeación</option>
                        <option value="4">SENNOVA</option>
                        <option value="5">Tecnoparque</option>
                        <option value="6">Tecnoacademia</option>
                    </select>
                </div>
            </div>

            <input type="submit" value="Crear Usuario" name="Registrar">
        </form>
    </div>
    <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php';
    ?>

</body>
</html>
