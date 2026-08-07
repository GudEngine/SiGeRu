<?php
/* Clase usuario para gestionar con API RESTful
 * Permite operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * Requiere conexión a una 
 * ase de datos MySQL
 */

// Configuracion del reporte de errores
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

class Usuario
{
	private $conn;

	// Constructor que recibe la conexión a la base de datos
	public function __construct($conn)
	{
		$this->conn = $conn;
	}

	// Métodos para manejar usuarios
	// Obtener todos los usuarios (método GET, endopoint: /usuarios)
	public function getAllUsuarios()
	{
		$query = "SELECT * FROM usuario";
		$result = mysqli_query($this->conn, $query);
		$usuarios = [];
		while($row = mysqli_fetch_assoc($result)) {
			$usuarios[] = $row;
		}
		return $usuarios;
	}
	// Obtener un usuario por ID  (método GET, endopoint: /usuarios/<id>)
	public function getUsuarioById($cedula){
		$query = "SELECT * FROM usuario WHERE usr_ci = $cedula ";
		$result = mysqli_query($this->conn, $query);
		$usuario = mysqli_fetch_assoc($result);
		return $usuario;
	}
	//asesinar una cuenta por e-mail
	public function deleteUsuarioByCI($cedula){
		
    $cedula = trim($cedula);

    // 2. checando que no me llegue vacía
    if (empty($cedula) || !ctype_digit($cedula)) {
        http_response_code(400); 
        echo json_encode(["mensaje" => "⚠️ Error: La cédula ingresada no es válida. Asegúrese de ingresar solo números sin espacios."]);
        exit;
    }

    // refuerzo a la seguridad
    $cedula = mysqli_real_escape_string($this->conn, $cedula);
		try {
			$query = "DELETE FROM usuario WHERE usr_ci = '$cedula'";
			mysqli_query($this->conn, $query);

			//me gusta mucho esto de affected_rows
			if (mysqli_affected_rows($this->conn) > 0) {
				http_response_code(200); // 200 significa É.X.I.T.O
				echo json_encode((["mensaje" => "Funcionario eliminado con éxito"]));
				exit;
			} else {
            // El query funcionó pero la cédula no existía en la tabla
            http_response_code(400); 
            echo json_encode(["mensaje" => "⚠️ Error: Cédula no registrada."]);
			exit;
        }
        

		} catch (mysqli_sql_exception $e) {
			http_response_code(500); 
			echo json_encode(["mensaje" => "Error interno en el servidor municipal: " . $e->getMessage()]);
			exit;
		}
		 
	}
	//registro del vecinirijillo
	public function addVecino($data) {
    // 1. Verificación básica de existencia
    if (empty($data['usr_ci']) || trim($data['usr_ci']) === "" || 
        empty($data['usr_email']) || trim($data['usr_email']) === "" || 
        empty($data['usr_password']) || trim($data['usr_password']) === "" || 
        empty($data['usr_rol']) || trim($data['usr_rol']) === "") {
        
        http_response_code(400);
        echo json_encode(["mensaje" => "🙅 Error: Todos los campos son obligatorios y no pueden estar vacíos."]);
        exit;
    }

    // 2. Limpieza de espacios con trim
    $usr_ci       = trim($data['usr_ci']);
    $usr_email    = trim($data['usr_email']);
    $usr_password = trim($data['usr_password']);
    $usr_rol      = trim($data['usr_rol']);

    // 3. Validación de Cédula de Identidad (8 dígitos numéricos)
    if (!ctype_digit($usr_ci) || strlen($usr_ci) !== 8) {
        http_response_code(400);
        echo json_encode(["mensaje" => "⚠️ Error: La Cédula de Identidad debe contener únicamente 8 números, sin puntos ni guiones."]);
        exit;
    }

    // 3.5. Validación básica de formato de correo electrónico
    if (!filter_var($usr_email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["mensaje" => "⚠️ Error: El formato del correo electrónico no es válido."]);
        exit;
    }

    // 4. Escape de caracteres para la consulta MySQL
    $usr_ci       = mysqli_real_escape_string($this->conn, $usr_ci);
    $usr_email    = mysqli_real_escape_string($this->conn, $usr_email);
    $usr_password = mysqli_real_escape_string($this->conn, $usr_password);
    $usr_rol      = mysqli_real_escape_string($this->conn, $usr_rol);

    try {
        $query = "INSERT INTO usuario (usr_ci, usr_name, usr_email, usr_password, usr_rol) 
                  VALUES ('$usr_ci', 'vecino', '$usr_email', '$usr_password', '$usr_rol')";
        
        mysqli_query($this->conn, $query);

        http_response_code(201); // 201 = Creado con éxito
        echo json_encode(["mensaje" => "Vecino registrado con éxito."]);
        exit;

    } catch (mysqli_sql_exception $e) {
        $codigo_error_mysql = $e->getCode();
        
        if ($codigo_error_mysql === 1062) {
            http_response_code(400);
            echo json_encode(["mensaje" => "⚠️ Error: La Cédula de Identidad o el email ya se encuentra registrado."]);
        } else {
            http_response_code(500);
            echo json_encode(["mensaje" => "Error interno en el servidor municipal: " . $e->getMessage()]);
        }
        exit;
    }
}




