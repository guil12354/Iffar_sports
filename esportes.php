
<?php
$paginaAtual = 'esportes';
$tituloPagina = 'Esportes';
$subtituloPagina = 'Gerencie as modalidades esportivas';

include 'include/head.php';
include 'include/nav.php';
?>

<main class="admin-main">

    <div class="page-heading">
        <div>
            <span class="eyebrow">MODALIDADES</span>
            <h1>Esportes</h1>
            <p>Edite informações, treinos, conquistas e galerias.</p>
        </div>

        <a href="esporte-form.php" class="btn btn-brand">
            <i class="bi bi-plus-lg"></i> Novo esporte
        </a>
    </div>

    <section class="panel">

        <div class="toolbar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="search" placeholder="Buscar modalidade...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table admin-table align-middle">

                <thead>
                    <tr>
                        <th>Ícone</th>
                        <th>Nome</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $esportes = [
                        ['bi-dribbble', 'Futsal'],
                        ['bi-circle', 'Basquete'],
                        ['bi-circle-fill', 'Handebol'],
                        ['bi-dribbble', 'Voleibol'],
                        ['bi-person-arms-up', 'Atletismo']
                    ];

                    foreach ($esportes as $esporte):
                    ?>

                    <tr>
                        <td>
                            <div class="sport-icon">
                                <i class="bi <?= $esporte[0] ?>"></i>
                            </div>
                        </td>

                        <td>
                            <strong><?= $esporte[1] ?></strong>
                        </td>

                        <td class="text-end">
                            <a href="esporte-form.php" class="icon-btn">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <button class="icon-btn danger">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>

                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    </section>

</main>

<?php include 'include/foot.php'; ?>
