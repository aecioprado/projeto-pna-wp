<?php
/**
 * Prepara o ambiente LOCAL: executa a configuração inicial do plugin
 * (a mesma da tela Ferramentas → PNA: configuração inicial) e cria o
 * conteúdo de exemplo:
 * páginas do menu, página inicial e de postagens, cadastro aberto,
 * postagens de exemplo, a página privada "Guia de estilo" e, com o
 * plugin pna-core ativo, pets, um membro e solicitações de exemplo.
 *
 * Use apenas no ambiente local: as postagens de exemplo não devem ir para a produção.
 *
 * Uso: npm run conteudo
 * Pode ser executado várias vezes: o que já existe não é duplicado.
 *
 * @package PNA
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

if ( ! function_exists( 'pna_core_configuracao_inicial' ) ) {
	WP_CLI::error( 'O plugin pna-core precisa estar ativo. Rode "npm start" e tente de novo.' );
}

// Mesma configuração da tela Ferramentas → PNA: configuração inicial.
foreach ( pna_core_configuracao_inicial() as $pna_linha ) {
	WP_CLI::log( $pna_linha );
}
WP_CLI::log( 'Configuração inicial conferida.' );

// No ambiente local o site fica sempre aberto (o modo pré-lançamento é para a hospedagem).
update_option( 'pna_core_pre_lancamento', '0' );

// Página privada "Guia de estilo" (só administradores veem).
if ( ! get_page_by_path( 'guia-de-estilo' ) ) {
	wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'private',
			'post_name'    => 'guia-de-estilo',
			'post_title'   => 'Guia de estilo',
			'post_content' => '<!-- wp:pattern {"slug":"pna/guia-de-estilo"} /-->',
		)
	);
	WP_CLI::log( 'Criada: Guia de estilo (privada)' );
} else {
	WP_CLI::log( 'Já existe: Guia de estilo' );
}

/**
 * Importa uma foto de scripts/exemplos/ e a define como imagem destacada.
 * Não faz nada se o conteúdo já tiver imagem destacada.
 *
 * @param int    $post_id   Post, pet ou página.
 * @param string $arquivo   Caminho da foto.
 * @param string $descricao Título do arquivo na biblioteca de mídia.
 */
