<!-- CSS -->
<link rel="stylesheet" href="modulos/categorias/css/style.css">

<?php 

//PEGA OS DADOS DO USUÁRIO ATUAL NA SESSION
$nivel_usuario         = filter_var($_SESSION['nivel']);
$identificador_usuario = filter_var($_SESSION['identificador']);

//SE FOR USUÁRIO, DESLOGA
if($nivel_usuario == 'U'){
    echo "<script>location.href='logout.php';</script>";
}

//BUSCA AS CATEGORIAS PAIS CADASTRADAS
$categorias_1 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 1 ORDER BY ordem ASC'); 

//VERIFICA SE TEM PROMOÇÃO VENCIDA E DESATIVA
$busca_categorias_promocao = mysqli_query($conn, "SELECT categoria.id AS id_categoria, promocao.id AS id_promocao, validade, promocao.status FROM categoria INNER JOIN promocao ON categoria.id = promocao.id_categoria WHERE promocao = 1 AND validade < DATE(NOW())");
while($categorias_promocao = mysqli_fetch_array($busca_categorias_promocao)){
	if((strtotime($categorias_promocao['validade']) < strtotime(date('Y-m-d'))) & $categorias_promocao['status'] == 1){
		//ALTERA O STATUS DA PROMOÇÃO NA CATEGORIA
		mysqli_query($conn, "UPDATE categoria SET promocao = 0 WHERE id = ".$categorias_promocao["id_categoria"]);
		//ENCERRA A PROMOÇÃO
		mysqli_query($conn, "UPDATE promocao SET data_desativacao = NOW(), status = 0 WHERE id = ".$categorias_promocao['id_promocao']);
	}
}

//BUSCA AS TAGS
$array_tags = array();
$tags = mysqli_query($conn, "SELECT * FROM tag ORDER BY nome ASC");
while($tag = mysqli_fetch_array($tags)){
	$array_tags[] = $tag['nome'];
}

?>

