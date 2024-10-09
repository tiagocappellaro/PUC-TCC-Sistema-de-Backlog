<?php
// gerencial.php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: /app/views/login.php');
    exit();
}

// Incluindo a navbar
include 'app/views/navbar.php';

// Conexão com o banco de dados
require_once 'app/models/Database.php';
$db = new Database();

// Consultas ao banco de dados para coletar os dados necessários para os gráficos
$ticketsPorStatus = $db->listarTicketsPorStatus();
$ticketsPorCategoria = $db->listarTicketsPorCategoria();
$ticketsPorAgencia = $db->listarTicketsPorAgencia();
$rankingUsuarios = $db->rankingUsuarios();
$tempoMedioResolucao = $db->tempoMedioResolucao();

// Preparar dados para os gráficos
$statusLabels = array_column($ticketsPorStatus, 'status');
$statusData = array_column($ticketsPorStatus, 'total');

$categoriaLabels = array_column($ticketsPorCategoria, 'categoria');
$categoriaData = array_column($ticketsPorCategoria, 'total');

$agenciaLabels = array_column($ticketsPorAgencia, 'agencia');
$agenciaData = array_column($ticketsPorAgencia, 'total');

$usuarioLabels = array_column($rankingUsuarios, 'usuario');
$usuarioData = array_column($rankingUsuarios, 'total');

$tempoLabels = array_column($tempoMedioResolucao, 'ticket');
$tempoData = array_column($tempoMedioResolucao, 'dias');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerencial</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        /* Estilos para os contêineres dos gráficos */
        .canvas-container {
            padding: 10px; /* Redução adicional do padding */
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            margin-bottom: 20px; /* Margem inferior otimizada */
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
        }
        .canvas-container h4 {
            margin-bottom: 10px; /* Margem inferior menor */
            text-align: center;
            font-size: 1rem; /* Fonte mais compacta */
        }
        /* Utilizando aspect-ratio para manter proporção dos gráficos */
        .chart-wrapper {
            width: 100%;
            aspect-ratio: 3 / 2; /* Proporção 3:2 para contêiner mais compacto */
            position: relative;
            max-height: 250px; /* Altura máxima menor */
        }
        canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h2 class="mb-4 text-center">Gráficos de Tickets</h2>
        <div class="row">
            <!-- Tickets por Status -->
            <div class="col-lg-6 col-md-6 chart-row">
                <div class="canvas-container">
                    <h4>Tickets por Status</h4>
                    <div class="chart-wrapper">
                        <canvas id="graficoStatus"></canvas>
                    </div>
                </div>
            </div>
            <!-- Tickets por Categoria -->
            <div class="col-lg-6 col-md-6 chart-row">
                <div class="canvas-container">
                    <h4>Tickets por Categoria</h4>
                    <div class="chart-wrapper">
                        <canvas id="graficoCategoria"></canvas>
                    </div>
                </div>
            </div>
            <!-- Tickets por Agência -->
            <div class="col-lg-6 col-md-6 chart-row">
                <div class="canvas-container">
                    <h4>Tickets por Agência</h4>
                    <div class="chart-wrapper">
                        <canvas id="graficoAgencia"></canvas>
                    </div>
                </div>
            </div>
            <!-- Ranking de Usuários -->
            <div class="col-lg-6 col-md-6 chart-row">
                <div class="canvas-container">
                    <h4>Ranking de Usuários</h4>
                    <div class="chart-wrapper">
                        <canvas id="graficoUsuarios"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclusão dos Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="/assets/js/script.js"></script>

    <script>
        // Função para gerar cores dinâmicas com base no número de elementos
        function generateColors(count) {
            const bgColors = [
                'rgba(29, 185, 84, 0.7)', 'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ];
            const borderColors = [
                'rgba(29, 185, 84, 1)', 'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ];
            let selectedBgColors = [];
            let selectedBorderColors = [];
            for (let i = 0; i < count; i++) {
                selectedBgColors.push(bgColors[i % bgColors.length]);
                selectedBorderColors.push(borderColors[i % borderColors.length]);
            }
            return { bgColors: selectedBgColors, borderColors: selectedBorderColors };
        }

        // Gráfico de Tickets por Status
        const statusCount = <?= count($statusLabels) ?>;
        const statusColors = generateColors(statusCount);
        var ctxStatus = document.getElementById('graficoStatus').getContext('2d');
        var graficoStatus = new Chart(ctxStatus, {
            type: 'bar',
            data: {
                labels: <?= json_encode($statusLabels) ?>,
                datasets: [{
                    label: 'Tickets por Status',
                    data: <?= json_encode($statusData) ?>,
                    backgroundColor: statusColors.bgColors,
                    borderColor: statusColors.borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false, // Permite que o gráfico ocupe o contêiner
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision:0
                        }
                    }
                }
            }
        });

        // Gráfico de Tickets por Categoria
        const categoriaCount = <?= count($categoriaLabels) ?>;
        const categoriaColors = generateColors(categoriaCount);
        var ctxCategoria = document.getElementById('graficoCategoria').getContext('2d');
        var graficoCategoria = new Chart(ctxCategoria, {
            type: 'pie',
            data: {
                labels: <?= json_encode($categoriaLabels) ?>,
                datasets: [{
                    label: 'Tickets por Categoria',
                    data: <?= json_encode($categoriaData) ?>,
                    backgroundColor: categoriaColors.bgColors,
                    borderColor: categoriaColors.borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                }
            }
        });

        // Gráfico de Tickets por Agência
        const agenciaCount = <?= count($agenciaLabels) ?>;
        const agenciaColors = generateColors(agenciaCount);
        var ctxAgencia = document.getElementById('graficoAgencia').getContext('2d');
        var graficoAgencia = new Chart(ctxAgencia, {
            type: 'bar',
            data: {
                labels: <?= json_encode($agenciaLabels) ?>,
                datasets: [{
                    label: 'Tickets por Agência',
                    data: <?= json_encode($agenciaData) ?>,
                    backgroundColor: agenciaColors.bgColors,
                    borderColor: agenciaColors.borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision:0
                        }
                    }
                }
            }
        });

        // Gráfico de Ranking de Usuários
        const usuarioCount = <?= count($usuarioLabels) ?>;
        const usuarioColors = generateColors(usuarioCount);
        var ctxUsuarios = document.getElementById('graficoUsuarios').getContext('2d');
        var graficoUsuarios = new Chart(ctxUsuarios, {
            type: 'bar',
            data: {
                labels: <?= json_encode($usuarioLabels) ?>,
                datasets: [{
                    label: 'Ranking de Usuários',
                    data: <?= json_encode($usuarioData) ?>,
                    backgroundColor: usuarioColors.bgColors,
                    borderColor: usuarioColors.borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision:0
                        }
                    }
                }
            }
        });

        // Gráfico de Tempo Médio de Resolução
        var ctxTempoResolucao = document.getElementById('graficoTempoResolucao').getContext('2d');
        var graficoTempoResolucao = new Chart(ctxTempoResolucao, {
            type: 'line',
            data: {
                labels: <?= json_encode($tempoLabels) ?>,
                datasets: [{
                    label: 'Tempo Médio de Resolução (dias)',
                    data: <?= json_encode($tempoData) ?>,
                    fill: true,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
