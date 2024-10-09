<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'backlog';
    private $user = 'root';
    private $password = '';
    public $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname . ";charset=utf8", $this->user, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }

    public function verificarUsuario($usuario, $senha) {
        $sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha'])) {
            return $user; // Retorna o usuário completo para acessar o campo agência
        } else {
            return false;
        }
    }

public function buscarSolicitacoes($filtro, $busca, $usuario = null) {
    $allowedFilters = ['categoria', 'descricao', 'id'];
    if (!in_array($filtro, $allowedFilters)) {
        throw new InvalidArgumentException("Filtro inválido.");
    }

    $buscaLike = "%$busca%";

    if ($usuario) {
        $sql = "SELECT * FROM solicitacoes WHERE $filtro LIKE :busca AND criado_por = :usuario";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':busca', $buscaLike, PDO::PARAM_STR);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    } else {
        $sql = "SELECT * FROM solicitacoes WHERE $filtro LIKE :busca";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':busca', $buscaLike, PDO::PARAM_STR);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function listarSolicitacoes($usuario = null) {
        if ($usuario) {
            $stmt = $this->pdo->prepare("SELECT * FROM solicitacoes WHERE criado_por = :usuario");
            $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $stmt = $this->pdo->query('SELECT * FROM solicitacoes');
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function criarSolicitacao($categoria, $descricao, $beneficios, $subticket, $anexos, $agencia, $prazo_execucao, $usuario) {
        // Armazena os anexos como JSON
        $anexos_json = json_encode($anexos);

        // Se o prazo_execucao não estiver preenchido, defina como NULL
        if (empty($prazo_execucao)) {
            $prazo_execucao = null;
        }

        $stmt = $this->pdo->prepare("INSERT INTO solicitacoes (categoria, descricao, beneficios, subticket, anexos, agencia, complexidade, relevancia, impacto, prazo_execucao, criado_por) 
                                      VALUES (:categoria, :descricao, :beneficios, :subticket, :anexos, :agencia, '3', '3', '3', :prazo_execucao, :criado_por)");
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':beneficios', $beneficios);
        $stmt->bindParam(':subticket', $subticket);
        $stmt->bindParam(':anexos', $anexos_json);
        $stmt->bindParam(':agencia', $agencia); // Adicionado para inserir a agência
        $stmt->bindParam(':prazo_execucao', $prazo_execucao); // Novo parâmetro
        $stmt->bindParam(':criado_por', $usuario); // Nome do usuário que criou a solicitação
        $stmt->execute();

        // Obter o ID da nova solicitação
        $id = $this->pdo->lastInsertId();

        return $id;
    }

    public function obterSolicitacaoPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM solicitacoes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarSolicitacao($id, $categoria, $descricao, $beneficios, $subticket, $status, $complexidade, $relevancia, $impacto, $prazo_execucao, $usuario) {
        // Busca a solicitação atual para comparar alterações
        $solicitacaoAtual = $this->obterSolicitacaoPorId($id);

        // Tratar o prazo de execução como NULL se for '0000-00-00' ou vazio
        $prazo_execucao = (!empty($prazo_execucao) && $prazo_execucao !== '0000-00-00') ? $prazo_execucao : null;

        if (!$solicitacaoAtual) {
            throw new Exception("Solicitação não encontrada.");
        }

        // Lógica para lidar com anexos
        $anexosExistentes = json_decode($solicitacaoAtual['anexos'], true) ?: [];
        $novosAnexos = [];

        // Adicionar novos anexos se houver
        if (isset($_FILES['anexos'])) {
            // Processa os novos anexos
            $totalArquivos = count($_FILES['anexos']['name']);
            for ($i = 0; $i < $totalArquivos; $i++) {
                $nomeArquivo = basename($_FILES['anexos']['name'][$i]);
                $caminhoDestino = 'uploads/' . uniqid() . '_' . $nomeArquivo;
                if (move_uploaded_file($_FILES['anexos']['tmp_name'][$i], $caminhoDestino)) {
                    $novosAnexos[] = $caminhoDestino;
                }
            }
        }

        // Combine os anexos existentes com os novos
        $anexosAtualizados = array_merge($anexosExistentes, $novosAnexos);
        $anexos_json = json_encode($anexosAtualizados, JSON_UNESCAPED_UNICODE);

        // Atualiza a solicitação
        $stmt = $this->pdo->prepare("UPDATE solicitacoes SET categoria = :categoria, descricao = :descricao, beneficios = :beneficios, subticket = :subticket, status = :status, complexidade = :complexidade, relevancia = :relevancia, impacto = :impacto, prazo_execucao = :prazo_execucao, anexos = :anexos WHERE id = :id");
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':beneficios', $beneficios);
        $stmt->bindParam(':subticket', $subticket);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':complexidade', $complexidade);
        $stmt->bindParam(':relevancia', $relevancia);
        $stmt->bindParam(':impacto', $impacto);
        $stmt->bindParam(':prazo_execucao', $prazo_execucao); // Novo parâmetro
        $stmt->bindParam(':anexos', $anexos_json); // Adiciona anexos atualizados
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Determinar quais campos foram alterados
        $alteracoes = [];
        foreach (['categoria', 'descricao', 'beneficios', 'status', 'complexidade', 'relevancia', 'impacto', 'prazo_execucao'] as $campo) {
            if ($solicitacaoAtual[$campo] != ${$campo}) {
                $alteracoes[$campo] = ['antes' => $solicitacaoAtual[$campo], 'depois' => ${$campo}];
            }
        }

        // Atualiza o histórico apenas se houve alterações
        if (!empty($alteracoes)) {
            $historicoAtual = $solicitacaoAtual['historico'] ? json_decode($solicitacaoAtual['historico'], true) : [];
            $novaEntrada = [
                "usuario" => $usuario,
                "alteracao" => date("d/m/Y H:i"),
                "campos" => $alteracoes
            ];
            $historicoAtual[] = $novaEntrada;

            $historico_json = json_encode($historicoAtual, JSON_UNESCAPED_UNICODE);
            $stmt = $this->pdo->prepare("UPDATE solicitacoes SET historico = :historico WHERE id = :id");
            $stmt->bindParam(':historico', $historico_json);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    }

    public function adicionarAnexo($id, $anexos) {
        $solicitacao = $this->obterSolicitacaoPorId($id);
        if ($solicitacao) {
            $anexosExistentes = $solicitacao['anexos'] ? json_decode($solicitacao['anexos'], true) : [];
            $anexosAtualizados = array_merge($anexosExistentes, $anexos);
            $anexos_json = json_encode($anexosAtualizados, JSON_UNESCAPED_UNICODE);
            $stmt = $this->pdo->prepare("UPDATE solicitacoes SET anexos = :anexos WHERE id = :id");
            $stmt->bindParam(':anexos', $anexos_json);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    }

    public function removerAnexo($id, $caminho) {
        // Remove o anexo do banco de dados e do diretório
        $stmt = $this->pdo->prepare("SELECT anexos FROM solicitacoes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($solicitacao) {
            $anexos = json_decode($solicitacao['anexos'], true);
            if (!is_array($anexos)) {
                $anexos = [];
            }
            $anexos = array_filter($anexos, function($anexo) use ($caminho) {
                return $anexo !== $caminho;
            });

            // Atualiza a tabela no banco de dados
            $stmt = $this->pdo->prepare("UPDATE solicitacoes SET anexos = :anexos WHERE id = :id");
            $anexos_json = json_encode(array_values($anexos), JSON_UNESCAPED_UNICODE);
            $stmt->bindParam(':anexos', $anexos_json);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Remove o arquivo do diretório
            if (file_exists($caminho)) {
                unlink($caminho);
            }
        }
    }

    public function obterHistoricoPorId($id) {
        $stmt = $this->pdo->prepare("SELECT historico FROM solicitacoes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retornar um array vazio se não houver histórico
        return !empty($solicitacao['historico']) ? json_decode($solicitacao['historico'], true) : [];
    }

    // Método para listar Tickets por Status
    public function listarTicketsPorStatus() {
        $stmt = $this->pdo->query("
            SELECT status, COUNT(*) as total
            FROM solicitacoes
            GROUP BY status
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para listar Tickets por Categoria
    public function listarTicketsPorCategoria() {
        $stmt = $this->pdo->query("
            SELECT categoria, COUNT(*) as total
            FROM solicitacoes
            GROUP BY categoria
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para listar Tickets por Agência
    public function listarTicketsPorAgencia() {
        $stmt = $this->pdo->query("
            SELECT agencia, COUNT(*) as total
            FROM solicitacoes
            GROUP BY agencia
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para ranking de Usuários
    public function rankingUsuarios() {
        $stmt = $this->pdo->query("
            SELECT criado_por as usuario, COUNT(*) as total
            FROM solicitacoes
            GROUP BY criado_por
            ORDER BY total DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Método para calcular o Tempo Médio de Resolução
    public function tempoMedioResolucao() {
        $stmt = $this->pdo->query("
            SELECT 
                id as ticket, 
                AVG(DATEDIFF(COALESCE(prazo_execucao, NOW()), criado_em)) as dias
            FROM solicitacoes
            GROUP BY id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
