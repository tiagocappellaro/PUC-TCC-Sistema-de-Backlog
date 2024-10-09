<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: app/views/login.php');
    exit();
}

require_once 'app/controllers/SolicitacaoController.php';
$controller = new SolicitacaoController();

// Inicializa as solicitações sem filtro de usuário
$solicitacoes = $controller->listarSolicitacoes(); // Carrega todas as solicitações

// Verifica se a solicitação é POST para criação ou atualização de ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['categoria'], $_POST['descricao'], $_POST['beneficios'])) {
        // Verifica se é uma atualização
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $status = $_POST['status'];
            $complexidade = $_POST['complexidade'];
            $relevancia = $_POST['relevancia'];
            $impacto = $_POST['impacto'];
            $prazo_execucao = $_POST['prazo_execucao']; // Novo campo

            $usuario = $_SESSION['usuario']; // Captura o usuário logado

            // Adicionar anexos se houver
            $anexos = [];
            if (isset($_FILES['anexos'])) {
                $totalArquivos = count($_FILES['anexos']['name']);
                for ($i = 0; $i < $totalArquivos; $i++) {
                    $nomeArquivo = basename($_FILES['anexos']['name'][$i]);
                    $caminhoDestino = 'uploads/' . uniqid() . '_' . $nomeArquivo;
                    if (move_uploaded_file($_FILES['anexos']['tmp_name'][$i], $caminhoDestino)) {
                        $anexos[] = $caminhoDestino;
                    }
                }
            }

            $controller->atualizarSolicitacao($id, $_POST['categoria'], $_POST['descricao'], $_POST['beneficios'], $id, $status, $complexidade, $relevancia, $impacto, $prazo_execucao, $usuario);

            // Adiciona os anexos após a atualização
            if (!empty($anexos)) {
                $controller->adicionarAnexo($id, $anexos);
            }

        } else {
            // Criação de nova solicitação
            $categoria = $_POST['categoria'];
            $descricao = $_POST['descricao'];
            $beneficios = $_POST['beneficios'];
            $subticket = 0; // Definindo o subticket como 0, você pode ajustar conforme necessário
            $agencia = $_SESSION['agencia']; // Captura a agência da sessão
            $prazo_execucao = $_POST['prazo_execucao']; // Novo campo
            $usuario = $_SESSION['usuario']; // Captura o usuário da sessão

            // Processamento dos arquivos anexados
            $anexos = [];
            if (isset($_FILES['anexos'])) {
                $totalArquivos = count($_FILES['anexos']['name']);
                for ($i = 0; $i < $totalArquivos; $i++) {
                    $nomeArquivo = basename($_FILES['anexos']['name'][$i]);
                    $caminhoDestino = 'uploads/' . uniqid() . '_' . $nomeArquivo;
                    if (move_uploaded_file($_FILES['anexos']['tmp_name'][$i], $caminhoDestino)) {
                        $anexos[] = $caminhoDestino;
                    }
                }
            }

            $controller->criarSolicitacao($categoria, $descricao, $beneficios, $subticket, $anexos, $agencia, $prazo_execucao, $usuario); // Passando o nome do usuário logado
        }
        // Redireciona para a página inicial após criar ou atualizar o ticket
        header("Location: index.php");
        exit();
    }

    // Verifica se é uma requisição para remover anexo
    if (isset($_POST['acao']) && $_POST['acao'] == 'removerAnexo') {
        if (isset($_POST['id']) && isset($_POST['caminho'])) {
            $id = $_POST['id'];
            $caminho = $_POST['caminho'];
            $controller->removerAnexo($id, $caminho);
            // Redireciona para a página inicial após remover o anexo
            header("Location: index.php");
            exit();
        }
    }

    // Verifica se é uma requisição para adicionar anexos na edição
    if (isset($_POST['acao']) && $_POST['acao'] == 'adicionarAnexo') {
        if (isset($_POST['id']) && isset($_FILES['anexos'])) {
            $id = $_POST['id'];
            $anexos = [];
            $totalArquivos = count($_FILES['anexos']['name']);
            for ($i = 0; $i < $totalArquivos; $i++) {
                $nomeArquivo = basename($_FILES['anexos']['name'][$i]);
                $caminhoDestino = 'uploads/' . uniqid() . '_' . $nomeArquivo;
                if (move_uploaded_file($_FILES['anexos']['tmp_name'][$i], $caminhoDestino)) {
                    $anexos[] = $caminhoDestino;
                }
            }
            $controller->adicionarAnexo($id, $anexos);
            header("Location: index.php");
            exit();
        }
    }
}

