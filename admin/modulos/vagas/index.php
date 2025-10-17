<style>
.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
}

.btn-outline-warning {
    color: #f39c12;
    border-color: #f39c12;
}

.btn-outline-warning:hover {
    background-color: #f39c12;
    border-color: #f39c12;
}

.btn-outline-danger {
    color: #e74c3c;
    border-color: #e74c3c;
}

.btn-outline-danger:hover {
    background-color: #e74c3c;
    border-color: #e74c3c;
}

.requisitos-list {
    list-style: none;
    padding-left: 0;
}

.requisitos-list li {
    position: relative;
    padding-left: 20px;
    margin-bottom: 5px;
}

.requisitos-list li:before {
    content: "•";
    color: #667eea;
    font-weight: bold;
    position: absolute;
    left: 0;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.alert {
    border: none;
    border-radius: 10px;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 14px;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}

/* Fallback CSS para modal caso Bootstrap não esteja funcionando */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    overflow-y: auto;
    outline: 0;
    display: none; /* Escondido por padrão */
}

.modal.show {
    display: flex !important;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    position: relative;
    width: auto;
    max-width: 500px;
    margin: 1.75rem auto;
    pointer-events: auto;
}

.modal-lg {
    max-width: 800px;
}

.modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    width: 100%;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 0.3rem;
    outline: 0;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1040;
    width: 100vw;
    height: 100vh;
    background-color: #000;
    opacity: 0.5;
    display: none;
}

.modal-backdrop.show {
    display: block;
}

.btn-close {
    background: none;
    border: 0;
    font-size: 1.5rem;
    color: #fff;
    cursor: pointer;
}

.modal-header, .modal-body, .modal-footer {
    padding: 1rem;
}

.modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    border-bottom: 1px solid #dee2e6;
}

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    border-top: 1px solid #dee2e6;
}

.modal-footer > * {
    margin-left: 0.5rem;
}

/* Garantir que os ícones apareçam */
.fas, .fa {
    font-family: "Font Awesome 5 Free", "Font Awesome 5 Pro", "FontAwesome" !important;
    font-weight: 900 !important;
    margin-right: 5px;
}

.btn-sm {
    min-width: 80px;
    white-space: nowrap;
}
</style>


<section>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Cabeçalho -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>
                        Gerenciar Vagas
                    </h4>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vagaModal" data-toggle="modal" data-target="#vagaModal" onclick="abrirModalVaga()">
                        <i class="fas fa-plus me-1"></i>
                        Nova Vaga
                    </button>
                </div>
            </div>

            <!-- Alertas -->
            <div id="alertContainer"></div>

            <!-- Lista de Vagas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Lista de Vagas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Requisitos</th>
                                    <th>Data Criação</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="vagasTableBody">
                                <!-- Conteúdo será carregado via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Criar/Editar Vaga -->
