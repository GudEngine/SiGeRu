create database urbin;
use urbin;
create table usuario(
usr_ci int not null primary key,
usr_name varchar(40),
usr_email varchar(50) unique,
usr_rol varchar(21),
usr_password varchar(25) default 'contraseña', /*un default contraseña es claramente preeliminar, se corregirá en la segunda entrega*/
usr_telefono int);
use urbin;

create table contenedor(
cont_id int,
cont_calle varchar(29) ,
cont_estado varchar(10),
primary key(cont_id),
check (cont_estado='funcional' or cont_estado='roto' or cont_estado='desbordado') 
);
create table camion(
cam_matricula char(7) primary key,
cam_tipo varchar(10),
cam_modelo varchar(13),
cam_estado varchar(9),
check (cam_estado='funcional' || cam_estado='roto'));

create table centro_acopio(
acopio_id int primary key,
acopio_operario int,
acopio_tipo_residuo varchar(15),
acopio_calle varchar(15),
acopio_puerta int,
acopio_capacidad int,
acopio_volumen_llenado decimal(3, 2) default 0,
check (acopio_volumen_llenado<=1),
constraint foreign key(acopio_operario) references usuario(usr_ci));

create table ruta(
ruta_id int auto_increment,
ruta_fecha date,
ruta_camion varchar(7),
ruta_acopio int,
volumen_total int,
primary key(ruta_id, ruta_fecha),
constraint foreign key (ruta_camion) references camion(cam_matricula),
constraint foreign key (ruta_acopio) references centro_acopio(acopio_id));

create table ruta_contenedor(
ruta_id int,
ruta_fecha date,
cont_id int,
vaciado boolean,
volumen_cargado int,/*desde bd*/
volumen_descarga int,
primary key (ruta_id, ruta_fecha, cont_id),
constraint fecha_contenedor unique (ruta_fecha, cont_id),
constraint foreign key (ruta_id, ruta_fecha) references ruta(ruta_id, ruta_fecha),
constraint foreign key(cont_id) references contenedor(cont_id));

create table cuadrilla(
cuad_ci int primary key,/* cedula del recolector*/
cuad_cam char(7),
constraint foreign key(cuad_ci) references usuario(usr_ci),
constraint foreign key(cuad_cam) references camion(cam_matricula)
);

create table vertedero(
nom_vertedero varchar(15) primary key,
calle_vertedero varchar(15),
puerta_vertedero int,
capacidad_vertedero int,
volumen_llenado_vertedero decimal(3, 2) default 0,
check (volumen_llenado_vertedero<=1));

INSERT INTO usuario (usr_ci, usr_name, usr_email, usr_rol, usr_password, usr_telefono ) values  
(1, 'pepe', 'pepe@gmail.com', 'administrador', 'contraseña', 1);
INSERT INTO usuario (usr_ci, usr_name, usr_email, usr_rol, usr_password, usr_telefono ) values  
(2, 'carlos', 'carlos@gmail.com', 'recolector', 'contraseña', 1);
INSERT INTO usuario (usr_ci, usr_name, usr_email, usr_rol, usr_password, usr_telefono ) values  
(3, 'dante', 'dante@gmail.com', 'operario_acopio', 'contraseña', 3);

-- 1. USUARIO (4 inserts nuevos)
INSERT INTO usuario (usr_ci, usr_name, usr_email, usr_rol, usr_password, usr_telefono) VALUES  
(10203040, 'Laura Gómez', 'laura.gomez@gmail.com', 'administrador', 'contraseña', 99111222),
(20304050, 'Martín Silva', 'martin.silva@gmail.com', 'recolector', 'contraseña', 99333444),
(30405060, 'Ana Rodríguez', 'ana.rodriguez@gmail.com', 'operario_acopio', 'contraseña', 99555666),
(40506070, 'Gonzalo Pérez', 'gonzalo.perez@gmail.com', 'vecino', 'gp4050', 99777888);

-- 2. CONTENEDOR (4 inserts)
INSERT INTO contenedor (cont_id, cont_calle, cont_estado) VALUES  
(101, 'Av. 18 de Julio', 'funcional'),
(102, 'Bulevar Artigas', 'funcional'),
(103, 'Av. Italia', 'desbordado'),
(104, 'Calle Yi', 'roto'),
(1, 'Yaguarón', 'funcional'),
(2, 'Perú y Bolivia', 'funcional'),
(3, 'Asamblea', 'funcional'),
(4, 'Colombes y Mariscal', 'desbordado');

-- 3. CAMIÓN (4 inserts nuevos)
INSERT INTO camion (cam_matricula, cam_tipo, cam_modelo, cam_estado) VALUES  
( 'STP1234', 'carga', 'Mercedes', 'funcional' ),
( 'SAB5678', 'ruta', 'Scania', 'funcional' ),
( 'SCD9012', 'ruta', 'Volvo', 'roto' ),
( 'SDF3456', 'carga', 'Caterpillar', 'funcional' );

-- 4. RUTA ( inserts - )
INSERT INTO ruta (ruta_id, ruta_fecha, ruta_camion, volumen_total) VALUES 
(1, '2026-05-10', 'SAD0003',  500),
(2, '2026-05-11', 'STP1234',  750);

-- 5. RUTA_CONTENEDOR ()
INSERT INTO ruta_contenedor (ruta_id, ruta_fecha, cont_id, vaciado, volumen_cargado, volumen_descarga) VALUES 
(1, '2026-05-10', 101, true, 200, 200),
(2, '2026-05-11', 102, false, 0, 0),
(1, '2026-05-10', 102, true, 200, 200),
(1, '2026-05-10', 2, true, 200, 200),
(1, '2026-05-10', 3, true, 200, 200),
(2, '2026-05-11', 1, true, 0, 0);

