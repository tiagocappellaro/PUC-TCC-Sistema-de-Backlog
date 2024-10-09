<?php
require_once __DIR__ . '/../models/Database.php';

class LoginController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function autenticar($usuario, $senha) {
        return $this->db->verificarUsuario($usuario, $senha);
    }
}
?>