function pna_local_aplicar_foto( $post_id, $arquivo, $descricao ) {
	if ( has_post_thumbnail( $post_id ) || ! file_exists( $arquivo ) ) {
		return;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// O WordPress move o arquivo ao importar: trabalha numa cópia temporária.
	$temp = wp_tempnam( basename( $arquivo ) );
	copy( $arquivo, $temp );
	$anexo = media_handle_sideload(
		array(
			'name'     => basename( $arquivo ),
			'tmp_name' => $temp,
		),
		$post_id,
		$descricao
	);
	if ( is_wp_error( $anexo ) ) {
		WP_CLI::warning( 'Foto não importada (' . basename( $arquivo ) . '): ' . $anexo->get_error_message() );
		return;
	}
	set_post_thumbnail( $post_id, $anexo );
	WP_CLI::log( 'Foto aplicada: ' . get_the_title( $post_id ) );
}

// Postagens de exemplo: as 4 do Figma e mais 6, para ver a paginação
// de /postagens/ (6 por página). A primeira da lista é a mais recente.
$pna_postagens = array(
	'quais-alimentos-dar-aos-pets'      => array( 'Quais alimentos dar aos pets?', 'Assim como ocorre com as pessoas, a nutrição é essencial para manter a saúde física e mental de cães e gatos. A alimentação correta impacta positivamente a qualidade de vida.' ),
	'cuidados-com-filhotes-de-cachorro' => array( 'Cuidados com filhotes de cachorro', 'Este post apresenta algumas formas de adaptar o pequeno à sua nova casa, com dicas de cuidado para o tutor quanto à alimentação, higiene e gasto de energia.' ),
	'gravidez-felina'                   => array( 'Gravidez felina', 'A gestação de gato é um assunto que merece muita atenção! Nesse período, a futura mamãe fica mais vulnerável e, por isso, precisamos tomar alguns cuidados.' ),
	'vacinas-obrigatorias'              => array( 'Vacinas obrigatórias para cães e gatos', 'A vacinação em pets protege contra diversas doenças graves e contagiosas, prevenindo riscos que podem comprometer a vida dos animais e até mesmo afetar pessoas.' ),
	'castracao-mitos-e-verdades'        => array( 'Castração: mitos e verdades', 'A castração evita ninhadas indesejadas e reduz o risco de algumas doenças. Veja o que é mito e o que é verdade sobre o procedimento e a recuperação do pet.' ),
	'adaptacao-do-gato-adotado'         => array( 'Os primeiros dias do gato adotado', 'É normal o gato se esconder quando chega a uma casa nova. Um cantinho tranquilo, paciência e rotina ajudam o novo morador a se sentir seguro.' ),
	'passeios-com-o-cachorro'           => array( 'Passeios com o cachorro', 'Passear gasta energia, estimula o faro e fortalece o vínculo com o tutor. Use sempre guia e coleira com identificação e evite os horários mais quentes.' ),
	'cuidados-no-calor'                 => array( 'Cuidados com os pets no calor', 'No calor do Agreste, água fresca sempre à disposição e sombra são indispensáveis. Fique atento a sinais de cansaço excessivo e respiração ofegante.' ),
	'brincadeiras-para-gatos'           => array( 'Brincadeiras para gatos', 'Brincar é uma necessidade do gato: gasta energia, reduz o estresse e evita comportamentos destrutivos. Brinquedos simples feitos em casa já fazem sucesso.' ),
	'escovacao-e-higiene'               => array( 'Escovação e higiene', 'Escovar o pelo remove fios soltos, evita nós e é um ótimo momento de carinho. A frequência ideal depende do tipo de pelagem de cada animal.' ),
);

$pna_ordem = 0;
foreach ( $pna_postagens as $pna_slug => $pna_post ) {
	++$pna_ordem;
	$pna_existente = get_page_by_path( $pna_slug, OBJECT, 'post' );
	if ( $pna_existente ) {
		$pna_post_id = $pna_existente->ID;
	} else {
		$pna_post_id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'publish',
				'post_name'    => $pna_slug,
				'post_title'   => $pna_post[0],
				'post_excerpt' => $pna_post[1],
				'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $pna_post[1] ) . '</p><!-- /wp:paragraph -->',
				'post_date'    => wp_date( 'Y-m-d H:i:s', time() - ( $pna_ordem * DAY_IN_SECONDS ) ),
			)
		);
		WP_CLI::log( "Criada postagem de exemplo: {$pna_post[0]}" );
	}
	pna_local_aplicar_foto( $pna_post_id, __DIR__ . '/exemplos/postagens/' . $pna_slug . '.jpg', 'Foto da postagem: ' . $pna_post[0] . ' (exemplo)' );
}

// Remove a postagem padrão do WordPress ("Olá, mundo!"), se ainda existir.
$pna_ola = get_page_by_path( 'ola-mundo', OBJECT, 'post' );
$pna_ola = $pna_ola ? $pna_ola : get_page_by_path( 'hello-world', OBJECT, 'post' );
if ( $pna_ola ) {
	wp_delete_post( $pna_ola->ID, true );
	WP_CLI::log( 'Removida a postagem padrão do WordPress.' );
}

// Pets de exemplo (nomes do Figma). Campos: espécie, sexo, porte, idade, castrado, vacinado, sociável, brincalhão, carinhoso.
$pna_pets = array(
	'rhaenyra'  => array( 'Rhaenyra', 'gato', 'femea', 'pequeno', 'desconhecida', 'sem_info', 'sem_info', 3, 2, 3, 'Ela é uma gatinha muito amorosa e extremamente sociável. Após aparecer no campus, Rhaenyra passou a frequentar o bloco de medicina da universidade, sempre interagindo com os alunos.' ),
	'nina-oreo' => array( 'Nina Oreo', 'cachorro', 'femea', 'medio', 'adulto', 'sim', 'sim', 2, 3, 3, 'Pet de exemplo para o ambiente local.' ),
	'neymar'    => array( 'Neymar', 'cachorro', 'macho', 'grande', 'jovem-adulto', 'nao', 'sim', 3, 3, 2, 'Pet de exemplo para o ambiente local.' ),
	'mistica'   => array( 'Mística', 'gato', 'femea', 'pequeno', 'adulto', 'sim', 'sim', 1, 2, 3, 'Pet de exemplo para o ambiente local.' ),
	'oncinha'   => array( 'Oncinha', 'gato', 'femea', 'pequeno', 'filhote', 'nao', 'nao', 2, 3, 2, 'Pet de exemplo para o ambiente local.' ),
);

