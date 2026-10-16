<?php
require_once __DIR__.'/conexao.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); exit('Método inválido.'); }
$email=$_POST['email']??''; $token=$_POST['token']??'';
$senha=$_POST['nova_senha']??''; $confirmacao=$_POST['nova_senha2']??'';
if (!filter_var($email,FILTER_VALIDATE_EMAIL) || !preg_match('/^[a-f0-9]{64}$/',$token) || strlen($senha)<8 || $senha!==$confirmacao) { http_response_code(400); exit('Dados inválidos ou senhas diferentes.'); }
$db=conectar(); $db->begin_transaction();
try {
 $stmt=$db->prepare('SELECT data FROM recuperar_senha WHERE email=? AND token=? AND usado=0 ORDER BY data DESC LIMIT 1 FOR UPDATE');
 $stmt->bind_param('ss',$email,$token); $stmt->execute(); $r=$stmt->get_result()->fetch_assoc();
 if (!$r || strtotime($r['data'])===false || strtotime($r['data'])<time()-86400 || strtotime($r['data'])>time()+300) throw new RuntimeException('Link inválido ou expirado.');
 $hash=password_hash($senha,PASSWORD_DEFAULT);
 $stmt=$db->prepare('UPDATE usuarios SET senha=? WHERE email=?'); $stmt->bind_param('ss',$hash,$email); $stmt->execute();
 if ($stmt->affected_rows<1) throw new RuntimeException('Conta não encontrada.');
 $stmt=$db->prepare('UPDATE recuperar_senha SET usado=1 WHERE email=?'); $stmt->bind_param('s',$email); $stmt->execute();
 $db->commit();
 echo '<!doctype html><html lang="pt-BR"><meta charset="UTF-8"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="estilo.css"><body><main class="card"><h3>Senha atualizada!</h3><p>Agora você pode entrar com sua nova senha.</p><a class="btn btn-primary" href="../login.php">Ir para o login</a></main></body></html>';
} catch(Throwable $e) { $db->rollback(); http_response_code(400); echo 'Não foi possível redefinir a senha. <a href="recuperar_senha.php">Solicitar novo link</a>'; error_log($e->getMessage()); }
