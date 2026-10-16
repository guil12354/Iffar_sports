<?php
require_once __DIR__.'/conexao.php';
$email=$_GET['email']??''; $token=$_GET['token']??''; $valido=false;
if (filter_var($email,FILTER_VALIDATE_EMAIL) && preg_match('/^[a-f0-9]{64}$/',$token)) {
 $db=conectar(); $stmt=$db->prepare('SELECT data FROM recuperar_senha WHERE email=? AND token=? AND usado=0 ORDER BY data DESC LIMIT 1');
 $stmt->bind_param('ss',$email,$token); $stmt->execute(); $r=$stmt->get_result()->fetch_assoc();
 $valido=$r && strtotime($r['data'])!==false && strtotime($r['data'])>=time()-86400 && strtotime($r['data'])<=time()+300;
}
function e($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
?><!doctype html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Nova senha | IFFar Sports</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="estilo.css"></head><body><main class="card"><h3 class="logo mb-4">IFFar Sports</h3><?php if (!$valido): ?><h4>Link inválido ou expirado</h4><a href="recuperar_senha.php">Solicitar novo link</a><?php else: ?><h4>Definir nova senha</h4><form method="post" action="salvar_nova_senha.php"><input type="hidden" name="email" value="<?= e($email) ?>"><input type="hidden" name="token" value="<?= e($token) ?>"><label class="form-label">Nova senha</label><input type="password" class="form-control mb-3" name="nova_senha" minlength="8" required autocomplete="new-password"><label class="form-label">Confirmar nova senha</label><input type="password" class="form-control mb-3" name="nova_senha2" minlength="8" required autocomplete="new-password"><button class="btn btn-primary w-100">Salvar nova senha</button></form><?php endif; ?></main></body></html>