$pna_pet_ids = array();
foreach ( $pna_pets as $pna_slug => $pna_pet ) {
	$pna_existente = get_page_by_path( $pna_slug, OBJECT, 'pet' );
	if ( $pna_existente ) {
		$pna_pet_ids[ $pna_slug ] = $pna_existente->ID;
		WP_CLI::log( "Já existe o pet: {$pna_pet[0]}" );
		continue;
	}
	$pna_id = wp_insert_post(
		array(
			'post_type'    => 'pet',
			'post_status'  => 'publish',
			'post_name'    => $pna_slug,
			'post_title'   => $pna_pet[0],
			'post_content' => $pna_pet[10],
		)
	);
	wp_set_object_terms( $pna_id, $pna_pet[1], 'pna_especie' );
	wp_set_object_terms( $pna_id, $pna_pet[2], 'pna_sexo' );
	wp_set_object_terms( $pna_id, $pna_pet[3], 'pna_porte' );
	wp_set_object_terms( $pna_id, $pna_pet[4], 'pna_idade' );
	update_post_meta( $pna_id, '_pna_status', 'disponivel' );
	update_post_meta( $pna_id, '_pna_castrado', $pna_pet[5] );
	update_post_meta( $pna_id, '_pna_vacinado', $pna_pet[6] );
	update_post_meta( $pna_id, '_pna_sociavel', (string) $pna_pet[7] );
	update_post_meta( $pna_id, '_pna_brincalhao', (string) $pna_pet[8] );
	update_post_meta( $pna_id, '_pna_carinhoso', (string) $pna_pet[9] );
	$pna_pet_ids[ $pna_slug ] = $pna_id;
	WP_CLI::log( "Criado o pet de exemplo: {$pna_pet[0]}" );
}

// Fotos dos pets de exemplo (scripts/exemplos/pets/, créditos em CREDITOS.md).
foreach ( $pna_pet_ids as $pna_slug => $pna_id ) {
	pna_local_aplicar_foto( $pna_id, __DIR__ . '/exemplos/pets/' . $pna_slug . '.jpg', 'Foto de ' . get_the_title( $pna_id ) . ' (exemplo)' );
}

// Membro de exemplo (somente ambiente local).
$pna_membro = get_user_by( 'login', 'membro.teste' );
if ( ! $pna_membro ) {
	$pna_membro_id = wp_insert_user(
		array(
			'user_login'   => 'membro.teste',
			'user_email'   => 'membro.teste@exemplo.local',
			'user_pass'    => 'senha-local-123',
			'display_name' => 'Fulano de Tal',
			'role'         => 'pna_membro',
		)
	);
	WP_CLI::log( 'Criado o membro de exemplo: membro.teste (senha: senha-local-123)' );
} else {
	$pna_membro_id = $pna_membro->ID;
	WP_CLI::log( 'Já existe o membro de exemplo: membro.teste' );
}

// Solicitações de exemplo (só se o membro ainda não tiver nenhuma).
$pna_tem_solicitacoes = get_posts(
	array(
		'post_type'      => 'pna_solicitacao',
		'post_status'    => 'any',
		'author'         => $pna_membro_id,
		'posts_per_page' => 1,
		'fields'         => 'ids',
	)
);

if ( ! $pna_tem_solicitacoes && ! is_wp_error( $pna_membro_id ) ) {
	$pna_comum = array(
		'nome'     => 'Fulano de Tal',
		'email'    => 'membro.teste@exemplo.local',
		'telefone' => '(81) 99999-0000',
		'cep'      => '55000-000',
	);
	pna_core_criar_solicitacao(
		array(
			'tipo'    => 'adocao',
			'pet_id'  => $pna_pet_ids['rhaenyra'],
			'user_id' => $pna_membro_id,
			'dados'   => array_merge(
				$pna_comum,
				array(
					'nascimento' => '15/03/1995',
					'ocupacao'   => 'Estudante',
					'animais'    => '1 gato',
					'motivo'     => 'Conheci a Rhaenyra no campus e quero dar a ela um lar.',
				)
			),
		)
	);
	pna_core_criar_solicitacao(
		array(
			'tipo'    => 'apadrinhamento',
			'pet_id'  => $pna_pet_ids['rhaenyra'],
			'user_id' => $pna_membro_id,
			'dados'   => array_merge(
				$pna_comum,
				array(
					'valor_mensal'    => 'R$ 50',
					'forma_pagamento' => 'Pix',
				)
			),
		)
	);
	WP_CLI::log( 'Criadas 2 solicitações de exemplo (adoção e apadrinhamento da Rhaenyra).' );
} else {
	WP_CLI::log( 'Solicitações de exemplo já existem.' );
}

flush_rewrite_rules( false );

WP_CLI::success( 'Conteúdo inicial pronto.' );
