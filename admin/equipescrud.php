<?php
session_start();
if (!isset($_SESSION['id_usuario'])) { header('Location: ../login.php'); exit; }
if (($_SESSION['nivel'] ?? '') !== 'administrador') { header('Location: ../index.php'); exit; }
$db = new mysqli('localhost', 'root', '', 'iffar_sports3');
if ($db->connect_error) { http_response_code(500); exit('Erro ao conectar ao banco.'); }
$db->set_charset('utf8mb4');
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
if (empty($_SESSION['csrf_equipes'])) $_SESSION['csrf_equipes'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf_equipes'];
$erro = '';
$edicao = null;
$atletasEdicao = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) { $erro = 'Token de segurança inválido.'; }
 else {
  $acao = $_POST['acao'] ?? '';
  if ($acao === 'salvar') {
   $id = filter_var($_POST['id_equipe'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
   $esporte = filter_var($_POST['id_esporte'] ?? 0, FILTER_VALIDATE_INT);
   $nome = trim($_POST['nome'] ?? '');
   $ano = filter_var($_POST['ano'] ?? 0, FILTER_VALIDATE_INT);
   $categoria = $_POST['categoria'] ?? '';
   $status = $_POST['status'] ?? '';
   $tecnico = trim($_POST['tecnico'] ?? '');
   $historico = trim($_POST['historico'] ?? '');
   // Quill gera HTML. Restringimos a um conjunto pequeno de tags, sem atributos.
   $historico = strip_tags($historico, '<p><br><strong><em><u><ol><ul><li><blockquote>');
   $historico = preg_replace('/<\s*(p|br|strong|em|u|ol|ul|li|blockquote)\b[^>]*>/i', '<$1>', $historico);
   $historico = preg_replace('/<\s*br\s*>/i', '<br>', $historico);
   $foto = null;
   if (!$esporte || !$ano || $ano < 1900 || $ano > 2100 || $nome === '' || mb_strlen($nome) > 150 || !in_array($categoria, ['masculino','feminino','misto'], true) || !in_array($status, ['atual','historica'], true)) {
    $erro = 'Confira modalidade, nome, ano, categoria e situação.';
   }
   if (!$erro && !empty($_FILES['foto']['name'])) {
    $arq = $_FILES['foto'];
    if ($arq['error'] !== UPLOAD_ERR_OK || $arq['size'] > 5*1024*1024) $erro = 'Imagem inválida ou acima de 5 MB.';
    else {
     $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arq['tmp_name']);
     $ext = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null;
     if (!$ext) $erro = 'Envie JPG, PNG ou WEBP.';
     else {
      $dir = __DIR__ . '/../img/equipes/';
      if (!is_dir($dir) && !mkdir($dir, 0755, true)) $erro = 'Não foi possível criar a pasta de fotos.';
      else {
       $arquivo = bin2hex(random_bytes(12)).'.'.$ext;
       if (!move_uploaded_file($arq['tmp_name'], $dir.$arquivo)) $erro = 'Falha ao salvar foto.';
       else $foto = 'img/equipes/'.$arquivo;
      }
     }
    }
   }
   if (!$erro) {
    $db->begin_transaction();
    try {
     if ($id) {
      $q=$db->prepare('UPDATE equipes SET id_esporte=?, nome=?, ano=?, categoria=?, tecnico=?, historico=?, status=? WHERE id_equipe=?');
      $q->bind_param('isissssi',$esporte,$nome,$ano,$categoria,$tecnico,$historico,$status,$id);
      $q->execute();
      if ($foto !== null) { $q=$db->prepare('UPDATE equipes SET foto=? WHERE id_equipe=?'); $q->bind_param('si',$foto,$id); $q->execute(); }
     } else {
      $q=$db->prepare('INSERT INTO equipes (id_esporte,nome,ano,categoria,tecnico,historico,status,foto) VALUES (?,?,?,?,?,?,?,?)');
      $q->bind_param('isisssss',$esporte,$nome,$ano,$categoria,$tecnico,$historico,$status,$foto);
      $q->execute(); $id=$db->insert_id;
     }
     // Substitui o elenco inteiro no salvamento, preservando o cadastro da equipe.
     $q=$db->prepare('DELETE FROM atletas_equipe WHERE id_equipe=?'); $q->bind_param('i',$id); $q->execute();
     $q=$db->prepare('INSERT INTO atletas_equipe (id_equipe,nome,numero,posicao) VALUES (?,?,?,?)');
     $nomes=$_POST['atleta_nome']??[]; $numeros=$_POST['atleta_numero']??[]; $posicoes=$_POST['atleta_posicao']??[];
     if (!is_array($nomes) || count($nomes)>80) throw new RuntimeException('Quantidade de atletas inválida.');
     foreach ($nomes as $i=>$n) {
      $n=trim((string)$n); if ($n==='') continue;
      if (mb_strlen($n)>150) throw new RuntimeException('Nome de atleta muito longo.');
      $numero=trim((string)($numeros[$i]??''));
      $num=($numero==='')?null:filter_var($numero,FILTER_VALIDATE_INT);
      if ($num===false || ($num!==null && ($num<0 || $num>999))) throw new RuntimeException('Número de atleta inválido.');
      $pos=substr(trim((string)($posicoes[$i]??'')),0,80);
      $q->bind_param('isis',$id,$n,$num,$pos); $q->execute();
     }
     $db->commit(); header('Location: equipescrud.php?ok=1'); exit;
    } catch (Throwable $ex) { $db->rollback(); $erro='Não foi possível salvar: '.$ex->getMessage(); }
   }
  } elseif ($acao === 'arquivar') {
   $id=filter_var($_POST['id_equipe']??0,FILTER_VALIDATE_INT);
   if ($id) { $q=$db->prepare("UPDATE equipes SET status='historica' WHERE id_equipe=?"); $q->bind_param('i',$id); $q->execute(); header('Location: equipescrud.php?ok=2'); exit; }
  }
 }
}
$idEditar=filter_input(INPUT_GET,'editar',FILTER_VALIDATE_INT);
if ($idEditar) {
 $q=$db->prepare('SELECT * FROM equipes WHERE id_equipe=?'); $q->bind_param('i',$idEditar); $q->execute(); $edicao=$q->get_result()->fetch_assoc();
 if ($edicao) { $q=$db->prepare('SELECT * FROM atletas_equipe WHERE id_equipe=? ORDER BY id_atleta'); $q->bind_param('i',$idEditar); $q->execute(); $atletasEdicao=$q->get_result()->fetch_all(MYSQLI_ASSOC); }
}
$esportes=$db->query('SELECT id_esporte,nome FROM esportes ORDER BY nome')->fetch_all(MYSQLI_ASSOC);
$equipes=$db->query('SELECT e.*,s.nome AS esporte_nome,(SELECT COUNT(*) FROM atletas_equipe a WHERE a.id_equipe=e.id_equipe) AS total_atletas FROM equipes e JOIN esportes s ON s.id_esporte=e.id_esporte ORDER BY e.ano DESC,s.nome,e.nome')->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Equipes | IFFar Sports</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<style>:root{--verde:#004b2c;--escuro:#003b23;--laranja:#ffad14}body{background:#f3f5f4;color:#172b22}.sidebar{position:fixed;width:223px;height:100vh;background:var(--escuro);color:white;padding:24px 12px}.sidebar a{display:block;color:#dcece2;padding:13px;text-decoration:none;border-radius:8px}.sidebar a:hover,.sidebar a.active{background:#ffffff19;border-left:4px solid var(--laranja)}.main{margin-left:223px;padding:32px;max-width:1500px}.panel{background:white;border:1px solid #dfe8e2;border-radius:14px;padding:25px;margin-bottom:24px}.btn-brand{background:var(--verde);color:white}.btn-brand:hover{background:var(--escuro);color:white}.ql-container{height:auto!important;min-height:180px}.ql-editor{min-height:180px;max-height:380px;overflow-y:auto}.editor-equipes{margin-bottom:28px;clear:both}.editor-equipes #editor{background:#fff}.elenco-equipes{clear:both;padding-top:14px;border-top:1px solid #e2e9e5;margin-top:24px}.atleta{align-items:center}.atleta .btn{min-height:38px}.thumb{width:75px;height:55px;object-fit:cover;border-radius:8px}@media(max-width:800px){.sidebar{position:static;width:auto;height:auto}.main{margin-left:0;padding:15px}}</style></head><body>
<aside class="sidebar"><h5><i class="bi bi-trophy-fill text-warning"></i> IFFar Sports</h5><hr><a href="index.php">Dashboard</a><a href="noticiascrud.php">Notícias</a><a href="esportescrud.php">Esportes</a><a class="active" href="equipescrud.php">Equipes</a><hr><a href="../index.php">Ver portal</a><a href="../logout.php">Sair</a></aside>
<main class="main"><div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3" style="color:#003b23">Histórico de equipes</h1><p class="text-muted mb-0">Cadastre elencos por esporte e temporada.</p></div><a class="btn btn-outline-success" href="index.php">Voltar</a></div>
<?php if($erro): ?><div class="alert alert-danger"><?=e($erro)?></div><?php endif;?><?php if(isset($_GET['ok'])): ?><div class="alert alert-success">Operação concluída.</div><?php endif;?>
<section class="panel"><h2 class="h5 mb-4"><?= $edicao?'Editar equipe':'Cadastrar equipe' ?></h2><form method="post" enctype="multipart/form-data" id="formEquipe"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="acao" value="salvar"><input type="hidden" name="id_equipe" value="<?= (int)($edicao['id_equipe']??0) ?>"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Modalidade</label><select name="id_esporte" class="form-select" required><option value="">Selecione</option><?php foreach($esportes as $s):?><option value="<?=$s['id_esporte']?>" <?=($edicao['id_esporte']??'')==$s['id_esporte']?'selected':''?>><?=e($s['nome'])?></option><?php endforeach;?></select></div>
<div class="col-md-6"><label class="form-label">Nome da equipe</label><input name="nome" class="form-control" required maxlength="150" value="<?=e($edicao['nome']??'')?>"></div>
<div class="col-md-3"><label class="form-label">Ano</label><input type="number" name="ano" min="1900" max="2100" required class="form-control" value="<?=e($edicao['ano']??date('Y'))?>"></div>
<div class="col-md-3"><label class="form-label">Categoria</label><select name="categoria" class="form-select"><?php foreach(['masculino','feminino','misto'] as $v):?><option value="<?=$v?>" <?=($edicao['categoria']??'')===$v?'selected':''?>><?=ucfirst($v)?></option><?php endforeach;?></select></div>
<div class="col-md-3"><label class="form-label">Situação</label><select name="status" class="form-select"><?php foreach(['atual'=>'Atual','historica'=>'Histórica'] as $v=>$t):?><option value="<?=$v?>" <?=($edicao['status']??'historica')===$v?'selected':''?>><?=$t?></option><?php endforeach;?></select></div>
<div class="col-md-3"><label class="form-label">Técnico</label><input name="tecnico" class="form-control" maxlength="150" value="<?=e($edicao['tecnico']??'')?>"></div>
<div class="col-md-12"><label class="form-label">Foto oficial (JPG, PNG ou WEBP, até 5 MB)</label><input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp"><?php if(!empty($edicao['foto'])):?><img class="thumb mt-2" src="../<?=e($edicao['foto'])?>" alt="Foto atual"><?php endif;?></div>
<div class="col-12 editor-equipes"><label class="form-label">Histórico da temporada (editor visual)</label><div id="editor"></div><input type="hidden" name="historico" id="historico"></div></div>
<div class="elenco-equipes"><h3 class="h6 mb-3">Elenco</h3><div id="atletas" class="d-grid gap-2"><?php foreach($atletasEdicao as $a):?><div class="row g-2 atleta"><div class="col-md-6"><input name="atleta_nome[]" class="form-control" placeholder="Nome" value="<?=e($a['nome'])?>"></div><div class="col-md-2"><input name="atleta_numero[]" type="number" min="0" max="999" class="form-control" placeholder="Nº" value="<?=e($a['numero'])?>"></div><div class="col-md-3"><input name="atleta_posicao[]" class="form-control" placeholder="Posição" value="<?=e($a['posicao'])?>"></div><div class="col-md-1"><button type="button" class="btn btn-outline-danger" onclick="this.closest('.atleta').remove()">×</button></div></div><?php endforeach;?></div><button type="button" id="adicionar" class="btn btn-outline-success mt-3"><i class="bi bi-person-plus"></i> Adicionar atleta</button><div class="mt-4"><button class="btn btn-brand" type="submit">Salvar equipe</button><?php if($edicao):?><a class="btn btn-secondary" href="equipescrud.php">Cancelar edição</a><?php endif;?></div></div></form></section>
<section class="panel"><h2 class="h5">Equipes cadastradas</h2><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Foto</th><th>Equipe</th><th>Esporte</th><th>Ano</th><th>Categoria</th><th>Atletas</th><th>Status</th><th>Ações</th></tr></thead><tbody><?php foreach($equipes as $eq):?><tr><td><?php if($eq['foto']):?><img class="thumb" src="../<?=e($eq['foto'])?>" alt="Foto"><?php else:?>—<?php endif;?></td><td><?=e($eq['nome'])?></td><td><?=e($eq['esporte_nome'])?></td><td><?=e($eq['ano'])?></td><td><?=e(ucfirst($eq['categoria']))?></td><td><?= (int)$eq['total_atletas']?></td><td><?=e($eq['status'])?></td><td><div class="d-flex gap-2"><a class="btn btn-sm btn-warning" href="?editar=<?=$eq['id_equipe']?>">Editar</a><?php if($eq['status']!=='historica'):?><form method="post"><input type="hidden" name="csrf" value="<?=e($csrf)?>"><input type="hidden" name="acao" value="arquivar"><input type="hidden" name="id_equipe" value="<?=$eq['id_equipe']?>"><button class="btn btn-sm btn-outline-secondary">Arquivar</button></form><?php endif;?></div></td></tr><?php endforeach;?><?php if(!$equipes):?><tr><td colspan="8" class="text-muted text-center">Nenhuma equipe cadastrada.</td></tr><?php endif;?></tbody></table></div></section></main>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script><script>
const quill=new Quill('#editor',{theme:'snow',modules:{toolbar:[['bold','italic','underline'],[{'list':'ordered'},{'list':'bullet'}],['blockquote','clean']]}});
quill.clipboard.dangerouslyPasteHTML(<?=json_encode($edicao['historico']??'',JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>);
document.getElementById('formEquipe').addEventListener('submit',()=>{document.getElementById('historico').value=quill.root.innerHTML});
document.getElementById('adicionar').addEventListener('click',()=>{document.getElementById('atletas').insertAdjacentHTML('beforeend',`<div class="row g-2 atleta"><div class="col-md-6"><input name="atleta_nome[]" class="form-control" placeholder="Nome"></div><div class="col-md-2"><input name="atleta_numero[]" type="number" min="0" max="999" class="form-control" placeholder="Nº"></div><div class="col-md-3"><input name="atleta_posicao[]" class="form-control" placeholder="Posição"></div><div class="col-md-1"><button type="button" class="btn btn-outline-danger" onclick="this.closest('.atleta').remove()">×</button></div></div>`)});
</script></body></html>
