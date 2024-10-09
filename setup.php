<?php
// setup.php

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'backlog';
$dbUser = 'root';        // Ajuste conforme sua configuração
$dbPassword = '';        // Ajuste conforme sua configuração

// Função para criar pastas
function criarPastas($folders) {
    foreach ($folders as $folder) {
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
            echo "Pasta criada: $folder<br>";
        } else {
            echo "Pasta já existe: $folder<br>";
        }
    }
}

// Função para criar arquivos
function criarArquivo($caminho, $conteudo) {
    $diretorio = dirname($caminho);
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }
    file_put_contents($caminho, $conteudo);
    echo "Arquivo criado: $caminho<br>";
}

// Conexão com o banco de dados usando PDO
try {
    // Conectando sem selecionar o banco inicialmente
    $pdo = new PDO("mysql:host=$host;charset=utf8", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criação do banco de dados se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "Banco de dados '$dbname' criado ou já existe.<br>";
    $pdo->exec("USE `$dbname`");

    // Criação da tabela de usuários
    $pdo->exec("DROP TABLE IF EXISTS usuarios");
    $pdo->exec("
        CREATE TABLE usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario VARCHAR(50) NOT NULL UNIQUE,
            senha VARCHAR(255) NOT NULL,
            nome VARCHAR(100) NOT NULL,
            agencia ENUM('1', '10', '11', '15', '16', '20', '25', '30', '35', '40', '45', '50', '55', '60', '65', '70', '71', '75', '80', '85', '90', '95') NOT NULL
        )
    ");
    echo "Tabela 'usuarios' criada.<br>";

    // Inserindo dois usuários fictícios
    $usuarios = [
        ['usuario' => 'admin', 'senha' => 'admin', 'nome' => 'Administrador', 'agencia' => '10'],
        ['usuario' => 'tiago', 'senha' => 'tiago', 'nome' => 'Tiago Buchi Cappellaro', 'agencia' => '20'],
        ['usuario' => 'fulano', 'senha' => 'fulano', 'nome' => 'Fulano da Silva', 'agencia' => '30'],
        ['usuario' => 'beltrano', 'senha' => 'beltrano', 'nome' => 'Beltrano de Oliveira', 'agencia' => '40']
    ];

    $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, senha, nome, agencia) VALUES (:usuario, :senha, :nome, :agencia)");
    foreach ($usuarios as $u) {
        $stmt->execute([
            ':usuario' => $u['usuario'],
            ':senha' => password_hash($u['senha'], PASSWORD_DEFAULT),
            ':nome' => $u['nome'],
            ':agencia' => $u['agencia']
        ]);
    }
    echo "Usuários fictícios inseridos.<br>";

    // Criação da tabela de solicitações
    $pdo->exec("DROP TABLE IF EXISTS solicitacoes");
    $pdo->exec("
        CREATE TABLE solicitacoes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            categoria ENUM('Melhoria', 'Erro', 'Suporte', 'Ajuste') NOT NULL,
            descricao TEXT NOT NULL,
            beneficios TEXT NOT NULL,
            subticket INT NOT NULL,
            status ENUM('Em análise', 'Aceito', 'Em aprovação', 'Em desenvolvimento', 'Entregue', 'Recusado') DEFAULT 'Em análise',
            anexos TEXT DEFAULT NULL,
            historico TEXT DEFAULT NULL,
            agencia ENUM('1', '10', '11', '15', '16', '20', '25', '30', '35', '40', '45', '50', '55', '60', '65', '70', '71', '75', '80', '85', '90', '95') NOT NULL,
            complexidade ENUM('1', '2', '3', '4', '5') DEFAULT '3',
            relevancia ENUM('1', '2', '3', '4', '5') DEFAULT '3',
            impacto ENUM('1', '2', '3', '4', '5') DEFAULT '3',
            prazo_execucao DATE DEFAULT NULL,
            criado_por VARCHAR(100) NOT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Tabela 'solicitacoes' criada.<br>";
    $solicitacoes = [
        // 15 solicitações com categoria 'Erro', criado_por 'admin', agência '10', status 'Em análise'
        ['categoria' => 'Erro', 'descricao' => 'Erro ao salvar dados', 'beneficios' => 'Correção do salvamento', 'subticket' => 1, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 3, 'relevancia' => 4, 'impacto' => 5, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Falha na autenticação', 'beneficios' => 'Acesso restaurado', 'subticket' => 2, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 2, 'relevancia' => 5, 'impacto' => 5, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Sistema não responde', 'beneficios' => 'Disponibilidade do sistema', 'subticket' => 3, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 4, 'relevancia' => 5, 'impacto' => 5, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Erro ao gerar relatório', 'beneficios' => 'Relatórios corretos', 'subticket' => 4, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 3, 'relevancia' => 3, 'impacto' => 4, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Problema na conexão com o servidor', 'beneficios' => 'Estabilidade da conexão', 'subticket' => 5, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 2, 'relevancia' => 4, 'impacto' => 5, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Erro ao carregar página', 'beneficios' => 'Página carregada corretamente', 'subticket' => 6, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 3, 'relevancia' => 3, 'impacto' => 3, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Dados inconsistentes', 'beneficios' => 'Dados corretos', 'subticket' => 7, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 2, 'relevancia' => 4, 'impacto' => 4, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Aplicativo fecha inesperadamente', 'beneficios' => 'Estabilidade do aplicativo', 'subticket' => 8, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 4, 'relevancia' => 5, 'impacto' => 5, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Erro ao imprimir documentos', 'beneficios' => 'Impressão normalizada', 'subticket' => 9, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 3, 'relevancia' => 3, 'impacto' => 4, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Página não encontrada', 'beneficios' => 'Navegação funcional', 'subticket' => 10, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 2, 'relevancia' => 2, 'impacto' => 3, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Falha ao enviar e-mails', 'beneficios' => 'Notificações restabelecidas', 'subticket' => 11, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 3, 'relevancia' => 4, 'impacto' => 5, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Erro de permissão de acesso', 'beneficios' => 'Permissões corrigidas', 'subticket' => 12, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 2, 'relevancia' => 3, 'impacto' => 4, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Sistema lento', 'beneficios' => 'Desempenho otimizado', 'subticket' => 13, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 4, 'relevancia' => 4, 'impacto' => 5, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Erro ao exportar dados', 'beneficios' => 'Exportação funcional', 'subticket' => 14, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 3, 'relevancia' => 3, 'impacto' => 4, 'criado_por' => 'admin'],
        ['categoria' => 'Erro', 'descricao' => 'Falha na integração com API', 'beneficios' => 'Integração restabelecida', 'subticket' => 15, 'status' => 'Em análise', 'agencia' => '10', 'complexidade' => 5, 'relevancia' => 5, 'impacto' => 5, 'criado_por' => 'admin'],
    
        // 8 solicitações com categoria 'Melhoria', criado_por 'tiago', agência '20', status 'Aceito'
        ['categoria' => 'Melhoria', 'descricao' => 'Adicionar filtro avançado', 'beneficios' => 'Busca mais precisa', 'subticket' => 16, 'status' => 'Aceito', 'agencia' => '20', 'complexidade' => 3, 'relevancia' => 4, 'impacto' => 3, 'criado_por' => 'tiago'],
        ['categoria' => 'Melhoria', 'descricao' => 'Implementar dashboard', 'beneficios' => 'Visualização de dados', 'subticket' => 17, 'status' => 'Aceito', 'agencia' => '20', 'complexidade' => 4, 'relevancia' => 5, 'impacto' => 4, 'criado_por' => 'tiago'],
        ['categoria' => 'Melhoria', 'descricao' => 'Melhorar desempenho do sistema', 'beneficios' => 'Sistema mais rápido', 'subticket' => 18, 'status' => 'Aceito', 'agencia' => '20', 'complexidade' => 3, 'relevancia' => 5, 'impacto' => 5, 'criado_por' => 'tiago'],
        ['categoria' => 'Melhoria', 'descricao' => 'Atualizar interface gráfica', 'beneficios' => 'Melhor experiência do usuário', 'subticket' => 19, 'status' => 'Aceito', 'agencia' => '20', 'complexidade' => 2, 'relevancia' => 4, 'impacto' => 3, 'criado_por' => 'tiago'],
        ['categoria' => 'Melhoria', 'descricao' => 'Implementar notificações push', 'beneficios' => 'Comunicação em tempo real', 'subticket' => 20, 'status' => 'Aceito', 'agencia' => '20', 'complexidade' => 4, 'relevancia' => 5, 'impacto' => 4, 'criado_por' => 'tiago'],
        ['categoria' => 'Melhoria', 'descricao' => 'Adicionar suporte a múltiplos idiomas', 'beneficios' => 'Acessibilidade internacional', 'subticket' => 21, 'status' => 'Aceito', 'agencia' => '20', 'complexidade' => 5, 'relevancia' => 5, 'impacto' => 5, 'criado_por' => 'tiago'],
        ['categoria' => 'Melhoria', 'descricao' => 'Integrar com API externa', 'beneficios' => 'Novas funcionalidades', 'subticket' => 22, 'status' => 'Aceito', 'agencia' => '20', 'complexidade' => 3, 'relevancia' => 4, 'impacto' => 4, 'criado_por' => 'tiago'],
        ['categoria' => 'Melhoria', 'descricao' => 'Otimizar código fonte', 'beneficios' => 'Manutenção facilitada', 'subticket' => 23, 'status' => 'Aceito', 'agencia' => '20', 'complexidade' => 2, 'relevancia' => 3, 'impacto' => 3, 'criado_por' => 'tiago'],
    
        // 5 solicitações com categoria 'Suporte', criado_por 'fulano', agência '30', status 'Em desenvolvimento'
        ['categoria' => 'Suporte', 'descricao' => 'Ajuda com configuração inicial', 'beneficios' => 'Usuário orientado', 'subticket' => 24, 'status' => 'Em desenvolvimento', 'agencia' => '30', 'complexidade' => 1, 'relevancia' => 2, 'impacto' => 1, 'criado_por' => 'fulano'],
        ['categoria' => 'Suporte', 'descricao' => 'Dúvida sobre funcionalidade X', 'beneficios' => 'Usuário esclarecido', 'subticket' => 25, 'status' => 'Em desenvolvimento', 'agencia' => '30', 'complexidade' => 1, 'relevancia' => 2, 'impacto' => 1, 'criado_por' => 'fulano'],
        ['categoria' => 'Suporte', 'descricao' => 'Solicitação de treinamento', 'beneficios' => 'Equipe capacitada', 'subticket' => 26, 'status' => 'Em desenvolvimento', 'agencia' => '30', 'complexidade' => 2, 'relevancia' => 3, 'impacto' => 2, 'criado_por' => 'fulano'],
        ['categoria' => 'Suporte', 'descricao' => 'Assistência na migração de dados', 'beneficios' => 'Transição suave', 'subticket' => 27, 'status' => 'Em desenvolvimento', 'agencia' => '30', 'complexidade' => 3, 'relevancia' => 4, 'impacto' => 3, 'criado_por' => 'fulano'],
        ['categoria' => 'Suporte', 'descricao' => 'Dúvida sobre funcionalidade Y', 'beneficios' => 'Usuário esclarecido', 'subticket' => 28, 'status' => 'Em desenvolvimento', 'agencia' => '30', 'complexidade' => 2, 'relevancia' => 2, 'impacto' => 2, 'criado_por' => 'fulano'],
    
        // 2 solicitações com categoria 'Ajuste', criado_por 'beltrano', agência '40', status 'Entregue'
        ['categoria' => 'Ajuste', 'descricao' => 'Ajuste no cálculo de impostos', 'beneficios' => 'Valores corretos', 'subticket' => 29, 'status' => 'Entregue', 'agencia' => '40', 'complexidade' => 2, 'relevancia' => 3, 'impacto' => 2, 'criado_por' => 'beltrano'],
        ['categoria' => 'Ajuste', 'descricao' => 'Correção de textos em emails', 'beneficios' => 'Comunicação clara', 'subticket' => 30, 'status' => 'Entregue', 'agencia' => '40', 'complexidade' => 1, 'relevancia' => 2, 'impacto' => 1, 'criado_por' => 'beltrano'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO solicitacoes (categoria, descricao, beneficios, subticket, status, agencia, complexidade, relevancia, impacto, criado_por) VALUES (:categoria, :descricao, :beneficios, :subticket, :status, :agencia, :complexidade, :relevancia, :impacto, :criado_por)");
    
    foreach ($solicitacoes as $solicitacao) {
        $stmt->execute([
            ':categoria' => $solicitacao['categoria'],
            ':descricao' => $solicitacao['descricao'],
            ':beneficios' => $solicitacao['beneficios'],
            ':subticket' => $solicitacao['subticket'],
            ':status' => $solicitacao['status'],
            ':agencia' => $solicitacao['agencia'],
            ':complexidade' => $solicitacao['complexidade'],
            ':relevancia' => $solicitacao['relevancia'],
            ':impacto' => $solicitacao['impacto'],
            ':criado_por' => $solicitacao['criado_por']
        ]);
    }
    
    echo "Solicitações fictícias inseridas.<br>";
    
    // Criação das pastas necessárias
    $folders = [
        'app/controllers',
        'app/models',
        'app/views',
        'assets/css',
        'assets/js',
        'uploads'
    ];
    criarPastas($folders);

    // Código da navbar.php com ajustes de botões
    $navbarCode = <<<HTML
<nav class="navbar navbar-expand-lg" style="background-color: #1DB954;">
  <form>
  <a class="navbar-brand text-white" href="index.php" style="font-size: 20px;">Backlog</a>
</form>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon" style="color: white;"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <form class="form-inline w-100" id="filtroForm">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" 
             aria-haspopup="true" aria-expanded="false">
            Menu
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="gerencial.php">Gerencial</a>
          </div>
        </li>
      </ul>

      <?php if (basename(\$_SERVER['PHP_SELF']) == 'index.php'): ?>
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <button class="btn btn-success mr-2" type="button" data-toggle="modal" data-target="#novoTicketModal">Novo Ticket</button>
          </li>
          <li class="nav-item">
            <button class="btn btn-info" type="button" id="meusTicketsButton" data-usuario="<?php echo htmlspecialchars(\$_SESSION['usuario']); ?>">Meus Tickets</button>
          </li>
        </ul>
        <select name="filtro" class="form-control ml-2" id="filtroSelect">
          <option value="id">ID</option>
          <option value="categoria">Categoria</option>
          <option value="descricao">Descrição</option>
        </select>
        <input class="form-control ml-2" type="search" placeholder="Buscar" aria-label="Search" id="campoBusca" name="busca">
      <?php endif; ?>
    </form>
  </div>
</nav>
HTML;

    criarArquivo('app/views/navbar.php', $navbarCode);

    // Arquivo de estilo (CSS) com melhorias visuais e tonalidade verde
    $cssCode = <<<CSS
/* assets/css/style.css */

body {
    background-color: #f7f7f7;
    font-family: 'Poppins', sans-serif;
    margin: 0;
}

.navbar-brand {
    color: white !important;
}

.navbar-nav .nav-link {
    color: white !important;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    font-weight: bold;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
    font-weight: bold;
}

.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.btn-warning {
    background-color: #ffc107;
    border-color: #ffc107;
    font-weight: bold;
}

.btn-warning:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    font-weight: bold;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.modal-header {
    background-color: #1DB954;
    color: white;
}

th {
    text-align: center; /* Centraliza os cabeçalhos da tabela */
}

/* Estilo específico para a página de login */
.login-body {
    height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #1DB954, #0a7f3d);
}

/* Container que envolve o conteúdo da página de login */
.login-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 400px;
    padding: 20px;
    box-sizing: border-box;
}

/* Título BACKLOG */
.login-header {
    text-align: center;
    margin-bottom: 30px; /* Espaçamento entre o título e o quadro de login */
    animation: fadeInDown 1s ease-in-out; /* Aplicando a animação ao contêiner do título */
}

.login-header h1 {
    font-size: 4rem;
    color: white;
    font-weight: bold;
    letter-spacing: 4px;
    text-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin: 0;
}

/* Estilizando o container de login */
.login-container {
    width: 100%;
    max-width: 400px;
    padding: 40px;
    background-color: #ffffff; /* Fundo branco do formulário */
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-radius: 12px;
    text-align: center;
}

/* Estilizando os campos de input */
.form-control {
    height: 45px;
    border-radius: 10px;
    font-size: 1rem;
    padding: 10px 15px;
    border: 1px solid #ddd;
    transition: all 0.3s ease;
    box-shadow: none;
}

.form-control:focus {
    border-color: #1DB954;
    box-shadow: 0 0 10px rgba(29, 185, 84, 0.15);
    outline: none;
}

/* Estilo do botão de login */
.btn-login {
    background: linear-gradient(135deg, #1DB954, #0a7f3d);
    border: none;
    border-radius: 10px;
    height: 50px;
    font-size: 1.2rem;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-login:hover {
    background: linear-gradient(135deg, #159f3c, #086f32);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* Estilizando mensagens de erro */
.alert-danger {
    color: #d9534f;
    background-color: #f2dede;
    border-color: #ebccd1;
    padding: 10px;
    border-radius: 10px;
    font-size: 0.9rem;
}

/* Animação ao carregar a página */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsividade */
@media (max-width: 768px) {
    .login-header h1 {
        font-size: 2.5rem;
    }

    .login-container {
        padding: 20px;
    }
}

.navbar-nav .btn {
    font-size: 16px; /* Aumenta o tamanho da fonte dos botões */
    padding: 10px 20px; /* Aumenta o padding para maior visibilidade */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Adiciona sombra para destaque */
}

#detalhesAnexos div {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

/* Customização do botão Histórico */
.btn-historico {
    background-color: #28a745; /* Verde */
    border-color: #28a745;
    color: white;
}

.btn-historico:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

/* Alinhamento dos campos Complexidade, Relevancia e Impacto */
.form-row .form-group {
    flex: 1;
    margin-right: 10px;
}

.form-row .form-group:last-child {
    margin-right: 0;
}

/* Drop Area para Anexos */
#novoTicket-drop-area,
#detalhes-drop-area {
    border: 2px dashed #1DB954;
    padding: 20px;
    text-align: center;
    cursor: pointer;
}

#novoTicket-drop-area.active,
#detalhes-drop-area.active {
    background-color: rgba(29, 185, 84, 0.2); /* Altera a cor de fundo ao arrastar */
}

#novoTicket-file-list,
#detalhes-file-list {
    margin-top: 10px;
}

/* Estilizando o Tooltip */
.tooltip-inner {
    background-color: #1DB954 !important;  /* Cor de fundo do tooltip */
    color: white !important;               /* Cor do texto */
    font-size: 14px;                       /* Tamanho da fonte */
    padding: 10px;                         /* Espaçamento interno */
    border-radius: 10px;                   /* Borda arredondada */
    border: 2px solid #0b8b3d !important;  /* Borda verde mais forte */
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Sombra */
}

/* Estilizando a seta do Tooltip com borda verde */
.bs-tooltip-top .tooltip-arrow::before,
.bs-tooltip-bottom .tooltip-arrow::before,
.bs-tooltip-left .tooltip-arrow::before,
.bs-tooltip-right .tooltip-arrow::before {
    background-color: #1DB954; /* Cor de fundo da seta */
}

/* Personalizando o tamanho e estilo da seta do Tooltip */
.tooltip .arrow::before {
    border-color: transparent; /* Remover a borda padrão */
    border-width: 10px;        /* Tamanho da seta */
}

/* Cor da seta para cada direção */
.bs-tooltip-top .arrow::before {
    border-top-color: #1DB954; /* Cor da seta no topo */
}
.bs-tooltip-bottom .arrow::before {
    border-bottom-color: #1DB954; /* Cor da seta na parte inferior */
}
.bs-tooltip-left .arrow::before {
    border-left-color: #1DB954; /* Cor da seta à esquerda */
}
.bs-tooltip-right .arrow::before {
    border-right-color: #1DB954; /* Cor da seta à direita */
}

CSS;
    criarArquivo('assets/css/style.css', $cssCode);

    // Modelo de conexão com o banco de dados
    $dbModel = <<<PHP
<?php
class Database {
    private \$host = '$host';
    private \$dbname = '$dbname';
    private \$user = '$dbUser';
    private \$password = '$dbPassword';
    public \$pdo;

    public function __construct() {
        try {
            \$this->pdo = new PDO("mysql:host=" . \$this->host . ";dbname=" . \$this->dbname . ";charset=utf8", \$this->user, \$this->password);
            \$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException \$e) {
            die("Erro na conexão: " . \$e->getMessage());
        }
    }

    public function verificarUsuario(\$usuario, \$senha) {
        \$sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
        \$stmt = \$this->pdo->prepare(\$sql);
        \$stmt->bindParam(':usuario', \$usuario);
        \$stmt->execute();
        \$user = \$stmt->fetch(PDO::FETCH_ASSOC);

        if (\$user && password_verify(\$senha, \$user['senha'])) {
            return \$user; // Retorna o usuário completo para acessar o campo agência
        } else {
            return false;
        }
    }

public function buscarSolicitacoes(\$filtro, \$busca, \$usuario = null) {
    \$allowedFilters = ['categoria', 'descricao', 'id'];
    if (!in_array(\$filtro, \$allowedFilters)) {
        throw new InvalidArgumentException("Filtro inválido.");
    }

    \$buscaLike = "%\$busca%";

    if (\$usuario) {
        \$sql = "SELECT * FROM solicitacoes WHERE \$filtro LIKE :busca AND criado_por = :usuario";
        \$stmt = \$this->pdo->prepare(\$sql);
        \$stmt->bindParam(':busca', \$buscaLike, PDO::PARAM_STR);
        \$stmt->bindParam(':usuario', \$usuario, PDO::PARAM_STR);
    } else {
        \$sql = "SELECT * FROM solicitacoes WHERE \$filtro LIKE :busca";
        \$stmt = \$this->pdo->prepare(\$sql);
        \$stmt->bindParam(':busca', \$buscaLike, PDO::PARAM_STR);
    }

    \$stmt->execute();
    return \$stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function listarSolicitacoes(\$usuario = null) {
        if (\$usuario) {
            \$stmt = \$this->pdo->prepare("SELECT * FROM solicitacoes WHERE criado_por = :usuario");
            \$stmt->bindParam(':usuario', \$usuario, PDO::PARAM_STR);
            \$stmt->execute();
        } else {
            \$stmt = \$this->pdo->query('SELECT * FROM solicitacoes');
        }
        return \$stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function criarSolicitacao(\$categoria, \$descricao, \$beneficios, \$subticket, \$anexos, \$agencia, \$prazo_execucao, \$usuario) {
        // Armazena os anexos como JSON
        \$anexos_json = json_encode(\$anexos);

        // Se o prazo_execucao não estiver preenchido, defina como NULL
        if (empty(\$prazo_execucao)) {
            \$prazo_execucao = null;
        }

        \$stmt = \$this->pdo->prepare("INSERT INTO solicitacoes (categoria, descricao, beneficios, subticket, anexos, agencia, complexidade, relevancia, impacto, prazo_execucao, criado_por) 
                                      VALUES (:categoria, :descricao, :beneficios, :subticket, :anexos, :agencia, '3', '3', '3', :prazo_execucao, :criado_por)");
        \$stmt->bindParam(':categoria', \$categoria);
        \$stmt->bindParam(':descricao', \$descricao);
        \$stmt->bindParam(':beneficios', \$beneficios);
        \$stmt->bindParam(':subticket', \$subticket);
        \$stmt->bindParam(':anexos', \$anexos_json);
        \$stmt->bindParam(':agencia', \$agencia); // Adicionado para inserir a agência
        \$stmt->bindParam(':prazo_execucao', \$prazo_execucao); // Novo parâmetro
        \$stmt->bindParam(':criado_por', \$usuario); // Nome do usuário que criou a solicitação
        \$stmt->execute();

        // Obter o ID da nova solicitação
        \$id = \$this->pdo->lastInsertId();

        return \$id;
    }

    public function obterSolicitacaoPorId(\$id) {
        \$stmt = \$this->pdo->prepare("SELECT * FROM solicitacoes WHERE id = :id");
        \$stmt->bindParam(':id', \$id);
        \$stmt->execute();
        return \$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizarSolicitacao(\$id, \$categoria, \$descricao, \$beneficios, \$subticket, \$status, \$complexidade, \$relevancia, \$impacto, \$prazo_execucao, \$usuario) {
        // Busca a solicitação atual para comparar alterações
        \$solicitacaoAtual = \$this->obterSolicitacaoPorId(\$id);

        // Tratar o prazo de execução como NULL se for '0000-00-00' ou vazio
        \$prazo_execucao = (!empty(\$prazo_execucao) && \$prazo_execucao !== '0000-00-00') ? \$prazo_execucao : null;

        if (!\$solicitacaoAtual) {
            throw new Exception("Solicitação não encontrada.");
        }

        // Lógica para lidar com anexos
        \$anexosExistentes = json_decode(\$solicitacaoAtual['anexos'], true) ?: [];
        \$novosAnexos = [];

        // Adicionar novos anexos se houver
        if (isset(\$_FILES['anexos'])) {
            // Processa os novos anexos
            \$totalArquivos = count(\$_FILES['anexos']['name']);
            for (\$i = 0; \$i < \$totalArquivos; \$i++) {
                \$nomeArquivo = basename(\$_FILES['anexos']['name'][\$i]);
                \$caminhoDestino = 'uploads/' . uniqid() . '_' . \$nomeArquivo;
                if (move_uploaded_file(\$_FILES['anexos']['tmp_name'][\$i], \$caminhoDestino)) {
                    \$novosAnexos[] = \$caminhoDestino;
                }
            }
        }

        // Combine os anexos existentes com os novos
        \$anexosAtualizados = array_merge(\$anexosExistentes, \$novosAnexos);
        \$anexos_json = json_encode(\$anexosAtualizados, JSON_UNESCAPED_UNICODE);

        // Atualiza a solicitação
        \$stmt = \$this->pdo->prepare("UPDATE solicitacoes SET categoria = :categoria, descricao = :descricao, beneficios = :beneficios, subticket = :subticket, status = :status, complexidade = :complexidade, relevancia = :relevancia, impacto = :impacto, prazo_execucao = :prazo_execucao, anexos = :anexos WHERE id = :id");
        \$stmt->bindParam(':categoria', \$categoria);
        \$stmt->bindParam(':descricao', \$descricao);
        \$stmt->bindParam(':beneficios', \$beneficios);
        \$stmt->bindParam(':subticket', \$subticket);
        \$stmt->bindParam(':status', \$status);
        \$stmt->bindParam(':complexidade', \$complexidade);
        \$stmt->bindParam(':relevancia', \$relevancia);
        \$stmt->bindParam(':impacto', \$impacto);
        \$stmt->bindParam(':prazo_execucao', \$prazo_execucao); // Novo parâmetro
        \$stmt->bindParam(':anexos', \$anexos_json); // Adiciona anexos atualizados
        \$stmt->bindParam(':id', \$id);
        \$stmt->execute();

        // Determinar quais campos foram alterados
        \$alteracoes = [];
        foreach (['categoria', 'descricao', 'beneficios', 'status', 'complexidade', 'relevancia', 'impacto', 'prazo_execucao'] as \$campo) {
            if (\$solicitacaoAtual[\$campo] != \${\$campo}) {
                \$alteracoes[\$campo] = ['antes' => \$solicitacaoAtual[\$campo], 'depois' => \${\$campo}];
            }
        }

        // Atualiza o histórico apenas se houve alterações
        if (!empty(\$alteracoes)) {
            \$historicoAtual = \$solicitacaoAtual['historico'] ? json_decode(\$solicitacaoAtual['historico'], true) : [];
            \$novaEntrada = [
                "usuario" => \$usuario,
                "alteracao" => date("d/m/Y H:i"),
                "campos" => \$alteracoes
            ];
            \$historicoAtual[] = \$novaEntrada;

            \$historico_json = json_encode(\$historicoAtual, JSON_UNESCAPED_UNICODE);
            \$stmt = \$this->pdo->prepare("UPDATE solicitacoes SET historico = :historico WHERE id = :id");
            \$stmt->bindParam(':historico', \$historico_json);
            \$stmt->bindParam(':id', \$id);
            \$stmt->execute();
        }
    }

    public function adicionarAnexo(\$id, \$anexos) {
        \$solicitacao = \$this->obterSolicitacaoPorId(\$id);
        if (\$solicitacao) {
            \$anexosExistentes = \$solicitacao['anexos'] ? json_decode(\$solicitacao['anexos'], true) : [];
            \$anexosAtualizados = array_merge(\$anexosExistentes, \$anexos);
            \$anexos_json = json_encode(\$anexosAtualizados, JSON_UNESCAPED_UNICODE);
            \$stmt = \$this->pdo->prepare("UPDATE solicitacoes SET anexos = :anexos WHERE id = :id");
            \$stmt->bindParam(':anexos', \$anexos_json);
            \$stmt->bindParam(':id', \$id);
            \$stmt->execute();
        }
    }

    public function removerAnexo(\$id, \$caminho) {
        // Remove o anexo do banco de dados e do diretório
        \$stmt = \$this->pdo->prepare("SELECT anexos FROM solicitacoes WHERE id = :id");
        \$stmt->bindParam(':id', \$id);
        \$stmt->execute();
        \$solicitacao = \$stmt->fetch(PDO::FETCH_ASSOC);

        if (\$solicitacao) {
            \$anexos = json_decode(\$solicitacao['anexos'], true);
            if (!is_array(\$anexos)) {
                \$anexos = [];
            }
            \$anexos = array_filter(\$anexos, function(\$anexo) use (\$caminho) {
                return \$anexo !== \$caminho;
            });

            // Atualiza a tabela no banco de dados
            \$stmt = \$this->pdo->prepare("UPDATE solicitacoes SET anexos = :anexos WHERE id = :id");
            \$anexos_json = json_encode(array_values(\$anexos), JSON_UNESCAPED_UNICODE);
            \$stmt->bindParam(':anexos', \$anexos_json);
            \$stmt->bindParam(':id', \$id);
            \$stmt->execute();

            // Remove o arquivo do diretório
            if (file_exists(\$caminho)) {
                unlink(\$caminho);
            }
        }
    }

    public function obterHistoricoPorId(\$id) {
        \$stmt = \$this->pdo->prepare("SELECT historico FROM solicitacoes WHERE id = :id");
        \$stmt->bindParam(':id', \$id);
        \$stmt->execute();
        \$solicitacao = \$stmt->fetch(PDO::FETCH_ASSOC);

        // Retornar um array vazio se não houver histórico
        return !empty(\$solicitacao['historico']) ? json_decode(\$solicitacao['historico'], true) : [];
    }

    // Método para listar Tickets por Status
    public function listarTicketsPorStatus() {
        \$stmt = \$this->pdo->query("
            SELECT status, COUNT(*) as total
            FROM solicitacoes
            GROUP BY status
        ");
        return \$stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para listar Tickets por Categoria
    public function listarTicketsPorCategoria() {
        \$stmt = \$this->pdo->query("
            SELECT categoria, COUNT(*) as total
            FROM solicitacoes
            GROUP BY categoria
        ");
        return \$stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para listar Tickets por Agência
    public function listarTicketsPorAgencia() {
        \$stmt = \$this->pdo->query("
            SELECT agencia, COUNT(*) as total
            FROM solicitacoes
            GROUP BY agencia
        ");
        return \$stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para ranking de Usuários
    public function rankingUsuarios() {
        \$stmt = \$this->pdo->query("
            SELECT criado_por as usuario, COUNT(*) as total
            FROM solicitacoes
            GROUP BY criado_por
            ORDER BY total DESC
        ");
        return \$stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Método para calcular o Tempo Médio de Resolução
    public function tempoMedioResolucao() {
        \$stmt = \$this->pdo->query("
            SELECT 
                id as ticket, 
                AVG(DATEDIFF(COALESCE(prazo_execucao, NOW()), criado_em)) as dias
            FROM solicitacoes
            GROUP BY id
        ");
        return \$stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
PHP;

    criarArquivo('app/models/Database.php', $dbModel);

    // Controlador de login
    $loginController = <<<PHP
<?php
require_once __DIR__ . '/../models/Database.php';

class LoginController {
    private \$db;

    public function __construct() {
        \$this->db = new Database();
    }

    public function autenticar(\$usuario, \$senha) {
        return \$this->db->verificarUsuario(\$usuario, \$senha);
    }
}
?>
PHP;

    criarArquivo('app/controllers/LoginController.php', $loginController);

    // Controlador de solicitações
    $solicitacaoController = <<<PHP
<?php
require_once __DIR__ . '/../models/Database.php';

class SolicitacaoController {
    private \$db;

    public function __construct() {
        \$this->db = new Database();
    }

 public function listarSolicitacoes(\$usuario = null) {
    return \$this->db->listarSolicitacoes(\$usuario);
}

public function buscarSolicitacoes(\$filtro, \$busca, \$usuario = null) {
    return \$this->db->buscarSolicitacoes(\$filtro, \$busca, \$usuario);
}
    

    public function criarSolicitacao(\$categoria, \$descricao, \$beneficios, \$subticket, \$anexos, \$agencia, \$prazo_execucao, \$usuario) {
        return \$this->db->criarSolicitacao(\$categoria, \$descricao, \$beneficios, \$subticket, \$anexos, \$agencia, \$prazo_execucao, \$usuario);
    }

    public function obterSolicitacaoPorId(\$id) {
        return \$this->db->obterSolicitacaoPorId(\$id);
    }

    public function atualizarSolicitacao(\$id, \$categoria, \$descricao, \$beneficios, \$subticket, \$status, \$complexidade, \$relevancia, \$impacto, \$prazo_execucao, \$usuario) {
        return \$this->db->atualizarSolicitacao(\$id, \$categoria, \$descricao, \$beneficios, \$subticket, \$status, \$complexidade, \$relevancia, \$impacto, \$prazo_execucao, \$usuario);
    }

    public function adicionarAnexo(\$id, \$anexos) {
        return \$this->db->adicionarAnexo(\$id, \$anexos);
    }

    public function removerAnexo(\$id, \$caminho) {
        return \$this->db->removerAnexo(\$id, \$caminho);
    }

    public function obterHistorico(\$id) {
        return \$this->db->obterHistoricoPorId(\$id);
    }
}
?>
PHP;

    criarArquivo('app/controllers/SolicitacaoController.php', $solicitacaoController);

    // Página de login sem animação
    $loginPage = <<<PHP
<?php
session_start();
if (isset(\$_POST['usuario']) && isset(\$_POST['senha'])) {
    require_once __DIR__ . '/../controllers/LoginController.php';
    \$controller = new LoginController();

    \$usuario = \$_POST['usuario'];
    \$senha = \$_POST['senha'];

    if (\$user = \$controller->autenticar(\$usuario, \$senha)) {
        \$_SESSION['usuario'] = \$user['usuario'];
        \$_SESSION['nome'] = \$user['nome']; // Adicionando nome à sessão
        \$_SESSION['agencia'] = \$user['agencia']; // Adicionando agência à sessão
        header('Location: ../../index.php');
        exit();
    } else {
        \$erro = "Usuário ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Backlog</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-header">
            <h1>BACKLOG</h1>
        </div>
        <div class="login-container">
            <h3 class="text-center">Bem-vindo</h3>
            <?php if (isset(\$erro)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars(\$erro) ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="usuario">Usuário</label>
                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn btn-block btn-login">Entrar</button>
            </form>
        </div>
    </div>

    <!-- Inclusão dos Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

PHP;

    criarArquivo('app/views/login.php', $loginPage);

    // Página inicial (index.php) com melhorias
    $indexPage = <<<PHP
<?php
session_start();
if (!isset(\$_SESSION['usuario'])) {
    header('Location: app/views/login.php');
    exit();
}

require_once 'app/controllers/SolicitacaoController.php';
\$controller = new SolicitacaoController();

// Inicializa as solicitações sem filtro de usuário
\$solicitacoes = \$controller->listarSolicitacoes(); // Carrega todas as solicitações

// Verifica se a solicitação é POST para criação ou atualização de ticket
if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset(\$_POST['categoria'], \$_POST['descricao'], \$_POST['beneficios'])) {
        // Verifica se é uma atualização
        if (isset(\$_POST['id'])) {
            \$id = \$_POST['id'];
            \$status = \$_POST['status'];
            \$complexidade = \$_POST['complexidade'];
            \$relevancia = \$_POST['relevancia'];
            \$impacto = \$_POST['impacto'];
            \$prazo_execucao = \$_POST['prazo_execucao']; // Novo campo

            \$usuario = \$_SESSION['usuario']; // Captura o usuário logado

            // Adicionar anexos se houver
            \$anexos = [];
            if (isset(\$_FILES['anexos'])) {
                \$totalArquivos = count(\$_FILES['anexos']['name']);
                for (\$i = 0; \$i < \$totalArquivos; \$i++) {
                    \$nomeArquivo = basename(\$_FILES['anexos']['name'][\$i]);
                    \$caminhoDestino = 'uploads/' . uniqid() . '_' . \$nomeArquivo;
                    if (move_uploaded_file(\$_FILES['anexos']['tmp_name'][\$i], \$caminhoDestino)) {
                        \$anexos[] = \$caminhoDestino;
                    }
                }
            }

            \$controller->atualizarSolicitacao(\$id, \$_POST['categoria'], \$_POST['descricao'], \$_POST['beneficios'], \$id, \$status, \$complexidade, \$relevancia, \$impacto, \$prazo_execucao, \$usuario);

            // Adiciona os anexos após a atualização
            if (!empty(\$anexos)) {
                \$controller->adicionarAnexo(\$id, \$anexos);
            }

        } else {
            // Criação de nova solicitação
            \$categoria = \$_POST['categoria'];
            \$descricao = \$_POST['descricao'];
            \$beneficios = \$_POST['beneficios'];
            \$subticket = 0; // Definindo o subticket como 0, você pode ajustar conforme necessário
            \$agencia = \$_SESSION['agencia']; // Captura a agência da sessão
            \$prazo_execucao = \$_POST['prazo_execucao']; // Novo campo
            \$usuario = \$_SESSION['usuario']; // Captura o usuário da sessão

            // Processamento dos arquivos anexados
            \$anexos = [];
            if (isset(\$_FILES['anexos'])) {
                \$totalArquivos = count(\$_FILES['anexos']['name']);
                for (\$i = 0; \$i < \$totalArquivos; \$i++) {
                    \$nomeArquivo = basename(\$_FILES['anexos']['name'][\$i]);
                    \$caminhoDestino = 'uploads/' . uniqid() . '_' . \$nomeArquivo;
                    if (move_uploaded_file(\$_FILES['anexos']['tmp_name'][\$i], \$caminhoDestino)) {
                        \$anexos[] = \$caminhoDestino;
                    }
                }
            }

            \$controller->criarSolicitacao(\$categoria, \$descricao, \$beneficios, \$subticket, \$anexos, \$agencia, \$prazo_execucao, \$usuario); // Passando o nome do usuário logado
        }
        // Redireciona para a página inicial após criar ou atualizar o ticket
        header("Location: index.php");
        exit();
    }

    // Verifica se é uma requisição para remover anexo
    if (isset(\$_POST['acao']) && \$_POST['acao'] == 'removerAnexo') {
        if (isset(\$_POST['id']) && isset(\$_POST['caminho'])) {
            \$id = \$_POST['id'];
            \$caminho = \$_POST['caminho'];
            \$controller->removerAnexo(\$id, \$caminho);
            // Redireciona para a página inicial após remover o anexo
            header("Location: index.php");
            exit();
        }
    }

    // Verifica se é uma requisição para adicionar anexos na edição
    if (isset(\$_POST['acao']) && \$_POST['acao'] == 'adicionarAnexo') {
        if (isset(\$_POST['id']) && isset(\$_FILES['anexos'])) {
            \$id = \$_POST['id'];
            \$anexos = [];
            \$totalArquivos = count(\$_FILES['anexos']['name']);
            for (\$i = 0; \$i < \$totalArquivos; \$i++) {
                \$nomeArquivo = basename(\$_FILES['anexos']['name'][\$i]);
                \$caminhoDestino = 'uploads/' . uniqid() . '_' . \$nomeArquivo;
                if (move_uploaded_file(\$_FILES['anexos']['tmp_name'][\$i], \$caminhoDestino)) {
                    \$anexos[] = \$caminhoDestino;
                }
            }
            \$controller->adicionarAnexo(\$id, \$anexos);
            header("Location: index.php");
            exit();
        }
    }
}

// Função para renderizar a tabela de solicitações
function renderizarTabela(\$solicitacoes) {
    \$output = '';
    if (!empty(\$solicitacoes)) {
        foreach (\$solicitacoes as \$solicitacao) {
            \$dataCriacao = date('d/m/Y', strtotime(\$solicitacao['criado_em']));
            \$output .= '
                <tr style="text-align: center; cursor: pointer;" 
                    onmouseover="this.style.backgroundColor=\'#c8e6c9\'" onmouseout="this.style.backgroundColor=\'\'" 
                    data-toggle="tooltip" title="' . htmlspecialchars(\$solicitacao['descricao']) . '">
                    <td>' . htmlspecialchars(\$solicitacao['id']) . '</td>
                    <td>' . htmlspecialchars(\$solicitacao['agencia']) . '</td>
                    <td>' . htmlspecialchars(\$solicitacao['categoria']) . '</td>
                    <td>' . htmlspecialchars(\$solicitacao['status']) . '</td>
                    <td>' . \$dataCriacao . '</td>
                    <td>' . htmlspecialchars(\$solicitacao['criado_por']) . '</td>
                    <td><button class="btn btn-success btn-sm" onclick="abrirDetalhes(' . \$solicitacao['id'] . ')">Detalhes</button></td>
                </tr>';
        }
    } else {
        \$output .= '
            <tr>
                <td colspan="7" class="text-center">Nenhuma solicitação encontrada</td>
            </tr>';
    }
    return \$output;
}

// Verifica se é uma requisição AJAX para o filtro ou para obter detalhes
if (isset(\$_GET['filtro']) && isset(\$_GET['busca'])) {
    \$filtro = \$_GET['filtro'];
    \$busca = \$_GET['busca'];
    \$usuario = isset(\$_GET['usuario']) ? \$_GET['usuario'] : null; // Corrigido para pegar o usuário
    \$solicitacoes = \$controller->buscarSolicitacoes(\$filtro, \$busca, \$usuario); // Agora passa o usuário
    echo renderizarTabela(\$solicitacoes);
    exit();
} elseif (isset(\$_GET['acao']) && isset(\$_GET['id'])) {
    \$id = \$_GET['id'];
    \$solicitacao = \$controller->obterSolicitacaoPorId(\$id);
    echo json_encode(\$solicitacao);
    exit();
} elseif (isset(\$_GET['acao']) && \$_GET['acao'] == 'historico' && isset(\$_GET['id'])) {
    \$id = \$_GET['id'];
    \$historico = \$controller->obterHistorico(\$id); // Obtém o histórico
    echo json_encode(\$historico); // Garante que sempre retornamos um array
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
                    <?= renderizarTabela(\$solicitacoes); ?>
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
PHP;

    criarArquivo('index.php', $indexPage);

    $scriptJs = <<<JS
$(document).ready(function() {
    // Inicializa os tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Função updateFileList ajustada com logs de depuração
    window.updateFileList = function(modalPrefix) {
        const inputFileElement = document.getElementById(modalPrefix + '-NovoAnexo');
        const fileList = document.getElementById(modalPrefix + '-file-list');

        // Depuração: Verificar se o prefixo é o correto
        console.log(`Verificando elementos com prefixo: \${modalPrefix}`);
        
        // Debug: logar os elementos encontrados ou não encontrados
        console.log("Elemento de input de arquivos:", inputFileElement);
        console.log("Elemento de exibição de lista de arquivos:", fileList);

        // Verifique se o input e a lista de arquivos existem
        if (inputFileElement && fileList) {
            const files = inputFileElement.files;
            fileList.innerHTML = ''; // Limpa a lista de arquivos

            for (let i = 0; i < files.length; i++) {
                const listItem = document.createElement('div');
                listItem.textContent = files[i].name; // Adiciona o nome do arquivo
                fileList.appendChild(listItem);
            }
        } else {
            console.error(`Elemento de input ou lista de arquivos não encontrado para o prefixo: \${modalPrefix}`);
        }
    };

    // Variável para controlar o estado do filtro "Meus Tickets"
    let meusTicketsAtivo = false;
    let anexosExistentes = []; // Variável global para armazenar anexos existentes

    // Função para abrir o modal de detalhes
    window.abrirDetalhes = function(id) {
        $.ajax({
            url: 'index.php',
            type: 'GET',
            data: { acao: 'detalhes', id: id },
            dataType: 'json',
            success: function(data) {
                $('#detalhesId').val(data.id);
                $('#solicitacaoId').text(data.id); // Exibir o número da solicitação no título
                $('#detalhesCategoria').val(data.categoria);
                $('#detalhesDescricao').val(data.descricao);
                $('#detalhesBeneficios').val(data.beneficios);
                $('#detalhesStatus').val(data.status);
                $('#detalhesComplexidade').val(data.complexidade);
                $('#detalhesRelevancia').val(data.relevancia);
                $('#detalhesImpacto').val(data.impacto);
                $('#prazo_execucao').val(data.prazo_execucao);

                // Processa os anexos
                var anexosHtml = '';
                if (data.anexos) {
                    anexosExistentes = JSON.parse(data.anexos); // Armazena os anexos existentes
                    anexosExistentes.forEach(function(anexo) {
                        var nomeArquivo = anexo.split('/').pop();
                        anexosHtml += '<div>' + 
                       '<button class="btn btn-secondary" onclick="window.open(\'' + anexo + '\', \'_blank\')">' + nomeArquivo + '</button>' + 
                       ' <button class="btn btn-danger btn-sm" onclick="removerAnexo(' + data.id + ', \'' + anexo + '\')">Remover</button></div>';
                    });
                } else {
                    anexosHtml = 'Nenhum anexo disponível.';
                }
                $('#detalhesAnexos').html(anexosHtml);

                // Verifica se há histórico para habilitar o botão
                var historico = JSON.parse(data.historico);
                if (historico && historico.length > 0) {
                    $('#historicoButton').show();
                } else {
                    $('#historicoButton').hide();
                }

                $('#detalhesModal').modal('show');
            },
            error: function() {
                alert('Erro ao carregar os detalhes.');
            }
        });
    };

    // Submissão do formulário de detalhes (atualização)
    $('#formDetalhes').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        
        // Adiciona anexos existentes para garantir que eles sejam preservados
        formData.append('anexos_existentes', JSON.stringify(anexosExistentes));

        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function() {
                alert('Alterações salvas com sucesso!');
                // Limpa a lista de anexos após salvar
                $('#detalhes-file-list').empty();
                $('#detalhesAnexos').empty(); // Limpa os anexos exibidos
                // Não fecha o modal, apenas recarrega os detalhes
                abrirDetalhes($('#detalhesId').val());
            },
            error: function() {
                alert('Erro ao atualizar a solicitação.');
            }
        });
    });

    // Exibir histórico de alterações
    $('#historicoButton').on('click', function() {
        var id = $('#detalhesId').val();
        $.ajax({
            url: 'index.php',
            type: 'GET',
            data: { acao: 'historico', id: id },
            dataType: 'json',
            success: function(data) {
                var historicoHtml = '';
                var historico = JSON.parse(data.historico);
                if (historico && historico.length > 0) {
                    historico.forEach(function(entry) {
                        historicoHtml += '<tr><td align="center">' + entry.usuario + '</td><td align="center">' + entry.alteracao + '</td><td>';
                        for (var campo in entry.campos) {
                            // Adicionando textos literais para complexidade, relevância e impacto
                            var valorLiteral = '';
                            switch (campo) {
                                case 'complexidade':
                                    valorLiteral = getImpactoLiteral(entry.campos[campo].antes) + ' → ' + getImpactoLiteral(entry.campos[campo].depois);
                                    break;
                                case 'relevancia':
                                    valorLiteral = getImpactoLiteral(entry.campos[campo].antes) + ' → ' + getImpactoLiteral(entry.campos[campo].depois);
                                    break;
                                case 'impacto':
                                    valorLiteral = getImpactoLiteral(entry.campos[campo].antes) + ' → ' + getImpactoLiteral(entry.campos[campo].depois);
                                    break;
                                default:
                                    valorLiteral = entry.campos[campo].antes + ' → ' + entry.campos[campo].depois;
                            }
                            historicoHtml += campo.charAt(0).toUpperCase() + campo.slice(1) + ': ' + valorLiteral + '<br>';
                        }
                        historicoHtml += '</td></tr>';
                    });
                } else {
                    historicoHtml = '<tr><td colspan="3">Nenhuma alteração registrada.</td></tr>';
                }
                $('#historicoConteudo').html(historicoHtml);
                $('#historicoModal').modal('show');
            },
            error: function() {
                alert('Erro ao carregar o histórico.');
            }
        });
    });

    $('#campoBusca').on('input', function() {
    var filtro = $('#filtroSelect').val(); // Captura o filtro selecionado (ID, Categoria, Descrição)
    var busca = $(this).val(); // Captura o termo de busca digitado
    var usuario = $('#meusTicketsButton').hasClass('btn-warning') ? "<?php echo \$_SESSION['usuario']; ?>" : null; // Verifica se o filtro de "Meus Tickets" está ativo

    // Depuração: Log para verificar os dados sendo enviados
    console.log("Filtro: " + filtro + ", Busca: " + busca + ", Usuário: " + usuario);

    $.ajax({
        url: 'index.php',
        type: 'GET',
        data: { filtro: filtro, busca: busca, usuario: usuario }, // Envia o filtro, a busca e o usuário
        success: function(response) {
            console.log("Resposta recebida com sucesso:", response); // Verifica a resposta recebida do servidor
            $('#tabelaSolicitacoes tbody').html(response); // Atualiza a tabela com os resultados
            $('[data-toggle="tooltip"]').tooltip(); // Re-inicializa os tooltips
        },
        error: function() {
            console.error('Erro ao filtrar as solicitações.'); // Log de erro para depuração
        }
    });
});




$('#meusTicketsButton').on('click', function() {
    meusTicketsAtivo = !meusTicketsAtivo;
    var usuario = $(this).data('usuario'); // Captura o valor de data-usuario
    var filtro = $('#filtroSelect').val();
    var busca = $('#campoBusca').val();

    if (meusTicketsAtivo) {
        $(this).removeClass('btn-info').addClass('btn-warning').text('Mostrar Todos');
        // Recarrega a tabela com filtro de usuário
        $.ajax({
            url: 'index.php',
            type: 'GET',
            data: { filtro: filtro, busca: busca, usuario: usuario }, // Passa o usuário
            success: function(response) {
                $('#tabelaSolicitacoes tbody').html(response);
                $('[data-toggle="tooltip"]').tooltip(); // Re-inicializa os tooltips
            },
            error: function() {
                alert('Erro ao filtrar as solicitações.');
            }
        });
    } else {
        $(this).removeClass('btn-warning').addClass('btn-info').text('Meus Tickets');
        // Recarrega a tabela sem filtro de usuário
        $.ajax({
            url: 'index.php',
            type: 'GET',
            data: { filtro: filtro, busca: busca, usuario: null }, // Sem filtro de usuário
            success: function(response) {
                $('#tabelaSolicitacoes tbody').html(response);
                $('[data-toggle="tooltip"]').tooltip(); // Re-inicializa os tooltips
            },
            error: function() {
                alert('Erro ao filtrar as solicitações.');
            }
        });
    }
});



    // Funções para Drag and Drop
    const dropAreaNovoTicket = document.getElementById('novoTicket-drop-area');
    const dropAreaDetalhes = document.getElementById('detalhes-drop-area');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropAreaNovoTicket.addEventListener(eventName, preventDefaults, false);
        dropAreaDetalhes.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropAreaNovoTicket.addEventListener(eventName, () => dropAreaNovoTicket.classList.add('active'), false);
        dropAreaDetalhes.addEventListener(eventName, () => dropAreaDetalhes.classList.add('active'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropAreaNovoTicket.addEventListener(eventName, () => dropAreaNovoTicket.classList.remove('active'), false);
        dropAreaDetalhes.addEventListener(eventName, () => dropAreaDetalhes.classList.remove('active'), false);
    });

    dropAreaNovoTicket.addEventListener('drop', handleDropNovoTicket, false);
    dropAreaDetalhes.addEventListener('drop', handleDropDetalhes, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function handleDropNovoTicket(e) {
        let dt = e.dataTransfer;
        let files = dt.files;
        document.getElementById('novoTicket-NovoAnexo').files = files; // Atribui os arquivos ao input
        updateFileList('novoTicket'); // Atualiza a lista de arquivos
    }

    function handleDropDetalhes(e) {
        let dt = e.dataTransfer;
        let files = dt.files;
        document.getElementById('detalhes-NovoAnexo').files = files; // Atribui os arquivos ao input
        updateFileList('detalhes'); // Atualiza a lista de arquivos
    }
    
    function updateFileList(modalPrefix) {
        const files = document.getElementById(modalPrefix + '-NovoAnexo').files;
        const fileList = document.getElementById(modalPrefix + '-file-list');
        fileList.innerHTML = ''; // Limpa a lista de arquivos
        for (let i = 0; i < files.length; i++) {
            const listItem = document.createElement('div');
            listItem.textContent = files[i].name; // Adiciona o nome do arquivo
            fileList.appendChild(listItem);
        }
    }

    // Remover anexo
    window.removerAnexo = function(id, caminho) {
        // Lógica para remover o anexo do banco de dados
        if (confirm('Tem certeza que deseja remover este anexo?')) {
            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: { id: id, caminho: caminho, acao: 'removerAnexo' },
                success: function() {
                    location.reload(); // Recarrega a página após a remoção
                },
                error: function() {
                    alert('Erro ao remover o anexo.');
                }
            });
        }
    };

    // Funções para obter os textos literais
    function getComplexidadeLiteral(value) {
        switch (value) {
            case '1': return 'Baixíssima';
            case '2': return 'Baixa';
            case '3': return 'Média';
            case '4': return 'Alta';
            case '5': return 'Altíssima';
            default: return 'Desconhecido';
        }
    }

    function getRelevanciaLiteral(value) {
        switch (value) {
            case '1': return 'Baixíssima';
            case '2': return 'Baixa';
            case '3': return 'Média';
            case '4': return 'Alta';
            case '5': return 'Altíssima';
            default: return 'Desconhecido';
        }
    }

    function getImpactoLiteral(value) {
        switch (value) {
            case '1': return 'Baixíssimo';
            case '2': return 'Baixo';
            case '3': return 'Médio';
            case '4': return 'Alto';
            case '5': return 'Altíssimo';
            default: return 'Desconhecido';
        }
    }
});
JS;

    criarArquivo('assets/js/script.js', $scriptJs);

    // Mensagem de sucesso
    echo "<h2>Configuração concluída com sucesso!</h2>";
    echo "<p>Acesse <a href='app/views/login.php'>Login</a> para iniciar.</p>";

} catch (PDOException $e) {
    echo "<h2>Erro na configuração:</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
