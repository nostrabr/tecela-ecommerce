<?php
// Inclui a conexão com o banco de dados
include_once '../../bd/conecta.php';

// Busca as 3 últimas categorias cadastradas no banco
// A tabela correta é 'categoria' e os campos necessários são id, nome e imagem
$busca_categorias = mysqli_query($conn, "SELECT id, nome, imagem FROM categoria ORDER BY id DESC LIMIT 3");

// Array para armazenar as categorias
$categorias = [];

// Coleta as categorias do banco
while($categoria = mysqli_fetch_array($busca_categorias)) {
    $categorias[] = $categoria;
}

// Desconecta do banco
include_once '../../bd/desconecta.php';
?>

<style>
    .nossas-linhas {
        background-color: #f5f8f8;
        padding: 50px 0;
        text-align: center;
    }
    
    .nossas-linhas-titulo h2 {
        color: #1C4A50;
        font-size: 16px;
        font-weight: 500;
        margin: 0;
    }
    
    .nossas-linhas-titulo h3 {
        color: #DC582A;
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }
    
    .nossas-linhas-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .linha-item {
        position: relative;
        width: 300px;
        border-radius: 20px;
        overflow: hidden;
    }
    
    .linha-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    
    .linha-item-titulo {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        background-color: var(--cor-categoria);
        color: white;
        padding: 5px 20px;
        border-radius: 15px;
        font-weight: 600;
    }
    
    .nossas-linhas-btn {
        background-color: #DC582A;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
    }
    
    /* Estilos para cores específicas das categorias */
    .linha-item:nth-child(1) .linha-item-titulo {
        --cor-categoria: #c9926d;
    }
    .linha-item:nth-child(2) .linha-item-titulo {
        --cor-categoria: #2a7770;
    }
    .linha-item:nth-child(3) .linha-item-titulo {
        --cor-categoria: #DC582A;
    }

    #container-linhas{
        width: 80%;
    }

    .header-linha{
        padding: 7px 10px;
        font-size: 22px;
        width: 70%;
        margin: 0px auto 30px auto; /* Alterado para remover margem inferior */
        font-weight: bold;
        color: white;
        border-top-left-radius: 20px;
        border-bottom-right-radius: 20px;
        position: relative; /* Adicionado para posicionamento do triângulo */
    }
    
    /* Adicionando o triângulo abaixo do header */
    .header-linha:after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 15px solid transparent;
        border-right: 15px solid transparent;
        border-top: 15px solid; /* A cor será definida inline */
        z-index: 1;
        color: var(--cor-categoria); /* Usando a variável CSS para a cor */
    }

    @media(min-width:1500px) {
        #container-linhas{
            width: 70%;
        }
    }
    
    @media(max-width:992px) {
        #container-linhas{
            width: 95%;
        }
    }
    .container-img-linha{
        width: 100%;
        height: 300px;
        border-top-left-radius: 40px;
        border-bottom-right-radius: 40px;
        overflow: hidden;
    }
    .container-img-linha img{
        width: 115%;
        height: 115%;
        object-fit: cover;
        object-position: center;
    }
    
    /* Responsividade para mobile */
    @media (max-width: 768px) {
        .nossas-linhas-container {
            flex-direction: column;
            align-items: center;
        }
    }
</style>

<section class="nossas-linhas" style="margin-left: -15px; margin-right: -15px;">
    <div class="container">
        <div class="nossas-linhas-titulo mb-5">
            <div class="text-center mb-4"><img style="width: 50px;" src='<?= $loja['site']?>imagens/ico-lancamento.png'></div>
            <h2 style="font-size: 1.4em;">Nossas</h2>
            <h3 style="font-size: 2em;">Linhas</h3>
        </div>

        <div class="row mx-auto" id="container-linhas">
            <?php foreach ($categorias as $key => $cat) { ?>
                <?php
                    if($key == 0) {
                        $cor_categoria = '#D18F5D';
                    } elseif($key == 1) {
                        $cor_categoria = '#000000';
                    } else {
                        $cor_categoria = '#2E7075';
                    }
                ?>

                <div class="col-12 col-lg-4 mb-5 mb-lg-0 px-3" style="cursor: pointer;" onclick="javascript: window.location.href = '<?= $loja['site'] ?>categoria/<?= $cat['id'] ?>';">
                    <div style="background-color: <?= $cor_categoria; ?>;  --cor-categoria: <?= $cor_categoria; ?>;" class="header-linha"><?= $cat['nome'] ?></div>

                    <div class="container-img-linha" style="border: 5px solid <?= $cor_categoria; ?>">
                        <img src='<?= $loja['site'] ?>imagens/categorias/<?= $cat['imagem'] ?>'>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="mt-5">
            <a href="<?= $loja['site'] ?>categorias">
                <button class="nossas-linhas-btn">Solicite um orçamento</button>
            </a>
        </div>
    </div>
</section>