<?php $titulo = 'Novo Usuário - Sistema de Agendamento'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">
        <i class="fas fa-user-plus me-2"></i> Novo Usuário
    </h1>
    <a href="/projeto-agendamento/public/usuarios" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Voltar
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="/projeto-agendamento/public/usuarios/criar" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome completo do usuário" required>
                    </div>
                    <small class="text-muted">O nome de usuário será gerado automaticamente no formato nome.sobrenome</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="E-mail do usuário" required>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="cpf" class="form-label">CPF <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF do usuário" required>
                    </div>
                    <small class="text-muted">A senha inicial será os 4 primeiros dígitos do CPF</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tipo_usuario" class="form-label">Tipo de Usuário <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                            <option value="admin">Administrador</option>
                            <option value="medio">Operador</option>
                            <option value="comum" selected>Visualizador</option>
                        </select>
                    </div>
                    <small class="text-muted">
                        Administrador: acesso total | Operador: cria agendamentos | Visualizador: apenas visualiza
                    </small>
                </div>
            </div>

            <hr>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="/projeto-agendamento/public/usuarios" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Salvar Usuário
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