	// Agregar un nuevo usuario (método POST, endopoint: /usuarios)
	public function addUsuario($data){
    // 1. Verificación básica de existencia
		if (empty($data['usr_ci']) || trim($data['usr_ci']) === "" || empty($data['usr_name']) || trim($data['usr_name']) === "" ||	empty($data['usr_email']) || trim($data['usr_email']) === "" ||	empty($data['usr_rol']) || trim($data['usr_rol']) === "" ||	empty($data['usr_telefono']) || trim($data['usr_telefono']) === "")	{
			http_response_code(400);
			echo json_encode(["mensaje" => "🙅 Error: Todos los campos son obligatorios y no pueden estar vacíos."]);
			exit;
		}

		// 2 sacamos los espacios con trim
		$usr_ci       = trim($data['usr_ci']);
		$usr_name     = trim($data['usr_name']);
		$usr_email    = trim($data['usr_email']);
		$usr_rol      = trim($data['usr_rol']);
		$usr_telefono = trim($data['usr_telefono']);

		

		// 3 Checo que  la cédula y el teléfono anden bien
		if (!ctype_digit($usr_ci) || strlen($usr_ci) !== 8 || !ctype_digit($usr_telefono) || strlen($usr_telefono) !== 8) {
			http_response_code(400);
			echo json_encode(["mensaje" => "⚠️ Error: La Cédula de Identidad y el teléfono deben contener únicamente 8 números, sin espacios."]);
			exit;
		}

		// 3.5 que el nombre no tenga números
		if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/', $usr_name)) {
			http_response_code(400);
			echo json_encode(["mensaje" => "⚠️ Error: El nombre solo puede incluir letras y espacios."]);
			exit;
		}
		// 4. Escapamos a una vida feliz con datos para la query una vez validados
		$usr_ci       = mysqli_real_escape_string($this->conn, $usr_ci);
		$usr_name     = mysqli_real_escape_string($this->conn, $usr_name);
		$usr_email    = mysqli_real_escape_string($this->conn, $usr_email);
		$usr_rol      = mysqli_real_escape_string($this->conn, $usr_rol);
		$usr_telefono = mysqli_real_escape_string($this->conn, $usr_telefono);

