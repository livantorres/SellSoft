<?php

namespace SellSoft\Controllers;

use SellSoft\Core\Controller;
use SellSoft\Core\Database;

class LocationController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    public function getDepartamentos()
    {
        $pais_id = $_GET['pais_id'] ?? 1; // Default to Colombia
        $stmt = $this->db->prepare("SELECT id, nombre FROM departamentos WHERE pais_id = :pais_id ORDER BY nombre ASC");
        $stmt->execute(['pais_id' => $pais_id]);
        $departamentos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $departamentos]);
    }

    public function getCiudades()
    {
        $departamento_id = $_GET['departamento_id'] ?? 0;
        $stmt = $this->db->prepare("SELECT id, nombre FROM ciudades WHERE departamento_id = :departamento_id ORDER BY nombre ASC");
        $stmt->execute(['departamento_id' => $departamento_id]);
        $ciudades = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $ciudades]);
    }
}
