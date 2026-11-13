
<?php
$paginaAtual = 'esporte-form';
$tituloPagina = 'Criar / Editar esporte';
$subtituloPagina = 'Modalidade, conquistas, treinos e galeria';

include 'include/head.php';
include 'include/nav.php';
?>

<main class="admin-main">

    <div class="page-heading">
        <div>
            <a href="esportes.php" class="back-link"><i class="bi bi-arrow-left"></i> Esportes</a>
            <h1>Criar / Editar esporte</h1>
            <p>Configure somente as seções que fizerem sentido para a modalidade.</p>
        </div>
        <button class="btn btn-brand">Salvar esporte</button>
    </div>

    <!-- INFORMAÇÕES DO ESPORTE -->
    <section class="panel form-panel">
        <div class="section-title">
            <span>1</span>
            <div>
                <h2>Informações da modalidade</h2>
                <p>Nome, apresentação e imagem principal.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <label class="form-label">Nome do esporte</label>
                <input class="form-control form-control-lg" value="Futsal">
            </div>

            <div class="col-lg-4">
                <label class="form-label">Imagem principal</label>
                <input class="form-control form-control-lg" type="file">
            </div>

            <div class="col-12">
                <label class="form-label">Descrição</label>
                <div class="wysiwyg">
                    <div class="wysiwyg-toolbar">
                        <button><i class="bi bi-type-bold"></i></button>
                        <button><i class="bi bi-type-italic"></i></button>
                        <button><i class="bi bi-list-ul"></i></button>
                        <button><i class="bi bi-link-45deg"></i></button>
                    </div>
                    <div class="wysiwyg-area" contenteditable="true">
                        <p>Descreva a modalidade, sua dinâmica e outras informações importantes...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CONQUISTAS -->
    <section class="panel form-panel">
        <div class="section-title">
            <span>2</span>
            <div>
                <h2>Conquistas <small>opcional</small></h2>
                <p>Registre medalhas e resultados, podendo reordená-los.</p>
            </div>
        </div>

        <div class="dynamic-form conquest-form">
            <select class="form-select" id="conquestGender">
                <option>Masculino</option>
                <option>Feminino</option>
                <option>Misto</option>
            </select>

            <input class="form-control" id="conquestYear" type="number" placeholder="Ano">

            <select class="form-select" id="conquestMedal">
                <option>Ouro</option>
                <option>Prata</option>
                <option>Bronze</option>
                <option>Outra</option>
            </select>

            <input class="form-control flex-grow-1" id="conquestName" placeholder="Nome da conquista">
            <button class="btn btn-brand" type="button" id="addConquest"><i class="bi bi-plus-lg"></i></button>
        </div>

        <div class="sortable-list" id="conquestList">
            <div class="sortable-item">
                <div class="medal gold"><i class="bi bi-trophy-fill"></i></div>
                <div class="flex-grow-1">
                    <strong>JERGS 2026 — Primeiro Lugar</strong>
                    <span>Masculino • Ouro</span>
                </div>
                <div class="reorder">
                    <button><i class="bi bi-arrow-up"></i></button>
                    <button><i class="bi bi-arrow-down"></i></button>
                    <button class="remove"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- HORÁRIOS DE TREINO -->
    <section class="panel form-panel">
        <div class="section-title">
            <span>3</span>
            <div>
                <h2>Horários de treino <small>opcional</small></h2>
                <p>Adicione quantos horários forem necessários para cada gênero.</p>
            </div>
        </div>

        <div class="dynamic-form training-form">
            <select class="form-select" id="trainingGender">
                <option>Feminino</option>
                <option>Masculino</option>
                <option>Misto</option>
            </select>

            <input class="form-control" id="trainingDays" placeholder="Dias da semana">
            <input class="form-control" id="trainingTime" placeholder="Horário">
            <input class="form-control" id="trainingPlace" placeholder="Local">
            <input class="form-control" id="trainingTeacher" placeholder="Professor responsável">
            <button class="btn btn-brand" type="button" id="addTraining"><i class="bi bi-plus-lg"></i></button>
        </div>

        <div class="sortable-list" id="trainingList">
            <div class="sortable-item">
                <div class="list-icon"><i class="bi bi-calendar-week"></i></div>
                <div class="flex-grow-1">
                    <strong>Feminino — Quintas-feiras • 12:25–13:30</strong>
                    <span>Ginásio • Prof. Fulano da Silva</span>
                </div>
                <div class="reorder">
                    <button><i class="bi bi-arrow-up"></i></button>
                    <button><i class="bi bi-arrow-down"></i></button>
                    <button class="remove"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- GALERIA DOS TIMES -->
    <section class="panel form-panel">
        <div class="section-title">
            <span>4</span>
            <div>
                <h2>Galeria dos times <small>opcional</small></h2>
                <p>Fotos de equipes por ano, com título e descrição para exibição ampliada no portal.</p>
            </div>
        </div>

        <div class="gallery-add sport-gallery-add">
            <div>
                <label class="form-label">Imagem</label>
                <input class="form-control" type="file" id="sportGalleryFile" accept="image/*">
            </div>

            <div>
                <label class="form-label">Título</label>
                <input class="form-control" id="sportGalleryTitle" placeholder="Ex.: Equipe 2026">
            </div>

            <div>
                <label class="form-label">Descrição</label>
                <input class="form-control" id="sportGalleryCaption" placeholder="Descrição da foto">
            </div>

            <button class="btn btn-brand" type="button" data-add-gallery="sportGallery">
                <i class="bi bi-plus-lg"></i> Adicionar
            </button>
        </div>

        <div class="gallery-list" id="sportGallery">
            <div class="gallery-item">
                <div class="gallery-preview"><i class="bi bi-people-fill"></i></div>
                <div class="gallery-info">
                    <strong>Equipe de Futsal 2026</strong>
                    <span>Time masculino participante dos JERGS.</span>
                </div>
                <div class="reorder">
                    <button><i class="bi bi-arrow-up"></i></button>
                    <button><i class="bi bi-arrow-down"></i></button>
                    <button class="remove"><i class="bi bi-x-lg"></i></button>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'include/foot.php'; ?>
