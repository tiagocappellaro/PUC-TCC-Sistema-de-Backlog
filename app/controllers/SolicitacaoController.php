<?php
require_once __DIR__ . '/../models/Database.php';

class SolicitacaoController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

 public function listarSolicitacoes($usuario = null) {
    return $this->db->listarSolicitacoes($usuario);
}

public function buscarSolicitacoes($filtro, $busca, $usuario = null) {
    return $this->db->buscarSolicitacoes($filtro, $busca, $usuario);
}
    

    public function criarSolicitacao($categoria, $descricao, $beneficios, $subticket, $anexos, $agencia, $prazo_execucao, $usuario) {
        return $this->db->criarSolicitacao($categoria, $descricao, $beneficios, $subticket, $anexos, $agencia, $prazo_execucao, $usuario);
    }

    public function obterSolicitacaoPorId($id) {
        return $this->db->obterSolicitacaoPorId($id);
    }

    public function atualizarSolicitacao($id, $categoria, $descricao, $beneficios, $subticket, $status, $complexidade, $relevancia, $impacto, $prazo_execucao, $usuario) {
        return $this->db->atualizarSolicitacao($id, $categoria, $descricao, $beneficios, $subticket, $status, $complexidade, $relevancia, $impacto, $prazo_execucao, $usuario);
    }

    public function adicionarAnexo($id, $anexos) {
        return $this->db->adicionarAnexo($id, $anexos);
    }

    public function removerAnexo($id, $caminho) {
        return $this->db->removerAnexo($id, $caminho);
    }

    public function obterHistorico($id) {
        return $this->db->obterHistoricoPorId($id);
    }
}
?>