		try {
			$query = "INSERT INTO usuario (usr_ci, usr_name, usr_email, usr_rol, usr_telefono) 
					VALUES ('$usr_ci', '$usr_name', '$usr_email', '$usr_rol', '$usr_telefono')";
			
			mysqli_query($this->conn, $query);

			http_response_code(201); // 201 = Creado con éxito
			echo json_encode(["mensaje" => "Usuario registrado con éxito."]);
			exit;

		} catch (mysqli_sql_exception $e) {
			$codigoErrorMySQL = $e->getCode();
			//después de tanto validar, la cédula ya registrada debería ser el único error del usuario 
			if ($codigoErrorMySQL === 1062) {
				http_response_code(400);
				echo json_encode(["mensaje" => "⚠️ Error: La Cédula de Identidad ya se encuentra registrada en el sistema."]);
			} else {
				http_response_code(500); // Algo desastrozo ocurrió con el servidor
				echo json_encode(["mensaje" => "Error interno en el servidor municipal: " . $e->getMessage()]);
			}
			exit;
		}
	}
	public function modificarUsuario($data){
		if (empty($data['usr_ci']) || trim($data['usr_ci']) === "" || empty($data['usr_name']) || trim($data['usr_name']) === "" ||	empty($data['usr_email']) || trim($data['usr_email']) === "" ||	empty($data['usr_rol']) || trim($data['usr_rol']) === "" ||	empty($data['usr_telefono']) || trim($data['usr_telefono']) === "")	{
			http_response_code(400);
			echo json_encode(["mensaje" => "🙅 Error: Todos los campos son obligatorios y no pueden estar vacíos."]);
			exit;
		}

		// 2 sacamos los espacios con trim
		$usr_ci       = trim($data['usr_ci']);
		$usr_name     = trim($data['usr_name']);
		$usr_email    = trim($data['usr_email']);
		$usr_rol      = trim($data['usr_rol']);
		$usr_telefono = trim($data['usr_telefono']);

		

		// 3 Checo que  la cédula y el teléfono anden bien
		if (!ctype_digit($usr_ci) || strlen($usr_ci) !== 8 || !ctype_digit($usr_telefono) || strlen($usr_telefono) !== 8) {
			http_response_code(400);
			echo json_encode(["mensaje" => "⚠️ Error: La Cédula de Identidad y el teléfono deben contener únicamente 8 números, sin espacios."]);
			exit;
		}

		// 3.5 que el nombre no tenga números
		if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/', $usr_name)) {
			http_response_code(400);
			echo json_encode(["mensaje" => "⚠️ Error: El nombre solo puede incluir letras y espacios."]);
			exit;
		}
		// 4. Escapamos a una vida feliz con datos para la query una vez validados
		$usr_ci       = mysqli_real_escape_string($this->conn, $usr_ci);
		$usr_name     = mysqli_real_escape_string($this->conn, $usr_name);
		$usr_email    = mysqli_real_escape_string($this->conn, $usr_email);
		$usr_rol      = mysqli_real_escape_string($this->conn, $usr_rol);
		$usr_telefono = mysqli_real_escape_string($this->conn, $usr_telefono);

		try{
			$query = "UPDATE usuario SET 
                usr_name = '$usr_name', 
                usr_email = '$usr_email', 
                usr_rol = '$usr_rol', 
                usr_telefono = '$usr_telefono' 
                WHERE usr_ci = '$usr_ci'";
        
			mysqli_query($this->conn, $query);

			// ¡Acá manejamos el resultado con affected_rows!
			if (mysqli_affected_rows($this->conn) > 0) {
				http_response_code(200);
				echo json_encode(["mensaje" => "Usuario actualizado con éxito."]);
				exit;
			} else {
				// Si dio 0, puede ser porque la cédula no existe o porque guardó los mismos datos sin cambiar nada.
				// para no complicarme le digo que ambas cosas son un error
				http_response_code(400);
				echo json_encode(["mensaje" => "⚠️ Error: No se realizaron cambios (Cédula no registrada o los datos ingresados son idénticos a los actuales)."]);
				exit;
			}
		} catch (mysqli_sql_exception $e) {
			$codigoErrorMySQL = $e->getCode();
	
			if ($codigoErrorMySQL === 1062) {
				http_response_code(400);
				echo json_encode(["mensaje" => "⚠️ Error: El correo electrónico ya se encuentra registrado por otro usuario."]);
			} else {
				// felizmente, creo que no hay errores de usuario que caigan acá
				http_response_code(500);
				echo json_encode(["mensaje" => "Error interno en el servidor municipal: " . $e->getMessage()]);
			}
			exit;
		}
	}

	// Iniciar sesión de usuario (método POST, endopoint: /login)
	public function loginUsuario($data) {
		if (empty($data['usr_email']) || empty($data['usr_password']) || empty($data['usr_rol'])) {
			http_response_code(400);
			echo json_encode(["mensaje" => "⚠️ Debe seleccionar su rol e ingresar e-mail y contraseña."]);
			exit;
		}

		$usr_email    = mysqli_real_escape_string($this->conn, trim($data['usr_email']));
		$usr_rol      = mysqli_real_escape_string($this->conn, trim($data['usr_rol']));
		$usr_password = trim($data['usr_password']);

		// que ese email exista, y en ese rol(después vemos si es meritorio cambiar email por cédula)
		$query = "SELECT * FROM usuario WHERE usr_email = '$usr_email' AND usr_rol = '$usr_rol'";
		$result = mysqli_query($this->conn, $query);

		if (mysqli_num_rows($result) > 0) {
			$usuario = mysqli_fetch_assoc($result);

			if ($usr_password === $usuario['usr_password']) {
				
				if (session_status() === PHP_SESSION_NONE) { session_start(); }
				
				// guarda sesión 
				$_SESSION['usr_ci']     = $usuario['usr_ci'];
				$_SESSION['usr_name']   = $usuario['usr_name'];
				$_SESSION['usr_email']  = $usuario['usr_email'];
				$_SESSION['usr_rol']    = $usuario['usr_rol'];

				http_response_code(200);
				echo json_encode([
					"mensaje" => "Inicio de sesión con éxito.",
					"rol"     => $usuario['usr_rol']
				]);
				exit;

			} else {
				http_response_code(400);
				echo json_encode(["mensaje" => "⚠️ Contraseña incorrecta."]);
				exit;
			}
		} else {
			http_response_code(400);
			echo json_encode(["mensaje" => "⚠️ No existe un usuario con ese e-mail registrado como " . $usr_rol]);
			exit;
		}
	}
}