<!--SECTION CATEGORIAS-->
<section id="categorias">

    <div class="container-fluid">

		<!-- ABAS -->
		<ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="tab-categorias" data-toggle="tab" href="#conteudo-tab-categorias" role="tab" aria-controls="conteudo-tab-categorias" aria-selected="true">Categorias</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="tab-tags" data-toggle="tab" href="#conteudo-tab-tags" role="tab" aria-controls="conteudo-tab-tags" aria-selected="false">Tags</a>
			</li>
		</ul>
		
		<div class="tab-content">

			<div class="tab-pane active" id="conteudo-tab-categorias" role="tabpanel" aria-labelledby="tab-categorias">

				<!-- ROW DO TÍTULO -->
				<div class="row">
					<div class="col-6">    
						<div id="admin-titulo-pagina">Categorias</div>
					</div>			
					<div class="col-6 text-right">    
						<button type="button" class="btn btn-dark btn-top-right" data-toggle="modal" data-target="#modal-add-categoria">NOVA CATEGORIA</button>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-12">   
						<ul>
							<li><b>AJUDA:</b></li>
							<li>1 - Para adicionar subcategorias, arraste uma categoria filha para dentro da mãe desejada.</li>
							<li>2 - O sistema suporta até 10 níveis de categorias.</li>
							<li>3 - A ordem das categorias de cima pra baixo mostradas aqui nessa tela, será a ordem assumida pelo site para visualização.</li>
							<li>4 - Para alterar o nome da categoria, basta clicar em cima do mesmo e alterar.</li>
						</ul>
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<div id="0" nivel="0">
							<div id="ninho" class="list-group col nested-sortable">
								<?php while($categoria_1 = mysqli_fetch_array($categorias_1)){ ?>	
									<div id="<?= $categoria_1['id'] ?>" identificador="<?= $categoria_1['identificador'] ?>" pai="<?= $categoria_1['pai'] ?>"class="list-group-item nested nested-1" nivel="1" ordem="<?= $categoria_1['ordem'] ?>">
										<input id="nome-<?= $categoria_1['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_1['identificador'] ?>');" value="<?= $categoria_1['nome'] ?>" style="border: 0px!important;">
										<a class="float-right" href="javascript: excluiCategoria('<?= $categoria_1['identificador'] ?>','<?= $categoria_1['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a>
										<a class="float-right" href="javascript: imagemCategoria('<?= $categoria_1['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_1['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a>
										<a id="promocao-<?= $categoria_1['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_1['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_1['identificador'] ?>','<?= $categoria_1['nome'] ?>','<?= $categoria_1['promocao'] ?>');" title="<?php if($categoria_1['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
										<div class="list-group nested-sortable">
											<?php $categorias_2 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 2 AND pai = '.$categoria_1['id'].' ORDER BY ordem ASC'); ?>
											<?php while($categoria_2 = mysqli_fetch_array($categorias_2)){ ?>										
												<div id="<?= $categoria_2['id'] ?>"  identificador="<?= $categoria_2['identificador'] ?>" pai="<?= $categoria_2['pai'] ?>" class="list-group-item nested nested-2" nivel="2" ordem="<?= $categoria_2['ordem'] ?>"><input id="nome-<?= $categoria_2['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_2['identificador'] ?>');" value="<?= $categoria_2['nome'] ?>" style="border: 0px!important;"><a class="float-right" href="javascript: excluiCategoria('<?= $categoria_2['identificador'] ?>','<?= $categoria_2['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a><a class="float-right" href="javascript: imagemCategoria('<?= $categoria_2['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_2['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a><a id="promocao-<?= $categoria_2['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_2['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_2['identificador'] ?>','<?= $categoria_2['nome'] ?>','<?= $categoria_2['promocao'] ?>');" title="<?php if($categoria_2['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
													<div class="list-group nested-sortable">	
														<?php $categorias_3 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 3 AND pai = '.$categoria_2['id'].' ORDER BY ordem ASC'); ?>
														<?php while($categoria_3 = mysqli_fetch_array($categorias_3)){ ?>										
															<div id="<?= $categoria_3['id'] ?>"  identificador="<?= $categoria_3['identificador'] ?>" pai="<?= $categoria_3['pai'] ?>" class="list-group-item nested nested-3" nivel="3" ordem="<?= $categoria_3['ordem'] ?>"><input id="nome-<?= $categoria_3['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_3['identificador'] ?>');" value="<?= $categoria_3['nome'] ?>" style="border: 0px!important;"><a class="float-right" href="javascript: excluiCategoria('<?= $categoria_3['identificador'] ?>','<?= $categoria_3['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a><a class="float-right" href="javascript: imagemCategoria('<?= $categoria_3['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_3['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a><a id="promocao-<?= $categoria_3['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_3['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_3['identificador'] ?>','<?= $categoria_3['nome'] ?>','<?= $categoria_3['promocao'] ?>');" title="<?php if($categoria_3['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
																<div class="list-group nested-sortable">	
																	<?php $categorias_4 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 4 AND pai = '.$categoria_3['id'].' ORDER BY ordem ASC'); ?>
																	<?php while($categoria_4 = mysqli_fetch_array($categorias_4)){ ?>										
																		<div id="<?= $categoria_4['id'] ?>"  identificador="<?= $categoria_4['identificador'] ?>" pai="<?= $categoria_4['pai'] ?>" class="list-group-item nested nested-4" nivel="4" ordem="<?= $categoria_4['ordem'] ?>"><input id="nome-<?= $categoria_4['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_4['identificador'] ?>');" value="<?= $categoria_4['nome'] ?>" style="border: 0px!important;"><a class="float-right" href="javascript: excluiCategoria('<?= $categoria_4['identificador'] ?>','<?= $categoria_4['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a><a class="float-right" href="javascript: imagemCategoria('<?= $categoria_4['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_4['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a><a id="promocao-<?= $categoria_4['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_4['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_4['identificador'] ?>','<?= $categoria_4['nome'] ?>','<?= $categoria_4['promocao'] ?>');" title="<?php if($categoria_4['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
																			<div class="list-group nested-sortable">																				
																				<?php $categorias_5 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 5 AND pai = '.$categoria_4['id'].' ORDER BY ordem ASC'); ?>
																				<?php while($categoria_5 = mysqli_fetch_array($categorias_5)){ ?>										
																					<div id="<?= $categoria_5['id'] ?>"  identificador="<?= $categoria_5['identificador'] ?>" pai="<?= $categoria_5['pai'] ?>" class="list-group-item nested nested-5" nivel="5" ordem="<?= $categoria_5['ordem'] ?>"><input id="nome-<?= $categoria_5['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_5['identificador'] ?>');" value="<?= $categoria_5['nome'] ?>" style="border: 0px!important;"><a class="float-right" href="javascript: excluiCategoria('<?= $categoria_5['identificador'] ?>','<?= $categoria_5['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a><a class="float-right" href="javascript: imagemCategoria('<?= $categoria_5['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_5['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a><a id="promocao-<?= $categoria_5['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_5['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_5['identificador'] ?>','<?= $categoria_5['nome'] ?>','<?= $categoria_5['promocao'] ?>');" title="<?php if($categoria_5['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
																						<div class="list-group nested-sortable">	
																							<?php $categorias_6 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 6 AND pai = '.$categoria_5['id'].' ORDER BY ordem ASC'); ?>
																							<?php while($categoria_6 = mysqli_fetch_array($categorias_6)){ ?>										
																								<div id="<?= $categoria_6['id'] ?>"  identificador="<?= $categoria_6['identificador'] ?>" pai="<?= $categoria_6['pai'] ?>" class="list-group-item nested nested-6" nivel="6" ordem="<?= $categoria_6['ordem'] ?>"><input id="nome-<?= $categoria_6['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_6['identificador'] ?>');" value="<?= $categoria_6['nome'] ?>" style="border: 0px!important;"><a class="float-right" href="javascript: excluiCategoria('<?= $categoria_6['identificador'] ?>','<?= $categoria_6['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a><a class="float-right" href="javascript: imagemCategoria('<?= $categoria_6['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_6['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a><a id="promocao-<?= $categoria_6['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_6['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_6['identificador'] ?>','<?= $categoria_6['nome'] ?>','<?= $categoria_6['promocao'] ?>');" title="<?php if($categoria_6['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
																									<div class="list-group nested-sortable">																									
																									<?php $categorias_7 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 7 AND pai = '.$categoria_6['id'].' ORDER BY ordem ASC'); ?>
																									<?php while($categoria_7 = mysqli_fetch_array($categorias_7)){ ?>										
																										<div id="<?= $categoria_7['id'] ?>"  identificador="<?= $categoria_7['identificador'] ?>" pai="<?= $categoria_7['pai'] ?>" class="list-group-item nested nested-7" nivel="7" ordem="<?= $categoria_7['ordem'] ?>"><input id="nome-<?= $categoria_7['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_7['identificador'] ?>');" value="<?= $categoria_7['nome'] ?>" style="border: 0px!important;"><a class="float-right" href="javascript: excluiCategoria('<?= $categoria_7['identificador'] ?>','<?= $categoria_7['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a><a class="float-right" href="javascript: imagemCategoria('<?= $categoria_7['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_7['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a><a id="promocao-<?= $categoria_7['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_7['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_7['identificador'] ?>','<?= $categoria_7['nome'] ?>','<?= $categoria_7['promocao'] ?>');" title="<?php if($categoria_7['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
																											<div class="list-group nested-sortable">																										
																												<?php $categorias_8 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 8 AND pai = '.$categoria_7['id'].' ORDER BY ordem ASC'); ?>
																												<?php while($categoria_8 = mysqli_fetch_array($categorias_8)){ ?>										
																													<div id="<?= $categoria_8['id'] ?>"  identificador="<?= $categoria_8['identificador'] ?>" pai="<?= $categoria_8['pai'] ?>" class="list-group-item nested nested-8" nivel="8" ordem="<?= $categoria_8['ordem'] ?>"><input id="nome-<?= $categoria_8['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_8['identificador'] ?>');" value="<?= $categoria_8['nome'] ?>" style="border: 0px!important;"><a class="float-right" href="javascript: excluiCategoria('<?= $categoria_8['identificador'] ?>','<?= $categoria_8['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a><a class="float-right" href="javascript: imagemCategoria('<?= $categoria_8['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_8['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a><a id="promocao-<?= $categoria_8['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_8['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_8['identificador'] ?>','<?= $categoria_8['nome'] ?>','<?= $categoria_8['promocao'] ?>');" title="<?php if($categoria_8['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
																														<div class="list-group nested-sortable">																										
																															<?php $categorias_9 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 9 AND pai = '.$categoria_8['id'].' ORDER BY ordem ASC'); ?>
																															<?php while($categoria_9 = mysqli_fetch_array($categorias_9)){ ?>										
																																<div id="<?= $categoria_9['id'] ?>"  identificador="<?= $categoria_9['identificador'] ?>" pai="<?= $categoria_9['pai'] ?>" class="list-group-item nested nested-9" nivel="9" ordem="<?= $categoria_9['ordem'] ?>"><input id="nome-<?= $categoria_9['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_9['identificador'] ?>');" value="<?= $categoria_9['nome'] ?>" style="border: 0px!important;"><a class="float-right" href="javascript: excluiCategoria('<?= $categoria_9['identificador'] ?>','<?= $categoria_9['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a><a class="float-right" href="javascript: imagemCategoria('<?= $categoria_9['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_9['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a><a id="promocao-<?= $categoria_9['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_9['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_9['identificador'] ?>','<?= $categoria_9['nome'] ?>','<?= $categoria_9['promocao'] ?>');" title="<?php if($categoria_9['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
																																	<div class="list-group nested-sortable">																										
																																		<?php $categorias_10 = mysqli_query($conn, 'SELECT * FROM categoria WHERE nivel = 10 AND pai = '.$categoria_9['id'].' ORDER BY ordem ASC'); ?>
																																		<?php while($categoria_10 = mysqli_fetch_array($categorias_10)){ ?>										
																																			<div id="<?= $categoria_10['id'] ?>"  identificador="<?= $categoria_10['identificador'] ?>" pai="<?= $categoria_10['pai'] ?>" class="list-group-item nested nested-10" nivel="10" ordem="<?= $categoria_10['ordem'] ?>"><input id="nome-<?= $categoria_10['identificador'] ?>" class="nome-categoria text-capitalize" type="text" onfocus="javascript: $(this).select();" onblur="javascript: trocaNome('<?= $categoria_10['identificador'] ?>');" value="<?= $categoria_10['nome'] ?>" style="border: 0px!important;"><a class="float-right" href="javascript: excluiCategoria('<?= $categoria_10['identificador'] ?>','<?= $categoria_10['nome'] ?>');" title="Excluir"><img class="acao-excluir" src="<?= $loja['site'] ?>imagens/acao-excluir-branca.png" alt="Excluir"></a><a class="float-right" href="javascript: imagemCategoria('<?= $categoria_10['identificador'] ?>');" title="Adicionar imagem de capa"><img class="acao-add-imagem <?php if($categoria_10['imagem'] != ''){ echo 'categoria-com-imagem';} ?>" src="<?= $loja['site'] ?>imagens/acao-add-imagem.png" alt="Adicionar imagem de capa"></a><a id="promocao-<?= $categoria_10['identificador'] ?>" class="float-right btn-promocao <?php if($modo_whatsapp){ if($loja['modo_whatsapp_preco'] == 0){ echo 'd-none'; } } ?> <?php if($categoria_10['promocao'] == 1){ echo 'promocao-ativada'; } ?>" href="javascript: promocaoCategoria('<?= $categoria_10['identificador'] ?>','<?= $categoria_10['nome'] ?>','<?= $categoria_10['promocao'] ?>');" title="<?php if($categoria_10['promocao'] == 1){ echo 'Desativar'; } else { echo 'Ativar'; } ?> promoção">Promoção</a>
																																				<div class="list-group nested-sortable"></div>
																																			</div>
																																		<?php } ?>	
																																	</div>
																																</div>
																															<?php } ?>				
																														</div>
																													</div>
																												<?php } ?>																										
																											</div>
																										</div>
																									<?php } ?>
																									</div>
																								</div>
																							<?php } ?>
																						</div>
																					</div>
																				<?php } ?>
																			</div>
																		</div>
																	<?php } ?>
																</div>
															</div>
														<?php } ?>
													</div>
												</div>
											<?php } ?>
										</div>
									</div>
								<?php } ?>
							</div>	
						</div>
					</div>
				</div>
				
			</div>

			<div class="tab-pane" id="conteudo-tab-tags" role="tabpanel" aria-labelledby="tab-tags">
				
				<!-- ROW DO TÍTULO -->
				<div class="row">
					<div class="col-6">    
						<div id="admin-titulo-pagina">Tags</div>
					</div>	
				</div>

				<div class="row mb-3">
					<div class="col-12">   
						<ul>
							<li><b>SOBRE:</b></li>
							<li>Crie tags por aqui e depois adicione-as aos seus produtos.</li>
							<li>Além das categorias, elas servem para agrupar os produtos e ajuda-los a serem encontrados mais facilmente.</li>
						</ul>
					</div>
				</div>
				<div data-no-duplicate="true" data-pre-tags-separator="\n" data-no-duplicate-text="Tag duplicada" data-type-zone-class="type-zone"	data-tag-box-class="tagging" data-edit-on-delete="true" id="tagBox"></div>				
				<small>Para adicionar novas tags, escreva as palavras que deseja dentro do campo acima e separe-as com a tecla ENTER.</small>
				<script>var tags = '<?= json_encode($array_tags) ?>';</script>
			</div>

    	</div>

    </div>

</section>

<!-- MODAL ADIÇÃO DE CATEGORIA -->
<div class="modal fade" id="modal-add-categoria" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered  modal-sm" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle">Nova categoria</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-12">
					<div class="form-group">
						<label for="nova-categoria">Nome</label>
						<input type="text" id="nova-categoria" class="form-control text-capitalize">
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-dark" onclick="javascript: novaCategoria();">Cadastrar</button>
		</div>
		</div>
	</div>
</div>

<!-- MODAL ADIÇÃO DE IMAGEM -->
<div class="modal fade" id="modal-add-imagem" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<ul>
				<li><h5 class="modal-title" id="exampleModalLongTitle">Adicionar imagem</h5></li>
				<li><small>Adicione uma imagem de capa para os ícone da categoria para dispositivos móveis.</small></li>
				<li><small><b>OBS: PARA REMOVER A IMAGEM, CLIQUE EM CADASTRAR COM O CAMPO IMAGEM EM BRANCO.</b></small></li>
			</ul>	
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<form id="form-cadastro-imagem-categoria" enctype="multipart/form-data" action="modulos/categorias/php/cadastro-imagem-categoria.php" method="POST">
				<input type="hidden" id="identificador-categoria-imagem" name="identificador-categoria">
				<div class="row">
					<div class="col-12">
						<div class="form-group">
							<label for="imagem">Imagem (RECOMENDADO 24x24px) <span class="campo-obrigatorio">*</span></label>
							<input type="file" name="imagem" id="imagem" class="imagem form-control-file" accept="image/png, image/jpeg" onchange="javascript: inputFileChange();">
							<input type="text" name="arquivo" id="arquivo" class="arquivo" placeholder="Selecionar arquivo" readonly="readonly">
							<input type="button" id="btn-escolher" class="btn-escolher" value="ESCOLHER" onclick="javascript: inputFileEscolher();">
						</div>
					</div>
				</div>
			</form>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-dark" onclick="javascript: $('#form-cadastro-imagem-categoria').submit(); ">Cadastrar</button>
		</div>
		</div>
	</div>
</div>

<!-- MODAL ADIÇÃO DE PROMOÇÃO -->
<div class="modal fade" id="modal-add-promocao" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered  modal-sm" role="document">
		<div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title" id="exampleModalLongTitle">Promoção</h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="modal-body">
			<input type="hidden" id="identificador-categoria">
			<div class="row mb-2">
				<div class="col-12">
					Categoria: <span id="nome-categoria"></span>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="form-group">
						<label for="porcentagem-desconto">% de desconto</label>
						<input type="number" id="porcentagem-desconto" class="form-control">
					</div>
				</div>
				<div class="col-12">
					<div class="form-group">
						<label for="validade">Validade</label>
						<input type="text" id="validade" class="form-control">
						<small>Formato: dd/mm/aaaa</small>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-dark btn-altera-promocao" onclick="javascript: alterarStatusPromocaoCategoria(1,'');">Confirmar</button>
		</div>
		</div>
	</div>
</div>

<!--SCRIPTS-->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script type="text/javascript" src="modulos/categorias/js/tagging.min.js"></script>
<script type="text/javascript" src="modulos/categorias/js/scripts.js"></script>