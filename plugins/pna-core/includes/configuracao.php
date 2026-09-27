<?php
/**
 * Configuração inicial do site (Ferramentas → PNA: configuração inicial).
 *
 * Deixa um WordPress recém-instalado pronto para o PNA, sem precisar de
 * terminal: cria as páginas, define página inicial e de postagens, abre
 * o cadastro, aplica os textos de Dúvidas e Doações e prepara um
 * rascunho da Política de Privacidade.
 *
 * Pode ser executada quantas vezes for preciso: nada é duplicado, e
 * textos já editados pela equipe nunca são sobrescritos.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Páginas do site (além das da área logada, em paginas.php).
 *
 * @return array slug => [título, padrão do tema para o conteúdo inicial]
 */
function pna_core_paginas_site() {
	return array(
		'inicio'              => array( __( 'Página inicial', 'pna' ), '' ),
		'postagens'           => array( __( 'Postagens', 'pna' ), '' ),
		'duvidas'             => array( __( 'Dúvidas', 'pna' ), 'pna/conteudo-duvidas' ),
		'doacoes'             => array( __( 'Doações', 'pna' ), 'pna/conteudo-doacoes' ),
		'minhas-notificacoes' => array( __( 'Minhas notificações', 'pna' ), '' ),
	);
}

/**
 * Executa a configuração inicial.
 *
 * @return string[] Registro do que foi feito, em linguagem simples.
 */
function pna_core_configuracao_inicial() {
	$registro = array();
	$padroes  = WP_Block_Patterns_Registry::get_instance();
	$ids      = array();

	foreach ( pna_core_paginas_site() as $slug => $dados ) {
		$pagina = get_page_by_path( $slug );
		if ( ! $pagina ) {
			$id = wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_name'   => $slug,
					'post_title'  => $dados[0],
				)
			);
			$pagina = get_post( $id );
			/* translators: %s: título da página. */
			$registro[] = sprintf( __( 'Página criada: %s', 'pna' ), $dados[0] );
		}
		$ids[ $slug ] = $pagina->ID;

		// Conteúdo inicial só em página vazia (nunca sobrescreve o que a equipe escreveu).
		if ( $dados[1] && '' === trim( $pagina->post_content ) && $padroes->is_registered( $dados[1] ) ) {
			wp_update_post(
				array(
					'ID'           => $pagina->ID,
					'post_content' => '<!-- wp:pattern {"slug":"' . $dados[1] . '"} /-->',
				)
			);
			/* translators: %s: título da página. */
			$registro[] = sprintf( __( 'Textos iniciais aplicados: %s', 'pna' ), $dados[0] );
		}
	}

	// A página "Minhas notificações" explica que o recurso virá depois.
	$notificacoes = get_post( $ids['minhas-notificacoes'] );
	if ( $notificacoes && '' === trim( $notificacoes->post_content ) ) {
		wp_update_post(
			array(
				'ID'           => $notificacoes->ID,
				'post_content' => '<!-- wp:paragraph --><p>' . esc_html__( 'Em breve você verá aqui as novidades dos seus pedidos. Enquanto isso, acompanhe tudo em Meu perfil, na seção "Minhas solicitações".', 'pna' ) . '</p><!-- /wp:paragraph -->',
			)
		);
	}

	pna_core_criar_paginas();
	$registro[] = __( 'Páginas da área logada conferidas: Entrar, Cadastro, Meu perfil, Adotar, Apadrinhar.', 'pna' );

	if ( 'page' !== get_option( 'show_on_front' ) || (int) get_option( 'page_on_front' ) !== $ids['inicio'] ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['inicio'] );
		update_option( 'page_for_posts', $ids['postagens'] );
		$registro[] = __( 'Página inicial e página de postagens definidas.', 'pna' );
	}

	if ( ! get_option( 'users_can_register' ) || 'pna_membro' !== get_option( 'default_role' ) ) {
		update_option( 'users_can_register', 1 );
		update_option( 'default_role', 'pna_membro' );
		$registro[] = __( 'Cadastro aberto ao público, com novos usuários como Membro.', 'pna' );
	}

	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$registro[] = __( 'Links amigáveis ativados (/nome-da-pagina/).', 'pna' );
	}

	if ( 'America/Recife' !== get_option( 'timezone_string' ) ) {
		update_option( 'timezone_string', 'America/Recife' );
		$registro[] = __( 'Fuso horário definido: Recife.', 'pna' );
	}

	$registro = array_merge( $registro, pna_core_preparar_politica_privacidade() );

	pna_core_criar_caracteristicas_padrao();
	flush_rewrite_rules( false );

	return $registro;
}

/**
 * Prepara a Política de Privacidade como RASCUNHO, a partir do modelo do
 * tema. Nunca publica sozinha: o texto precisa ser revisado e completado.
 *
 * @return string[] Registro.
 */
