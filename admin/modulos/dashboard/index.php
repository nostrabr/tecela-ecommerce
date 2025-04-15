<!--CSS
<link rel="stylesheet" href="https://ga-dev-tools.appspot.com/public/css/chartjs-visualizations.css">-->
<link rel="stylesheet" href="modulos/dashboard/css/style.css">

<?php 

$data_inicio = filter_input(INPUT_GET, 'data-inicio');
$data_fim    = filter_input(INPUT_GET, 'data-fim');

if(!isset($data_inicio) & !isset($data_fim)){
    $data_inicio = date('Y-m-01');
    $data_fim    = date('Y-m-d');
}

?>

<!--SECTION DASHBOARD-->
<section id="dashboard">
  
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">    
                <div id="admin-titulo-pagina"><?= $loja['nome'] ?></div>
            </div>
            <div class="col-6 text-right d-none justify-content-end">
                <div id="active-users-container"></div>
            </div>
        </div>
    </div>

    <div id="container-data-site-periodo" class="container-data-site container-fluid mb-4">    
    
        <div class="row admin-subtitulo"><div class="col-12">PERÍODO</div></div>
      
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label for="data-site-data-inicio">Inicio</label>
                            <input type="date" class="form-control" id="data-site-data-inicio" value="<?= $data_inicio ?>">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label for="data-site-data-fim">Fim</label>
                            <input type="date" class="form-control" id="data-site-data-fim" value="<?= $data_fim ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="container-data-site-cliques" class="container-data-site container-fluid mb-4">    
        
        <div class="row admin-subtitulo"><div class="col-12">CLIQUES NO WHATSAPP</div></div>  
        
        <div class="row">
            <div class="col-6 col-md-3 mb-3 mb-md-0">
                <ul class="data-site-vendas-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-whatsapp-flutuante.png"></li>
                    <li>
                        <ul class="d-block data-site-vendas-totais-descricao">
                            <li id="data-site-cliques-whatsapp-flutuante" class="data-site-vendas-totais-total"></li>
                            <li class="data-site-vendas-totais-legenda">Flutuante</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-6 col-md-3 mb-3 mb-md-0">
                <ul class="data-site-vendas-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-whatsapp-rodape.png"></li>
                    <li>
                        <ul class="d-block data-site-vendas-totais-descricao">
                            <li id="data-site-cliques-whatsapp-rodape" class="data-site-vendas-totais-total"></li>
                            <li class="data-site-vendas-totais-legenda">Rodapé</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-6 col-md-3 mb-3 mb-md-0">
                <ul class="data-site-vendas-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-whatsapp-contato.png"></li>
                    <li>
                        <ul class="d-block data-site-vendas-totais-descricao">
                            <li id="data-site-cliques-whatsapp-contato" class="data-site-vendas-totais-total"></li>
                            <li class="data-site-vendas-totais-legenda">Contato</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <ul class="data-site-vendas-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-whatsapp-total.png"></li>
                    <li>
                        <ul class="d-block data-site-vendas-totais-descricao">
                            <li id="data-site-cliques-whatsapp-total" class="data-site-vendas-totais-total"></li>
                            <li class="data-site-vendas-totais-legenda">Total</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <div id="container-data-site-performance" class="container-data-site container-fluid mb-4">    
    
        <?php if($modo_whatsapp){ ?>
            <div class="row admin-subtitulo"><div class="col-12">GERAL</div></div>        
        <?php } else { ?>
            <div class="row admin-subtitulo"><div class="col-12">PERFORMANCE</div></div>  
        <?php } ?>

        <div class="row">
            <div class="col-12 col-md-3 mb-3 mb-md-0">
                <ul class="data-site-vendas-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-vendas-visitas.png"></li>
                    <li>
                        <ul class="d-block data-site-vendas-totais-descricao">
                            <li id="data-site-vendas-totais-total-visitas" class="data-site-vendas-totais-total"></li>
                            <li class="data-site-vendas-totais-legenda">Visitas</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-md-3 mb-3 mb-md-0 <?php if($modo_whatsapp){ echo 'd-none'; } ?>">
                <ul class="data-site-vendas-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-vendas-pedidos.png"></li>
                    <li>
                        <ul class="d-block data-site-vendas-totais-descricao">
                            <li id="data-site-vendas-totais-total-pedidos" class="data-site-vendas-totais-total"></li>
                            <li class="data-site-vendas-totais-legenda">Pedidos pagos</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-md-3 mb-3 mb-md-0 <?php if($modo_whatsapp){ echo 'd-none'; } ?>">
                <ul class="data-site-vendas-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-vendas-totais.png"></li>
                    <li>
                        <ul class="d-block data-site-vendas-totais-descricao">
                            <li id="data-site-vendas-totais-total-vendido" class="data-site-vendas-totais-total"></li>
                            <li class="data-site-vendas-totais-legenda">Total vendido</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-md-3 <?php if($modo_whatsapp){ echo 'd-none'; } ?>">
                <ul class="data-site-vendas-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-vendas-conversao.png"></li>
                    <li>
                        <ul class="d-block data-site-vendas-totais-descricao">
                            <li id="data-site-vendas-totais-total-conversao" class="data-site-vendas-totais-total"></li>
                            <li class="data-site-vendas-totais-legenda">Conversão</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

    </div>
    
    <div id="container-data-site-vendas" class="container-data-site container-fluid mb-4 <?php if($modo_whatsapp){ echo 'd-none'; } ?>">   

        <div class="row admin-subtitulo"><div class="col-12">VENDAS</div></div>

        <div class="row mb-4">
            <div class="col-12">
                <label for="data-site-vendas-pedidos">PEDIDOS POR STATUS</label>
                <div id="data-site-vendas-pedidos"></div>
            </div>            
        </div>

        <div class="row">
            <div class="col-12 col-md-6 mb-4 mb-md-0">
                <label for="data-site-vendas-formas-pagamento">FORMAS DE PAGAMENTO</label>
                <div id="data-site-vendas-formas-pagamento"></div>
            </div>    
            <div class="col-12 col-md-6">
                <label for="data-site-vendas-formas-entrega">FORMAS DE ENTREGA</label>
                <div id="data-site-vendas-formas-entrega"></div>
            </div>           
        </div>

    </div>
    
    <div id="container-data-site-seo" class="container-data-site container-fluid mb-4">   

        <div class="row admin-subtitulo"><div class="col-12">VISITAS</div></div>

        <div class="row">           
            <div class="col-12 col-md-6 mb-4">
                <div id="data-site-geolocalizacao-clientes-cidade"></div>
            </div>          
            <div class="col-12 col-md-6 mb-4">
                <div id="data-site-geolocalizacao-clientes-estado"></div>
            </div>   
            <div class="col-12 mb-4">
                <label for="data-site-geolocalizacao-clientes-estado-mapa" id="data-site-geolocalizacao-clientes-estado-mapa-label">MAPA DE ACESSOS POR ESTADO</label>
                <div id="data-site-geolocalizacao-clientes-estado-mapa-label-carregando">Carregando...</div>
                <div id="data-site-geolocalizacao-clientes-estado-mapa"></div>
            </div>
        </div>

        <div class="row">           
            <div class="col-12 col-md-6 mb-4 mb-md-0">
                <label for="data-site-dispositivos">DISPOSITIVO</label>
                <div id="data-site-dispositivos"></div>
            </div>          
            <div class="col-12 col-md-6">
                <label for="data-site-resolucoes">RESOLUÇÃO</label>
                <div id="data-site-resolucoes"></div>
            </div> 
        </div>

    </div>

    <div id="container-data-site-produtos" class="container-data-site container-fluid mb-4 mb-md-0">

        <div class="row admin-subtitulo"><div class="col-12">PRODUTOS</div></div>
        
        <?php
                            
        $busca_total_cadastrado  = mysqli_query($conn, "SELECT COUNT(id) AS total FROM produto");
        $busca_total_ativo       = mysqli_query($conn, "SELECT COUNT(id) AS total FROM produto WHERE status = 1");
        $busca_total_inativo     = mysqli_query($conn, "SELECT COUNT(id) AS total FROM produto WHERE status = 0");
        $busca_total_sem_estoque = mysqli_query($conn, "SELECT COUNT(id) AS total FROM produto WHERE estoque = 0");
        $total_cadastrado        = mysqli_fetch_array($busca_total_cadastrado);
        $total_ativo             = mysqli_fetch_array($busca_total_ativo);
        $total_inativo           = mysqli_fetch_array($busca_total_inativo);
        $total_sem_estoque       = mysqli_fetch_array($busca_total_sem_estoque);
        
        ?>

        <div class="row mb-4">
            <div class="col-6 col-md-3 mb-3 mb-md-0">
                <ul class="data-site-produto-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-produto-lista.png"></li>
                    <li>
                        <ul class="d-block data-site-produto-totais-descricao">
                            <li class="data-site-produto-totais-total"><?= $total_cadastrado['total'] ?></li>
                            <li class="data-site-produto-totais-legenda">Cadastrados</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-6 col-md-3 mb-3 mb-md-0">
                <ul class="data-site-produto-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-produto-on.png"></li>
                    <li>
                        <ul class="d-block data-site-produto-totais-descricao">
                            <li class="data-site-produto-totais-total"><?= $total_ativo['total'] ?></li>
                            <li class="data-site-produto-totais-legenda">Ativos</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <ul class="data-site-produto-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-produto-off.png"></li>
                    <li>
                        <ul class="d-block data-site-produto-totais-descricao">
                            <li class="data-site-produto-totais-total"><?= $total_inativo['total'] ?></li>
                            <li class="data-site-produto-totais-legenda">Inativos</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <ul class="data-site-produto-totais d-inline-flex align-items-center">
                    <li class="mr-3"><img src="<?= $loja['site'] ?>imagens/dashboard-produto-out-of-stock.png"></li>
                    <li>
                        <ul class="d-block data-site-produto-totais-descricao">
                            <li class="data-site-produto-totais-total"><?= $total_sem_estoque['total'] ?></li>
                            <li class="data-site-produto-totais-legenda">Sem estoque</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    
        <div class="row <?php if($modo_whatsapp){ echo 'd-none'; } ?>">
            <div class="col-12 col-md-6 mb-4">
                <div id="data-site-mais-visitados"></div>
            </div>
            <div class="col-12 col-md-6 mb-4">
                <div id="data-site-mais-vendidos"></div>
            </div>  
        </div>        
    
        <div class="row <?php if(!$modo_whatsapp){ echo 'd-none'; } ?>">
            <div class="col-12 col-md-6 mb-4">
                <div id="data-site-mais-visitados-whatsapp"></div>
            </div>
            <div class="col-12 col-md-6 mb-4">
                <div id="data-site-mais-whatsapp"></div>
            </div>  
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                <div id="data-site-mais-pesquisados"></div>
            </div>  
        </div>

    </div>

    <div id="container-google-analytics" class="container-fluid mt-5 d-none">
    
        <div class="row admin-subtitulo"><div class="col-12">GOOGLE ANALYTICS</div></div>

        <div id="view-name"></div>
        <div id="embed-api-auth-container"></div>
        <div class="d-none" id="view-selector-container"></div>
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="Chartjs">
                    <p>SESSÕES</p>
                    <div id="date-range-selector-1-container"></div>
                    <div id="data-chart-1-container"></div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12 col-md-6">
                <div class="Chartjs">
                    <p>ESTA SEMANA x SEMANA PASSADA (por sessões)</p>
                    <figure class="Chartjs-figure" id="chart-1-container"></figure>
                    <ol class="Chartjs-legend text-left" id="legend-1-container"></ol>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="Chartjs">
                    <p>ESTE ANO x ANO PASSADO (por usuários)</p>
                    <figure class="Chartjs-figure" id="chart-2-container"></figure>
                    <ol class="Chartjs-legend text-left" id="legend-2-container"></ol>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12 col-md-4">
                <div class="Chartjs">
                    <p class="text-center mb-4">Top Cidades</p>
                    <figure class="Chartjs-figure" id="chart-5-container"></figure>
                    <ol class="Chartjs-legend" id="legend-5-container"></ol>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="Chartjs">
                    <p class="text-center mb-4">Top Países</p>
                    <figure class="Chartjs-figure" id="chart-4-container"></figure>
                    <ol class="Chartjs-legend" id="legend-4-container"></ol>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="Chartjs">
                    <p class="text-center mb-4">Top Browsers</p>
                    <figure class="Chartjs-figure" id="chart-3-container"></figure>
                    <ol class="Chartjs-legend" id="legend-3-container"></ol>
                </div>
            </div>
        </div>

    </div>

</section>

<!--SCRIPTS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript" src="modulos/dashboard/js/scripts.js"></script>