// Função para renderizar a tabela de solicitações
function renderizarTabela($solicitacoes) {
    $output = '';
    if (!empty($solicitacoes)) {
        foreach ($solicitacoes as $solicitacao) {
            $dataCriacao = date('d/m/Y', strtotime($solicitacao['criado_em']));
            $output .= '
                <tr style="text-align: center; cursor: pointer;" 
                    onmouseover="this.style.backgroundColor=\'#c8e6c9\'" onmouseout="this.style.backgroundColor=\'\'" 
                    data-toggle="tooltip" title="' . htmlspecialchars($solicitacao['descricao']) . '">
                    <td>' . htmlspecialchars($solicitacao['id']) . '</td>
                    <td>' . htmlspecialchars($solicitacao['agencia']) . '</td>
                    <td>' . htmlspecialchars($solicitacao['categoria']) . '</td>
                    <td>' . htmlspecialchars($solicitacao['status']) . '</td>
                    <td>' . $dataCriacao . '</td>
                    <td>' . htmlspecialchars($solicitacao['criado_por']) . '</td>
                    <td><button class="btn btn-success btn-sm" onclick="abrirDetalhes(' . $solicitacao['id'] . ')">Detalhes</button></td>
                </tr>';
        }
    } else {
        $output .= '
            <tr>
                <td colspan="7" class="text-center">Nenhuma solicitação encontrada</td>
            </tr>';
    }
    return $output;
}