<div class="modal fade" id="vagaModal" tabindex="-1" aria-labelledby="vagaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vagaModalLabel">Nova Vaga</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="fecharModalVaga()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="vagaForm">
                <div class="modal-body">
                    <input type="hidden" id="vagaId" name="id">
                    
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título da Vaga *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required placeholder="Ex: Assistente de Marketing">
                    </div>
                    
                    <div class="mb-3">
                        <label for="requisitos" class="form-label">Requisitos *</label>
                        <textarea class="form-control" id="requisitos" name="requisitos" rows="8" required 
                                placeholder="Digite os requisitos e pressione Enter para adicionar uma nova linha&#10;Ex: Ensino superior completo em Marketing;&#10;Experiência mínima de 2 anos na área;&#10;Conhecimento em redes sociais;"></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Digite cada requisito em uma linha. Ao pressionar Enter, um ponto e vírgula (;) será adicionado automaticamente.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="fecharModalVaga()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Deletar -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="fecharModalDelete()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta vaga?</p>
                <p class="text-muted">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" onclick="fecharModalDelete()">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i>
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function() {
    let editingVagaId = null;
    
    // Função SIMPLES para abrir modal
    window.abrirModalVaga = function() {
        // Esconde qualquer loading antes de abrir o modal
        esconderLoadingGeral();
        
        document.getElementById('vagaModalLabel').textContent = 'Nova Vaga';
        document.getElementById('vagaForm').reset();
        document.getElementById('vagaId').value = '';
        editingVagaId = null;
        
        const modal = document.getElementById('vagaModal');
        modal.style.display = 'flex';
        modal.classList.add('show');
    };
    
    // Função SIMPLES para fechar modal
    window.fecharModalVaga = function() {
        const modal = document.getElementById('vagaModal');
        modal.style.display = 'none';
        modal.classList.remove('show');
    };
    
    // Função SIMPLES para fechar modal delete
    window.fecharModalDelete = function() {
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'none';
        modal.classList.remove('show');
    };
    
    // Função para esconder loading geral do sistema
    function esconderLoadingGeral() {
        // Tenta esconder vários tipos de loading que podem existir
        const loadingElements = [
            '#loading',
            '.loading',
            '#loader',
            '.loader',
            '#loading-overlay',
            '.loading-overlay',
            '#spinner',
            '.spinner',
            '[id*="loading"]',
            '[class*="loading"]'
        ];
        
        loadingElements.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                element.style.display = 'none';
                element.classList.add('d-none');
                element.classList.remove('show', 'active');
            });
        });
        
        // Remove classe modal-open do body
        document.body.classList.remove('modal-open');
        
        // Remove qualquer backdrop que possa estar ativo
        const backdrops = document.querySelectorAll('.modal-backdrop, [class*="backdrop"]');
        backdrops.forEach(backdrop => {
            backdrop.remove();
        });
    }
    
    // Máscara para requisitos
    const requisitosTextarea = document.getElementById('requisitos');
    requisitosTextarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const cursorPos = this.selectionStart;
            const textBefore = this.value.substring(0, cursorPos);
            const textAfter = this.value.substring(cursorPos);
            
            const currentLine = textBefore.split('\n').pop();
            const separator = currentLine.trim() && !currentLine.endsWith(';') ? ';\n' : '\n';
            
            this.value = textBefore + separator + textAfter;
            this.selectionStart = this.selectionEnd = cursorPos + separator.length;
        }
    });
    
    // Carrega vagas
    carregarVagas();
    
    // Submissão do formulário
    document.getElementById('vagaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const url = editingVagaId ? 'modulos/vagas/php/atualizar_vaga.php' : 'modulos/vagas/php/criar_vaga.php';
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Esconde qualquer loading que possa estar ativo
            esconderLoadingGeral();
            
            if (data.status === 'success') {
                mostrarAlerta(data.message, 'success');
                fecharModalVaga();
                carregarVagas();
            } else {
                mostrarAlerta(data.message, 'danger');
            }
        })
        .catch(error => {
            // Esconde loading em caso de erro também
            esconderLoadingGeral();
            mostrarAlerta('Erro: ' + error.message, 'danger');
        });
    });
    
    // Função para carregar vagas
    function carregarVagas() {
        fetch('modulos/vagas/php/listar_vagas.php')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('vagasTableBody');
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nenhuma vaga cadastrada</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.map(vaga => {
                const dataFormatada = new Date(vaga.data_criacao).toLocaleDateString('pt-BR');
                const requisitosFormatados = vaga.requisitos_formatados.map(req => `<li>${req}</li>`).join('');
                
                return `
                    <tr>
                        <td>${vaga.id}</td>
                        <td><strong>${vaga.titulo}</strong></td>
                        <td><ul class="requisitos-list">${requisitosFormatados}</ul></td>
                        <td>${dataFormatada}</td>
                        <td class="text-center">
                            <button class="btn btn-outline-warning btn-sm me-1" onclick="editarVaga(${vaga.id})">Editar</button>
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmarDelete(${vaga.id})">Excluir</button>
                        </td>
                    </tr>
                `;
            }).join('');
        })
        .catch(error => {
            mostrarAlerta('Erro ao carregar vagas: ' + error.message, 'danger');
        });
    }
    
    // Editar vaga
    window.editarVaga = function(id) {
        // Esconde qualquer loading antes de abrir o modal
        esconderLoadingGeral();
        
        editingVagaId = id;
        
        fetch(`modulos/vagas/php/obter_vaga.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('vagaId').value = data.id;
            document.getElementById('titulo').value = data.titulo;
            document.getElementById('requisitos').value = data.requisitos_raw;
            document.getElementById('vagaModalLabel').textContent = 'Editar Vaga';
            
            const modal = document.getElementById('vagaModal');
            modal.style.display = 'flex';
            modal.classList.add('show');
        })
        .catch(error => {
            mostrarAlerta('Erro: ' + error.message, 'danger');
        });
    };
    
    // Confirmar delete
    window.confirmarDelete = function(id) {
        // Esconde qualquer loading antes de abrir o modal
        esconderLoadingGeral();
        
        editingVagaId = id;
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'flex';
        modal.classList.add('show');
    };
    
    // Delete
    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (!editingVagaId) return;
        
        const formData = new FormData();
        formData.append('id', editingVagaId);
        
        fetch('modulos/vagas/php/deletar_vaga.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Esconde qualquer loading que possa estar ativo
            esconderLoadingGeral();
            
            if (data.status === 'success') {
                mostrarAlerta(data.message, 'success');
                carregarVagas();
            } else {
                mostrarAlerta(data.message, 'danger');
            }
            fecharModalDelete();
            editingVagaId = null;
        })
        .catch(error => {
            // Esconde loading em caso de erro também
            esconderLoadingGeral();
            mostrarAlerta('Erro: ' + error.message, 'danger');
        });
    });
    
    // Função para mostrar alertas
    function mostrarAlerta(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        const alertId = 'alert-' + Date.now();
        
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert" id="${alertId}">
                ${message}
                <button type="button" class="btn-close" onclick="document.getElementById('${alertId}').remove()"></button>
            </div>
        `;
        
        alertContainer.insertAdjacentHTML('beforeend', alertHTML);
        
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) alert.remove();
        }, 5000);
    }
});
</script>