-- BD y SPs
CREATE DATABASE IF NOT EXISTS inventario CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE inventario;
CREATE TABLE IF NOT EXISTS rol (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(20) NOT NULL UNIQUE);
CREATE TABLE IF NOT EXISTS usuario (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(100) NOT NULL, correo VARCHAR(100) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, rol_id INT NOT NULL, status BOOLEAN NOT NULL DEFAULT TRUE, FOREIGN KEY (rol_id) REFERENCES rol(id));
CREATE TABLE IF NOT EXISTS producto (id INT AUTO_INCREMENT PRIMARY KEY, nombre VARCHAR(100) NOT NULL, cantidad INT NOT NULL DEFAULT 0, activo BOOLEAN NOT NULL DEFAULT TRUE, CHECK (cantidad>=0), UNIQUE KEY uq_producto_nombre(nombre));
CREATE TABLE IF NOT EXISTS movimiento (id INT AUTO_INCREMENT PRIMARY KEY, producto_id INT NOT NULL, usuario_id INT NOT NULL, tipo ENUM('ENTRADA','SALIDA') NOT NULL, cantidad INT NOT NULL, fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (producto_id) REFERENCES producto(id), FOREIGN KEY (usuario_id) REFERENCES usuario(id), CHECK (cantidad>0));
INSERT IGNORE INTO rol(nombre) VALUES ('ADMIN'),('ALMACENISTA');
INSERT IGNORE INTO usuario (nombre,correo,password,rol_id,status) SELECT 'Administrador','admin@empresa.com','admin123',r.id,TRUE FROM rol r WHERE r.nombre='ADMIN' LIMIT 1;
INSERT IGNORE INTO usuario (nombre,correo,password,rol_id,status) SELECT 'Almacenista','almac@empresa.com','almac123',r.id,TRUE FROM rol r WHERE r.nombre='ALMACENISTA' LIMIT 1;
INSERT IGNORE INTO producto(nombre,cantidad,activo) VALUES ('Laptop',10,TRUE),('Mouse',25,TRUE),('Teclado',15,TRUE);
CREATE OR REPLACE VIEW vw_inventario AS SELECT p.id,p.nombre,p.cantidad,p.activo,CASE WHEN p.activo THEN 'ACTIVO' ELSE 'INACTIVO' END estatus FROM producto p;
CREATE OR REPLACE VIEW vw_inventario_activo AS SELECT * FROM vw_inventario WHERE activo=TRUE;
CREATE OR REPLACE VIEW vw_inventario_inactivo AS SELECT * FROM vw_inventario WHERE activo=FALSE;
CREATE OR REPLACE VIEW vw_productos_para_salida AS SELECT id,nombre,cantidad FROM producto WHERE activo=TRUE;
CREATE OR REPLACE VIEW vw_movimientos AS SELECT m.id,m.fecha,m.tipo,m.cantidad,p.id producto_id,p.nombre producto,u.id usuario_id,u.nombre usuario,r.nombre rol FROM movimiento m JOIN producto p ON p.id=m.producto_id JOIN usuario u ON u.id=m.usuario_id JOIN rol r ON r.id=u.rol_id ORDER BY m.fecha DESC,m.id DESC;

DELIMITER $$
DROP PROCEDURE IF EXISTS sp_login $$
CREATE PROCEDURE sp_login(IN p_correo VARCHAR(100), IN p_password VARCHAR(255))
BEGIN
  DECLARE v_uid INT; DECLARE v_status BOOLEAN;
  SELECT u.id,u.status INTO v_uid,v_status FROM usuario u WHERE u.correo=p_correo AND u.password=p_password LIMIT 1;
  IF v_uid IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Credenciales inválidas.'; END IF;
  IF v_status=FALSE THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario inactivo. Contacte al administrador.'; END IF;
  SELECT u.id,u.nombre,u.correo,r.nombre rol,u.status FROM usuario u JOIN rol r ON r.id=u.rol_id WHERE u.id=v_uid;
END $$

DROP PROCEDURE IF EXISTS sp_registrar_producto $$
CREATE PROCEDURE sp_registrar_producto(IN p_user_id INT, IN p_nombre VARCHAR(100))
BEGIN
  DECLARE v_rol VARCHAR(20);
  DECLARE EXIT HANDLER FOR 1062 BEGIN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El producto ya existe.'; END;
  SELECT r.nombre INTO v_rol FROM usuario u JOIN rol r ON r.id=u.rol_id WHERE u.id=p_user_id AND u.status=TRUE LIMIT 1;
  IF v_rol IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no válido o inactivo.'; END IF;
  IF v_rol<>'ADMIN' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No autorizado: solo ADMIN puede registrar productos.'; END IF;
  INSERT INTO producto(nombre,cantidad,activo) VALUES (p_nombre,0,TRUE);
END $$

DROP PROCEDURE IF EXISTS sp_dar_baja_producto $$
CREATE PROCEDURE sp_dar_baja_producto(IN p_user_id INT, IN p_producto_id INT)
BEGIN
  DECLARE v_rol VARCHAR(20); DECLARE v_activo BOOLEAN;
  SELECT r.nombre INTO v_rol FROM usuario u JOIN rol r ON r.id=u.rol_id WHERE u.id=p_user_id AND u.status=TRUE LIMIT 1;
  IF v_rol IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no válido o inactivo.'; END IF;
  IF v_rol<>'ADMIN' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No autorizado: solo ADMIN puede dar de baja productos.'; END IF;
  SELECT activo INTO v_activo FROM producto WHERE id=p_producto_id;
  IF v_activo IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Producto no encontrado.'; END IF;
  IF v_activo=FALSE THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El producto ya está inactivo.'; END IF;
  UPDATE producto SET activo=FALSE WHERE id=p_producto_id;
