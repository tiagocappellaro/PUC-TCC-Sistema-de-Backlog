$(document).ready(function() {
    // Inicializa os tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Função updateFileList ajustada com logs de depuração
    window.updateFileList = function(modalPrefix) {
        const inputFileElement = document.getElementById(modalPrefix + '-NovoAnexo');
        const fileList = document.getElementById(modalPrefix + '-file-list');

        // Depuração: Verificar se o prefixo é o correto
        console.log(`Verificando elementos com prefixo: ${modalPrefix}`);
        
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
            console.error(`Elemento de input ou lista de arquivos não encontrado para o prefixo: ${modalPrefix}`);
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
    var usuario = $('#meusTicketsButton').hasClass('btn-warning') ? "<?php echo $_SESSION['usuario']; ?>" : null; // Verifica se o filtro de "Meus Tickets" está ativo

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