function pna_core_preparar_politica_privacidade() {
	$id     = (int) get_option( 'wp_page_for_privacy_policy' );
	$pagina = $id ? get_post( $id ) : null;

	if ( $pagina && 'publish' === $pagina->post_status ) {
		return array();
	}
	if ( ! WP_Block_Patterns_Registry::get_instance()->is_registered( 'pna/politica-privacidade' ) ) {
		return array();
	}

	$conteudo = '<!-- wp:pattern {"slug":"pna/politica-privacidade"} /-->';

	if ( $pagina ) {
		// Substitui o modelo genérico do WordPress apenas se ninguém editou o rascunho.
		if ( false === strpos( $pagina->post_content, 'pna/politica-privacidade' ) && $pagina->post_modified === $pagina->post_date ) {
			wp_update_post(
				array(
					'ID'           => $pagina->ID,
					'post_content' => $conteudo,
				)
			);
			return array( __( 'Rascunho da Política de Privacidade do PNA aplicado. Revise, complete os trechos entre [colchetes] e publique.', 'pna' ) );
		}
		return array();
	}

	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_name'    => 'politica-de-privacidade',
			'post_title'   => __( 'Política de Privacidade', 'pna' ),
			'post_content' => $conteudo,
		)
	);
	update_option( 'wp_page_for_privacy_policy', $id );
	return array( __( 'Rascunho da Política de Privacidade criado. Revise, complete os trechos entre [colchetes] e publique.', 'pna' ) );
}

/**
 * Situação atual de cada item da configuração (para a tela).
 *
 * @return array rótulo => bool
 */
function pna_core_situacao_configuracao() {
	$privacidade = (int) get_option( 'wp_page_for_privacy_policy' );
	$situacao    = array(
		__( 'Página inicial definida', 'pna' )          => 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ),
		__( 'Página de postagens definida', 'pna' )     => (bool) get_option( 'page_for_posts' ),
		__( 'Cadastro aberto (novos usuários como Membro)', 'pna' ) => get_option( 'users_can_register' ) && 'pna_membro' === get_option( 'default_role' ),
		__( 'Links amigáveis ativados', 'pna' )         => '' !== (string) get_option( 'permalink_structure' ),
		__( 'Política de Privacidade publicada', 'pna' ) => $privacidade && 'publish' === get_post_status( $privacidade ),
		__( 'Idioma do site em português', 'pna' )     => 'pt_BR' === get_locale(),
		__( 'Mecanismos de busca bloqueados (use só no ambiente de testes)', 'pna' ) => '0' === (string) get_option( 'blog_public' ),
	);
	foreach ( array_merge( pna_core_paginas_site(), pna_core_paginas() ) as $slug => $dados ) {
		/* translators: %s: título da página. */
		$situacao[ sprintf( __( 'Página "%s" existe', 'pna' ), $dados[0] ) ] = (bool) get_page_by_path( $slug );
	}
	return $situacao;
}

/**
 * Tela em Ferramentas → PNA: configuração inicial.
 */
function pna_core_menu_configuracao() {
	add_management_page(
		__( 'PNA: configuração inicial', 'pna' ),
		__( 'PNA: configuração inicial', 'pna' ),
		'manage_options',
		'pna-configuracao',
		'pna_core_render_configuracao'
	);
}
add_action( 'admin_menu', 'pna_core_menu_configuracao' );

/**
 * Conteúdo da tela.
 */
function pna_core_render_configuracao() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$registro = array();
	if ( isset( $_POST['pna_configurar'] ) && check_admin_referer( 'pna_configuracao' ) ) {
		$registro = pna_core_configuracao_inicial();
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'PNA: configuração inicial', 'pna' ); ?></h1>
		<p><?php esc_html_e( 'Prepara o site para o PNA: páginas, página inicial, cadastro aberto, textos de Dúvidas e Doações e um rascunho da Política de Privacidade. Pode ser executada várias vezes: nada é duplicado e textos já editados não são alterados.', 'pna' ); ?></p>

		<?php if ( $registro ) : ?>
			<div class="notice notice-success"><p><strong><?php esc_html_e( 'Configuração executada:', 'pna' ); ?></strong></p>
				<ul style="list-style:disc;padding-left:20px">
					<?php foreach ( $registro as $linha ) : ?>
						<li><?php echo esc_html( $linha ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<h2><?php esc_html_e( 'Situação atual', 'pna' ); ?></h2>
		<table class="widefat striped" style="max-width:720px">
			<tbody>
				<?php foreach ( pna_core_situacao_configuracao() as $rotulo => $ok ) : ?>
					<tr>
						<td><?php echo esc_html( $rotulo ); ?></td>
						<td style="width:120px"><?php echo $ok ? '✅ ' . esc_html__( 'Sim', 'pna' ) : '⚠️ ' . esc_html__( 'Não', 'pna' ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p class="description"><?php esc_html_e( 'Idioma: Configurações → Geral → Idioma do site. Mecanismos de busca: Configurações → Leitura. A Política de Privacidade é publicada em Configurações → Privacidade, depois de revisada.', 'pna' ); ?></p>

		<form method="post">
			<?php wp_nonce_field( 'pna_configuracao' ); ?>
			<p><button type="submit" name="pna_configurar" value="1" class="button button-primary"><?php esc_html_e( 'Executar configuração inicial', 'pna' ); ?></button></p>
		</form>
	</div>
	<?php
}
