<?php
$idEquipe = filter_input(INPUT_GET, 'equipe', FILTER_VALIDATE_INT);
$eqH = static fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
$eqDb = new mysqli('localhost', 'root', '', 'iffar_sports3');
if ($eqDb->connect_error) { echo '<div class="container py-5">Não foi possível carregar a equipe.</div>'; return; }
$eqDb->set_charset('utf8mb4');
$eq = null; $atletas = [];
if ($idEquipe) {
 $q = $eqDb->prepare('SELECT e.*, s.nome AS esporte_nome FROM equipes e JOIN esportes s ON s.id_esporte=e.id_esporte WHERE e.id_equipe=?');
 $q->bind_param('i', $idEquipe); $q->execute(); $eq = $q->get_result()->fetch_assoc();
 if ($eq) { $q=$eqDb->prepare('SELECT nome, numero, posicao FROM atletas_equipe WHERE id_equipe=? ORDER BY numero IS NULL, numero, nome'); $q->bind_param('i',$idEquipe); $q->execute(); $atletas=$q->get_result()->fetch_all(MYSQLI_ASSOC); }
}
?>
<style>
.iffar-eq-page{max-width:1140px;margin:36px auto 70px;padding:0 20px}.iffar-eq-page h1{color:#003b23;font-weight:800}.iffar-eq-back{color:#004b2c;text-decoration:none;font-weight:650}.iffar-eq-cover{width:100%;max-height:380px;object-fit:cover;border-radius:14px;margin:20px 0}.iffar-eq-panel{background:#fff;border:1px solid #e0e7e2;border-radius:13px;padding:24px;margin-top:22px}.iffar-eq-panel h2{font-size:1.25rem;color:#003b23;font-weight:750}.iffar-eq-panel table{width:100%;border-collapse:collapse}.iffar-eq-panel td,.iffar-eq-panel th{padding:12px;border-bottom:1px solid #e6ece8;text-align:left}.iffar-eq-panel th{color:#004b2c}.iffar-eq-history{line-height:1.75;overflow-wrap:anywhere}.iffar-eq-history p:last-child{margin-bottom:0}
</style>
<main class="iffar-eq-page">
<?php if (!$eq): ?>
 <h1>Equipe não encontrada</h1><a class="iffar-eq-back" href="index.php?id=esportes">← Voltar aos esportes</a>
<?php else: ?>
 <a class="iffar-eq-back" href="index.php?id=esporte&amp;esporte=<?= (int)$eq['id_esporte'] ?>">← <?= $eqH($eq['esporte_nome']) ?></a>
 <p style="color:#e39500;font-weight:700;margin:18px 0 6px">IFFAR SPORTS · HISTÓRICO</p>
 <h1><?= $eqH($eq['nome']) ?></h1>
 <p><?= (int)$eq['ano'] ?> · <?= $eqH(ucfirst($eq['categoria'])) ?> · <?= $eq['status']==='atual'?'Equipe atual':'Equipe histórica' ?></p>
 <?php if ($eq['foto']): ?><img class="iffar-eq-cover" src="<?= $eqH($eq['foto']) ?>" alt="Foto oficial de <?= $eqH($eq['nome']) ?>"><?php endif; ?>
 <?php if ($eq['tecnico']): ?><section class="iffar-eq-panel"><h2>Comissão técnica</h2><p><?= $eqH($eq['tecnico']) ?></p></section><?php endif; ?>
 <section class="iffar-eq-panel"><h2>Elenco</h2>
 <?php if (!$atletas): ?><p>Nenhum atleta cadastrado.</p><?php else: ?>
 <div style="overflow-x:auto"><table><thead><tr><th>Nº</th><th>Atleta</th><th>Posição</th></tr></thead><tbody>
 <?php foreach ($atletas as $a): ?><tr><td><?= $a['numero'] === null ? '—' : (int)$a['numero'] ?></td><td><?= $eqH($a['nome']) ?></td><td><?= $eqH($a['posicao']) ?></td></tr><?php endforeach; ?>
 </tbody></table></div><?php endif; ?></section>
 <?php if (trim((string)$eq['historico']) !== ''): ?>
 <section class="iffar-eq-panel"><h2>Histórico da temporada</h2><div class="iffar-eq-history">
 <?php
 // Reaplica uma lista restrita de tags ao conteúdo do editor.
 $safe = strip_tags($eq['historico'], '<p><br><strong><em><u><ol><ul><li><blockquote>');
 $safe = preg_replace('/<\s*(p|br|strong|em|u|ol|ul|li|blockquote)\b[^>]*>/i', '<$1>', $safe);
 echo $safe;
 ?>
 </div></section><?php endif; ?>
<?php endif; ?>
</main>
<?php $eqDb->close(); ?>