END $$

DROP PROCEDURE IF EXISTS sp_reactivar_producto $$
CREATE PROCEDURE sp_reactivar_producto(IN p_user_id INT, IN p_producto_id INT)
BEGIN
  DECLARE v_rol VARCHAR(20); DECLARE v_activo BOOLEAN;
  SELECT r.nombre INTO v_rol FROM usuario u JOIN rol r ON r.id=u.rol_id WHERE u.id=p_user_id AND u.status=TRUE LIMIT 1;
  IF v_rol IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no válido o inactivo.'; END IF;
  IF v_rol<>'ADMIN' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No autorizado: solo ADMIN puede reactivar productos.'; END IF;
  SELECT activo INTO v_activo FROM producto WHERE id=p_producto_id;
  IF v_activo IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Producto no encontrado.'; END IF;
  IF v_activo=TRUE THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El producto ya está activo.'; END IF;
  UPDATE producto SET activo=TRUE WHERE id=p_producto_id;
END $$

DROP PROCEDURE IF EXISTS sp_entrada_producto $$
CREATE PROCEDURE sp_entrada_producto(IN p_user_id INT, IN p_producto_id INT, IN p_cantidad INT)
BEGIN
  DECLARE v_rol VARCHAR(20); DECLARE v_existente INT; DECLARE v_activo BOOLEAN;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
  IF p_cantidad IS NULL OR p_cantidad<=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Entrada inválida: la cantidad debe ser mayor a 0 (en ENTRADA no se permite disminuir).'; END IF;
  SELECT r.nombre INTO v_rol FROM usuario u JOIN rol r ON r.id=u.rol_id WHERE u.id=p_user_id AND u.status=TRUE LIMIT 1;
  IF v_rol IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no válido o inactivo.'; END IF;
  IF v_rol<>'ADMIN' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No autorizado: solo ADMIN puede hacer ENTRADAS.'; END IF;
  START TRANSACTION;
  SELECT cantidad,activo INTO v_existente,v_activo FROM producto WHERE id=p_producto_id FOR UPDATE;
  IF v_existente IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Producto no encontrado.'; END IF;
  IF v_activo=FALSE THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No se puede hacer ENTRADA a un producto inactivo.'; END IF;
  UPDATE producto SET cantidad=cantidad+p_cantidad WHERE id=p_producto_id;
  INSERT INTO movimiento(producto_id,usuario_id,tipo,cantidad) VALUES (p_producto_id,p_user_id,'ENTRADA',p_cantidad);
  COMMIT;
END $$

DROP PROCEDURE IF EXISTS sp_salida_producto $$
CREATE PROCEDURE sp_salida_producto(IN p_user_id INT, IN p_producto_id INT, IN p_cantidad INT)
BEGIN
  DECLARE v_rol VARCHAR(20); DECLARE v_existente INT; DECLARE v_activo BOOLEAN;
  DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;
  IF p_cantidad IS NULL OR p_cantidad<=0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Salida inválida: la cantidad debe ser mayor a 0.'; END IF;
  SELECT r.nombre INTO v_rol FROM usuario u JOIN rol r ON r.id=u.rol_id WHERE u.id=p_user_id AND u.status=TRUE LIMIT 1;
  IF v_rol IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no válido o inactivo.'; END IF;
  IF v_rol<>'ALMACENISTA' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No autorizado: solo ALMACENISTA puede realizar SALIDAS.'; END IF;
  START TRANSACTION;
  SELECT cantidad,activo INTO v_existente,v_activo FROM producto WHERE id=p_producto_id FOR UPDATE;
  IF v_existente IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Producto no encontrado.'; END IF;
  IF v_activo=FALSE THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No se puede sacar material de un producto inactivo.'; END IF;
  IF v_existente<p_cantidad THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Stock insuficiente: no se puede sacar una cantidad mayor a la existente.'; END IF;
  UPDATE producto SET cantidad=cantidad-p_cantidad WHERE id=p_producto_id;
  INSERT INTO movimiento(producto_id,usuario_id,tipo,cantidad) VALUES (p_producto_id,p_user_id,'SALIDA',p_cantidad);
  COMMIT;
END $$

DROP PROCEDURE IF EXISTS sp_historial_movimientos $$
CREATE PROCEDURE sp_historial_movimientos(IN p_user_id INT, IN p_tipo VARCHAR(10))
BEGIN
  DECLARE v_rol VARCHAR(20);
  SELECT r.nombre INTO v_rol FROM usuario u JOIN rol r ON r.id=u.rol_id WHERE u.id=p_user_id AND u.status=TRUE LIMIT 1;
  IF v_rol IS NULL THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Usuario no válido o inactivo.'; END IF;
  IF v_rol<>'ADMIN' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='No autorizado: solo ADMIN puede consultar el histórico.'; END IF;
  IF p_tipo IS NULL OR UPPER(p_tipo) IN ('','TODOS') THEN SELECT * FROM vw_movimientos;
  ELSEIF UPPER(p_tipo)='ENTRADA' THEN SELECT * FROM vw_movimientos_entrada;
  ELSEIF UPPER(p_tipo)='SALIDA' THEN SELECT * FROM vw_movimientos_salida;
  ELSE SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='Tipo inválido. Use: ENTRADA, SALIDA o NULL/TODOS.'; END IF;
END $$
DELIMITER ;
