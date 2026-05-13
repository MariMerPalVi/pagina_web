<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function falex_services(): array
{
    return [
        ['Cuellos escolares', 'Fabricamos cuellos cómodos, resistentes y personalizados para uniformes escolares.'],
        ['Cuellos empresariales', 'Soluciones para prendas corporativas con colores y acabados alineados a tu marca.'],
        ['Bordados de sellos institucionales', 'Bordamos sellos, escudos, nombres y logotipos con presentación profesional.'],
        ['Prendas en telas de punto', 'Confeccionamos prendas para hombres, mujeres y niños con materiales seleccionados.'],
        ['Equipos deportivos sublimados', 'Uniformes deportivos con diseños vivos, personalizados y listos para competir.'],
        ['Equipos deportivos estampados', 'Camisetas y conjuntos con nombres, números, marcas y detalles estampados.'],
        ['Camisetas personalizadas', 'Camisetas para eventos, promociones, grupos, equipos, empresas y marcas.'],
        ['Uniformes institucionales', 'Prendas para colegios, escuelas, empresas y organizaciones que cuidan su imagen.'],
        ['Prendas para hombres, mujeres y niños', 'Producción textil versátil para diferentes públicos, tallas y necesidades.'],
    ];
}

function public_categories(): array
{
    try {
        return db()->query('SELECT id, nombre FROM categorias WHERE estado = "activo" ORDER BY nombre')->fetchAll();
    } catch (Throwable) {
        return [];
    }
}

function public_products(int $categoryId = 0): array
{
    try {
        $sql = 'SELECT p.*, c.nombre AS categoria
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                WHERE p.estado = "activo"';
        $params = [];

        if ($categoryId > 0) {
            $sql .= ' AND p.categoria_id = ?';
            $params[] = $categoryId;
        }

        $sql .= ' ORDER BY p.fecha_creacion DESC';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    } catch (Throwable) {
        return [];
    }
}

function public_gallery_items(): array
{
    try {
        $sql = 'SELECT p.id, p.nombre, p.descripcion, p.imagen, c.nombre AS categoria
                FROM productos p
                INNER JOIN categorias c ON c.id = p.categoria_id
                WHERE p.estado = "activo"
                  AND p.imagen IS NOT NULL
                  AND p.imagen <> ""
                ORDER BY p.fecha_actualizacion DESC, p.fecha_creacion DESC';

        return db()->query($sql)->fetchAll();
    } catch (Throwable) {
        return [];
    }
}
