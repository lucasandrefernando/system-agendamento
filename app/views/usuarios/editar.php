<?php $titulo = 'Editar Usuário - Sistema de Agendamento'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-user-edit me-2"></i> Editar Usuário
    </h1>
    <a href="/projeto-agendamento/public/usuarios" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="/projeto-agendamento/public/usuarios/editar/<?= $usuario['id'] ?>" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="nome" name="nome"
                            value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email"
                            value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control" id="cpf" name="cpf"
                            value="<?= htmlspecialchars($usuario['cpf']) ?>" required>
                    </div>
                    <small class="text-muted">A senha inicial é os 4 primeiros dígitos do CPF</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tipo_usuario" class="form-label">Tipo de Usuário <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                            <option value="admin" <?= $usuario['tipo_usuario'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                            <option value="medio" <?= $usuario['tipo_usuario'] === 'medio' ? 'selected' : '' ?>>Operador</option>
                            <option value="comum" <?= $usuario['tipo_usuario'] === 'comum' ? 'selected' : '' ?>>Visualizador</option>
                        </select>
                    </div>
                    <small class="text-muted">
                        Administrador: acesso total | Operador: cria agendamentos | Visualizador: apenas visualiza
                    </small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome de Usuário</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-circle"></i></span>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['usuario']) ?>" readonly>
                    </div>
                    <small class="text-muted">O nome de usuário não pode ser alterado</small>
                </div>
            </div>

            <hr>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/projeto-agendamento/public/usuarios" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Atualizar Usuário
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Máscara para CPF (se disponível)
    if (typeof $.fn.mask === "function") {
        $("#cpf").mask("000.000.000-00", {clearIfNotMatch: true});
    }
});
</script>';
?>