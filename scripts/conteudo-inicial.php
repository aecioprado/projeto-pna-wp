<?php
/**
 * Cria o conteúdo mínimo para o ambiente local funcionar:
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

$pna_paginas = array(
	'inicio'              => 'Página inicial',
	'postagens'           => 'Postagens',
	'duvidas'             => 'Dúvidas',
	'doacoes'             => 'Doações',
	'meu-perfil'          => 'Meu perfil',
	'minhas-notificacoes' => 'Minhas notificações',
);

$pna_ids = array();

foreach ( $pna_paginas as $pna_slug => $pna_titulo ) {
	$pna_existente = get_page_by_path( $pna_slug );

	if ( $pna_existente ) {
		$pna_ids[ $pna_slug ] = $pna_existente->ID;
		WP_CLI::log( "Já existe: {$pna_titulo}" );
		continue;
	}

	$pna_id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_name'   => $pna_slug,
			'post_title'  => $pna_titulo,
		),
		true
	);

	if ( is_wp_error( $pna_id ) ) {
		WP_CLI::warning( "Não foi possível criar {$pna_titulo}: " . $pna_id->get_error_message() );
		continue;
	}

	$pna_ids[ $pna_slug ] = $pna_id;
	WP_CLI::log( "Criada: {$pna_titulo}" );
}

if ( isset( $pna_ids['inicio'], $pna_ids['postagens'] ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $pna_ids['inicio'] );
	update_option( 'page_for_posts', $pna_ids['postagens'] );
}

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

// Postagens de exemplo (textos do Figma), para ver os cards de postagem.
$pna_postagens = array(
	'quais-alimentos-dar-aos-pets'     => array( 'Quais alimentos dar aos pets?', 'Assim como ocorre com as pessoas, a nutrição é essencial para manter a saúde física e mental de cães e gatos. A alimentação correta impacta positivamente a qualidade de vida.' ),
	'cuidados-com-filhotes-de-cachorro' => array( 'Cuidados com filhotes de cachorro', 'Este post apresenta algumas formas de adaptar o pequeno à sua nova casa, com dicas de cuidado para o tutor quanto à alimentação, higiene e gasto de energia.' ),
	'gravidez-felina'                   => array( 'Gravidez felina', 'A gestação de gato é um assunto que merece muita atenção! Nesse período, a futura mamãe fica mais vulnerável e, por isso, precisamos tomar alguns cuidados.' ),
	'vacinas-obrigatorias'              => array( 'Vacinas obrigatórias para cães e gatos', 'A vacinação em pets protege contra diversas doenças graves e contagiosas, prevenindo riscos que podem comprometer a vida dos animais e até mesmo afetar pessoas.' ),
);

foreach ( $pna_postagens as $pna_slug => $pna_post ) {
	if ( get_page_by_path( $pna_slug, OBJECT, 'post' ) ) {
		WP_CLI::log( "Já existe: {$pna_post[0]}" );
		continue;
	}
	wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_name'    => $pna_slug,
			'post_title'   => $pna_post[0],
			'post_excerpt' => $pna_post[1],
			'post_content' => '<!-- wp:paragraph --><p>' . esc_html( $pna_post[1] ) . '</p><!-- /wp:paragraph -->',
		)
	);
	WP_CLI::log( "Criada postagem de exemplo: {$pna_post[0]}" );
}

// Cadastro aberto ao público. Novos usuários entram como Membro (plugin pna-core).
update_option( 'users_can_register', 1 );
update_option( 'default_role', get_role( 'pna_membro' ) ? 'pna_membro' : 'subscriber' );

if ( ! function_exists( 'pna_core_criar_solicitacao' ) ) {
	WP_CLI::warning( 'Plugin pna-core inativo: pets e solicitações de exemplo não foram criados.' );
	WP_CLI::success( 'Conteúdo inicial pronto.' );
	return;
}

pna_core_criar_caracteristicas_padrao();

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
