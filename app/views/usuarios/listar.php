<?php $titulo = 'Usuários - Sistema de Agendamento'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-users me-2"></i> Usuários
    </h1>
    <a href="/projeto-agendamento/public/usuarios/criar" class="btn btn-primary">
        <i class="fas fa-user-plus me-1"></i> Novo Usuário
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (empty($usuarios)): ?>
            <p class="text-center py-3">
                <i class="fas fa-info-circle me-1"></i> Nenhum usuário encontrado
            </p>
        <?php else: ?>
            <div class="table-responsive">
                <table id="tabela-usuarios" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Usuário</th>
                            <th>CPF</th>
                            <th>Tipo</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                                <td>
                                    <?php
                                    $cpf = preg_replace('/[^0-9]/', '', $usuario['cpf']);
                                    echo substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $tipoClasses = [
                                        'admin' => 'danger',
                                        'medio' => 'warning',
                                        'comum' => 'info'
                                    ];
                                    $tipoNomes = [
                                        'admin' => 'Administrador',
                                        'medio' => 'Operador',
                                        'comum' => 'Visualizador'
                                    ];
                                    $tipoClass = $tipoClasses[$usuario['tipo_usuario']] ?? 'secondary';
                                    $tipoNome = $tipoNomes[$usuario['tipo_usuario']] ?? $usuario['tipo_usuario'];
                                    ?>
                                    <span class="badge bg-<?= $tipoClass ?>">
                                        <?= $tipoNome ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="/projeto-agendamento/public/usuarios/editar/<?= $usuario['id'] ?>" class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-excluir"
                                            data-id="<?= $usuario['id'] ?>"
                                            data-nome="<?= htmlspecialchars($usuario['nome']) ?>"
                                            title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Exclusão (oculto) -->
<form id="form-excluir" method="post" action="">
    <!-- Será preenchido via JavaScript -->
</form>

<?php
$scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inicializa DataTables
    $("#tabela-usuarios").DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json"
        },
        responsive: true,
        order: [[0, "asc"]], // Ordena por nome
        pageLength: 10
    });
    
    // Configuração dos botões de exclusão
    document.querySelectorAll(".btn-excluir").forEach(function(button) {
        button.addEventListener("click", function() {
            const id = this.getAttribute("data-id");
            const nome = this.getAttribute("data-nome");
            
            if (confirm("Deseja excluir o usuário " + nome + "? Esta ação não pode ser desfeita.")) {
                const form = document.getElementById("form-excluir");
                form.action = "/projeto-agendamento/public/usuarios/excluir/" + id;
                form.submit();
            }
        });
    });
});
</script>';
?>