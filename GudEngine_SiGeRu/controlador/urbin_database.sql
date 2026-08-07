create database urbin;
use urbin;
create table usuario(
usr_ci int primary key,
usr_name varchar(40) not null,
usr_apellido varchar(40) not null,
usr_email varchar(50) unique,
usr_rol varchar(21),
usr_edad int,
usr_municipio varchar(2),/*esto preguntá si es relevante, seguramente termine borrado*/
usr_password varchar(25) default 'contraseña', /*un default contraseña es claramente preeliminar, es para los trabajadores registrados por
la backoffice*/
usr_telefono int/*preguntá si es meritorio hacer teléfono como multivluado*/);
use urbin;

create table administra_municipalidad(
usr_ci int primary key,/*preguntar si un admin debería poder administrar más de una municipalidad*/
mun_id int,
usr_rol varchar(21) not null default 'administrador_municipal',
constraint foreign key (usr_ci, usr_rol) references usuario(usr_ci, usr_rol)
on delete cascade,
/*esto estaría buenisimo porque en la parte de programar me ahorra muchos delete desde backend, la idea es que si yo borro al administrador
de la base de datos, esto hace automaticamente el delete de las tuplas que lo tengan a él en esta tabla, pedir permiso para usarlo*/
constraint foreign key(mun_id) references municipalidad(mun_id)
on delete cascade,
check (usr_rol = 'administrador_municipal')
);

create table cuadrilla(
usr_ci int primary key,/*preguntá si corresponde un id de cuadrilla*/
cam_matricula char(7),
usr_rol varchar (21),
constraint foreign key (usr_ci, usr_rol) references usuario(usr_ci, usr_rol) 
on delete cascade,
constraint foreign key (cam_matricula) references camion(cam_matricula)
on delete cascade
);

create table asignar_cuadrilla(
recolector_ci int,/*preguntar si corresponde un id de cuadrilla, para que el camión lo puedan usar varias cuadrillas*/
municipal_ci int,
asignacion_fecha datetime /*por si el admin se da cuenta de que lo asignó al camión equivocado*/,
primary key(recolector_ci, asignacion_fecha),
constraint foreign key (recolector_ci) references cuadrilla(usr_ci),
constraint foreign key (municipal_ci) references administra_municipalidad(usr_ci));


create table contenedor(
cont_id int,
cont_latitud DECIMAL(10, 8) NOT NULL,
cont_longitud DECIMAL(11, 8) NOT NULL,
cont_calle varchar(29) ,
cont_estado varchar(10),
cont_tipo varchar(9),
cont_funcionando boolean, /*la letra pide contenedores de repusto, asumo que esos no están activos */
primary key(cont_id),
check (cont_estado='funcional' or cont_estado='roto' or cont_estado='desbordado')
/*check (cont_tipo='volqueta' or cont_tipo='reciclaje' or cont_tipo='mezclados')  preguntar al profe los nombres de esto*/
);

create table camion(
cam_matricula char(7) primary key,
cam_tipo varchar(10),
cam_capacidad int,
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