// Verifica se é uma requisição AJAX para o filtro ou para obter detalhes
if (isset($_GET['filtro']) && isset($_GET['busca'])) {
    $filtro = $_GET['filtro'];
    $busca = $_GET['busca'];
    $usuario = isset($_GET['usuario']) ? $_GET['usuario'] : null; // Corrigido para pegar o usuário
    $solicitacoes = $controller->buscarSolicitacoes($filtro, $busca, $usuario); // Agora passa o usuário
    echo renderizarTabela($solicitacoes);
    exit();
} elseif (isset($_GET['acao']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $solicitacao = $controller->obterSolicitacaoPorId($id);
    echo json_encode($solicitacao);
    exit();
} elseif (isset($_GET['acao']) && $_GET['acao'] == 'historico' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $historico = $controller->obterHistorico($id); // Obtém o histórico
    echo json_encode($historico); // Garante que sempre retornamos um array
}

include 'app/views/navbar.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Solicitações</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet"> <!-- CSS separado -->
</head>
<body>
    <div class="container-fluid mt-4">
        <h2>Solicitações</h2>
        <div class="table-responsive">
            <table class="table table-bordered w-100" id="tabelaSolicitacoes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Agência</th>
                        <th>Categoria</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Usuário</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?= renderizarTabela($solicitacoes); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Detalhes -->
    <div class="modal fade" id="detalhesModal" tabindex="-1" role="dialog" aria-labelledby="detalhesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formDetalhes" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detalhesModalLabel">Detalhes da Solicitação # <span id="solicitacaoId"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="detalhesId" name="id">
                        <div class="form-group">
                            <label for="detalhesDescricao">Sugestão</label>
                            <textarea class="form-control" id="detalhesDescricao" name="descricao" required readonly></textarea>
                        </div>
                        <div class="form-group">
                            <label for="detalhesBeneficios">Benefícios</label>
                            <textarea class="form-control" id="detalhesBeneficios" name="beneficios" required readonly></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="detalhesCategoria">Categoria</label>
                                <select class="form-control" id="detalhesCategoria" name="categoria" required>
                                    <option value="Melhoria">Melhoria</option>
                                    <option value="Erro">Erro</option>
                                    <option value="Suporte">Suporte</option>
                                    <option value="Ajuste">Ajuste</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="detalhesStatus">Status</label>
                                <select class="form-control" id="detalhesStatus" name="status" required>
                                    <option value="Em análise">Em análise</option>
                                    <option value="Aceito">Aceito</option>
                                    <option value="Em aprovação">Em aprovação</option>
                                    <option value="Em desenvolvimento">Em desenvolvimento</option>
                                    <option value="Entregue">Entregue</option>
                                    <option value="Recusado">Recusado</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="prazo_execucao">Prazo de Execução</label>
                                <input type="date" class="form-control" id="prazo_execucao" name="prazo_execucao">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="detalhesComplexidade">Complexidade</label>
                                <select class="form-control" id="detalhesComplexidade" name="complexidade" required>
                                    <option value="1">Baixíssima</option>
                                    <option value="2">Baixa</option>
                                    <option value="3">Média</option>
                                    <option value="4">Alta</option>
                                    <option value="5">Altíssima</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="detalhesRelevancia">Relevância</label>
                                <select class="form-control" id="detalhesRelevancia" name="relevancia" required>
                                    <option value="1">Baixíssima</option>
                                    <option value="2">Baixa</option>
                                    <option value="3">Média</option>
                                    <option value="4">Alta</option>
                                    <option value="5">Altíssima</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="detalhesImpacto">Impacto</label>
                                <select class="form-control" id="detalhesImpacto" name="impacto" required>
                                    <option value="1">Baixíssimo</option>
                                    <option value="2">Baixo</option>
                                    <option value="3">Médio</option>
                                    <option value="4">Alto</option>
                                    <option value="5">Altíssimo</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Anexos</label>
                            <div id="detalhesAnexos" style="display: flex; flex-direction: column;"></div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-success" onclick="toggleDropArea('detalhes-drop-area')">Adicionar Anexos</button>
                            <div id="detalhes-drop-area" style="display: none;">
                                <p>Arraste e solte os arquivos aqui ou clique para selecionar arquivos</p>
                                <input type="file" id="detalhes-NovoAnexo" name="anexos[]" multiple style="display: none;" onchange="updateFileList('detalhes')">
                                <button type="button" onclick="document.getElementById('detalhes-NovoAnexo').click()" class="btn btn-secondary">Selecionar Arquivos</button>
                                <div id="detalhes-file-list" style="margin-top: 10px;"></div> <!-- Lista de arquivos -->
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-historico mr-auto" id="historicoButton" style="display: none;">Ver Histórico</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para o Histórico -->
    <div class="modal fade" id="historicoModal" tabindex="-1" role="dialog" aria-labelledby="historicoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historicoModalLabel">Histórico de Alterações</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Data e Hora</th>
                                <th>Alterações</th>
                            </tr>
                        </thead>
                        <tbody id="historicoConteudo"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Novo Ticket -->
    <div class="modal fade" id="novoTicketModal" tabindex="-1" role="dialog" aria-labelledby="novoTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novoTicketModalLabel">Novo Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formNovoTicket" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="categoria">Categoria</label>
                            <select class="form-control" id="categoria" name="categoria" required>
                                <option value="Melhoria">Melhoria</option>
                                <option value="Erro">Erro</option>
                                <option value="Suporte">Suporte</option>
                                <option value="Ajuste">Ajuste</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="descricao">Sugestão</label>
                            <textarea class="form-control" id="descricao" name="descricao" placeholder="Escreva detalhadamente sua sugestão/contribuição/demanda." required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="beneficios">Benefícios</label>
                            <textarea class="form-control" id="beneficios" name="beneficios" placeholder="Informe os benefícios a semre obtidos com sua sugestão/contribuição/demanda."  required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Anexos</label>
                            <div id="novoTicket-drop-area">
                                <p>Arraste e solte os arquivos aqui ou clique para selecionar arquivos</p>
                                <input type="file" id="novoTicket-NovoAnexo" name="anexos[]" multiple style="display: none;" onchange="updateFileList('novoTicket')">
                                <button type="button" onclick="document.getElementById('novoTicket-NovoAnexo').click()" class="btn btn-success">Selecionar Arquivos</button>
                                <div id="novoTicket-file-list" style="margin-top: 10px;"></div> <!-- Lista de arquivos -->
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Criar Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclusão dos Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script> <!-- Popper.js 1.x compatível com Bootstrap 4.5.2 -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script> <!-- Importando o arquivo JavaScript separado -->

    <script>
        function toggleDropArea(areaId) {
            const dropArea = document.getElementById(areaId);
            if (dropArea.style.display === "none" || dropArea.style.display === "") {
                dropArea.style.display = "block"; // Exibir a área de arrastar e soltar
            } else {
                dropArea.style.display = "none"; // Ocultar a área de arrastar e soltar
            }
        }
    </script>
</body>
</html>