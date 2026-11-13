<?php
$eqEsporteId = (int)($esporte['id_esporte'] ?? ($_GET['esporte'] ?? 0));

if (!function_exists('limparEquipe')) {
    function limparEquipe($v) {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}

$eqDb = new mysqli('localhost', 'root', '', 'iffar_sports3');
if ($eqDb->connect_error) {
    echo '<p>Histórico temporariamente indisponível.</p>';
    return;
}
$eqDb->set_charset('utf8mb4');

$eqLista = [];
if ($eqEsporteId > 0) {
    $eqStmt = $eqDb->prepare('SELECT id_equipe, nome, ano, categoria, foto, status FROM equipes WHERE id_esporte = ? ORDER BY ano DESC, categoria, nome');
    $eqStmt->bind_param('i', $eqEsporteId);
    $eqStmt->execute();
    $eqLista = $eqStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<style>
.iffar-equipes-section{margin:32px 0 55px}.iffar-equipes-section h2{color:#003b23;font-weight:750;font-size:1.5rem;margin-bottom:8px}.iffar-equipes-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(245px,1fr));gap:18px;margin-top:18px}.iffar-equipes-card{border:1px solid #e0e8e3;border-radius:13px;background:#fff;overflow:hidden;box-shadow:0 6px 18px #003b230d}.iffar-equipes-card img{display:block;width:100%;height:170px;object-fit:cover}.iffar-equipes-placeholder{height:170px;display:grid;place-items:center;background:#dfeae3;color:#004b2c;font-size:48px}.iffar-equipes-card-body{padding:18px}.iffar-equipes-card-body h3{font-size:1.08rem;font-weight:700;color:#003b23}.iffar-equipes-meta{font-size:.9rem;color:#65756b;margin:10px 0}.iffar-equipes-link{color:#004b2c;font-weight:700;text-decoration:none}.iffar-equipes-link:hover{text-decoration:underline}
</style>
<section class="iffar-equipes-section" id="historico-equipes">
    <h2>Histórico de equipes</h2>
    <p style="color:#66776c">Conheça os elencos que representaram esta modalidade.</p>
    <?php if (!$eqLista): ?>
        <p class="text-muted">Nenhuma equipe cadastrada para esta modalidade.</p>
    <?php else: ?>
        <div class="iffar-equipes-grid">
            <?php foreach ($eqLista as $eq): ?>
                <article class="iffar-equipes-card">
                    <?php if ($eq['foto']): ?>
                        <img src="<?= limparEquipe($eq['foto']) ?>" alt="Foto da equipe <?= limparEquipe($eq['nome']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="iffar-equipes-placeholder" aria-hidden="true">⚽</div>
                    <?php endif; ?>
                    <div class="iffar-equipes-card-body">
                        <h3><?= limparEquipe($eq['nome']) ?></h3>
                        <p class="iffar-equipes-meta"><?= (int)$eq['ano'] ?> · <?= limparEquipe(ucfirst($eq['categoria'])) ?> · <?= $eq['status'] === 'atual' ? 'Atual' : 'Histórica' ?></p>
                        <a class="iffar-equipes-link" href="index.php?id=equipe&equipe=<?= (int)$eq['id_equipe'] ?>">Ver elenco →</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php $eqDb->close(); ?>