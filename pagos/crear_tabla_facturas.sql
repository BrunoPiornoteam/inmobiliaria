-- Crear la tabla facturas si no existe
CREATE TABLE IF NOT EXISTS facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    fecha_emision DATE NOT NULL,
    concepto TEXT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente', 'pagada', 'cancelada') NOT NULL DEFAULT 'pendiente',
    tipo_factura CHAR(1) NOT NULL DEFAULT 'A',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
