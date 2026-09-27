<?php
/**
 * Página "Meu perfil" (Figma: foto, nome, pronome, e-mail, apadrinhamentos),
 * com "Minhas solicitações", "Meus dados" e "Alterar senha".
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
	printf( '<p %s>%s</p>', get_block_wrapper_attributes(), esc_html__( 'Entre para ver seu perfil.', 'pna' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$pna_usuario = wp_get_current_user();
$pna_uid     = $pna_usuario->ID;
$pna_meta    = static function ( $chave ) use ( $pna_uid ) {
	return (string) get_user_meta( $pna_uid, $chave, true );
};

$pna_solicitacoes = get_posts(
	array(
		'post_type'      => 'pna_solicitacao',
		'post_status'    => 'any',
		'author'         => $pna_uid,
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$pna_apadrinhamentos = 0;
foreach ( $pna_solicitacoes as $pna_s ) {
	$pna_d = pna_core_obter_solicitacao( $pna_s->ID );
	if ( 'apadrinhamento' === $pna_d['tipo'] && 'aprovada' === $pna_d['status'] ) {
		++$pna_apadrinhamentos;
	}
}

$pna_explicacoes = array(
	'enviada'    => __( 'Recebemos seu pedido. Em breve nossa equipe vai analisá-lo.', 'pna' ),
	'em_analise' => __( 'Nossa equipe está analisando seu pedido.', 'pna' ),
	'aprovada'   => __( 'Aprovado! Vamos entrar em contato pelo seu e-mail com os próximos passos.', 'pna' ),
	'recusada'   => __( 'Seu pedido não foi aprovado desta vez.', 'pna' ),
	'concluida'  => __( 'Adoção concluída. Obrigado por dar um lar!', 'pna' ),
	'encerrada'  => __( 'Apadrinhamento encerrado.', 'pna' ),
	'cancelada'  => __( 'Pedido cancelado.', 'pna' ),
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'pna-perfil' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php
	pna_core_aviso_da_url(
		array(
			'bem-vindo'              => __( 'Cadastro feito! Boas-vindas ao PNA.', 'pna' ),
			'dados'                  => __( 'Seus dados foram atualizados.', 'pna' ),
			'senha'                  => __( 'Sua senha foi alterada.', 'pna' ),
			'cancelada'              => __( 'Pedido cancelado.', 'pna' ),
			'enviada-adocao'         => __( 'Pedido de adoção enviado! Acompanhe o andamento abaixo.', 'pna' ),
			'enviada-apadrinhamento' => __( 'Pedido de apadrinhamento enviado! Acompanhe o andamento abaixo.', 'pna' ),
		)
	);
	?>

	<section class="pna-perfil__cabecalho">
		<span class="pna-perfil__foto" aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( $pna_usuario->display_name, 0, 1 ) ) ); ?></span>
		<div class="pna-perfil__identidade">
			<h1 class="pna-perfil__nome"><?php echo esc_html( $pna_usuario->display_name ); ?></h1>
			<?php if ( $pna_meta( 'pna_pronome' ) ) : ?>
				<p class="pna-perfil__pronome"><?php echo esc_html( $pna_meta( 'pna_pronome' ) ); ?></p>
			<?php endif; ?>
			<p class="pna-perfil__email"><?php echo esc_html( $pna_usuario->user_email ); ?></p>
		</div>
		<div class="pna-perfil__contador">
			<p class="pna-perfil__contador-rotulo"><?php esc_html_e( 'Apadrinhamentos:', 'pna' ); ?></p>
			<p class="is-style-pna-numero"><?php echo esc_html( str_pad( (string) $pna_apadrinhamentos, 2, '0', STR_PAD_LEFT ) ); ?></p>
		</div>
	</section>

	<section class="pna-perfil__secao" id="minhas-solicitacoes">
		<h2 class="pna-form__secao-titulo"><?php esc_html_e( 'Minhas solicitações', 'pna' ); ?></h2>
		<?php if ( ! $pna_solicitacoes ) : ?>
			<p><?php esc_html_e( 'Você ainda não fez nenhum pedido.', 'pna' ); ?> <a href="<?php echo esc_url( get_post_type_archive_link( 'pet' ) ); ?>"><?php esc_html_e( 'Conheça os pets para adoção.', 'pna' ); ?></a></p>
		<?php else : ?>
			<ul class="pna-solicitacoes">
				<?php foreach ( $pna_solicitacoes as $pna_s ) : ?>
					<?php
					$pna_d      = pna_core_obter_solicitacao( $pna_s->ID );
					$pna_status = pna_core_status_solicitacao( $pna_d['tipo'] );
					?>
					<li class="pna-solicitacao pna-solicitacao--<?php echo esc_attr( $pna_d['status'] ); ?>">
						<div class="pna-solicitacao__info">
							<p class="pna-solicitacao__titulo">
								<?php echo esc_html( pna_core_tipos_solicitacao()[ $pna_d['tipo'] ] ?? '' ); ?> ·
								<a href="<?php echo esc_url( get_permalink( $pna_d['pet_id'] ) ); ?>"><?php echo esc_html( get_the_title( $pna_d['pet_id'] ) ); ?></a>
							</p>
							<p class="pna-solicitacao__texto"><?php echo esc_html( $pna_explicacoes[ $pna_d['status'] ] ?? '' ); ?></p>
							<p class="pna-solicitacao__data"><?php echo esc_html( sprintf( /* translators: %s: data. */ __( 'Enviado em %s', 'pna' ), get_the_date( 'd/m/Y', $pna_s ) ) ); ?></p>
						</div>
						<span class="pna-solicitacao__status"><?php echo esc_html( $pna_status[ $pna_d['status'] ] ?? $pna_d['status'] ); ?></span>
						<?php if ( pna_core_membro_pode_cancelar( $pna_s->ID ) ) : ?>
							<form method="post" action="" class="pna-solicitacao__cancelar" onsubmit="return confirm('<?php echo esc_js( __( 'Cancelar este pedido?', 'pna' ) ); ?>');">
								<?php wp_nonce_field( 'pna_cancelar', 'pna_nonce' ); ?>
								<input type="hidden" name="pna_acao" value="cancelar">
								<input type="hidden" name="solicitacao" value="<?php echo esc_attr( (string) $pna_s->ID ); ?>">
								<button type="submit" class="pna-link-botao"><?php esc_html_e( 'Cancelar pedido', 'pna' ); ?></button>
							</form>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</section>

	<section class="pna-perfil__secao" id="meus-dados">
		<h2 class="pna-form__secao-titulo"><?php esc_html_e( 'Meus dados', 'pna' ); ?></h2>
		<p><?php esc_html_e( 'Usamos estes dados para preencher seus pedidos de adoção e apadrinhamento.', 'pna' ); ?></p>
		<?php pna_core_resumo_erros( 'perfil' ); ?>
		<form class="pna-form" method="post" action="#meus-dados">
			<?php wp_nonce_field( 'pna_perfil', 'pna_nonce' ); ?>
			<input type="hidden" name="pna_acao" value="perfil">
			<div class="pna-form__grade">
				<?php
				pna_core_campo( 'perfil', 'nome', __( 'Nome completo', 'pna' ), array( 'valor' => $pna_usuario->display_name, 'autocomplete' => 'name' ) );
				pna_core_campo( 'perfil', 'pna_pronome', __( 'Pronome (opcional)', 'pna' ), array( 'valor' => $pna_meta( 'pna_pronome' ), 'placeholder' => __( 'Ex.: ela/dela', 'pna' ), 'obrigatorio' => false ) );
				pna_core_campo( 'perfil', 'pna_telefone', __( 'Telefone / WhatsApp', 'pna' ), array( 'tipo' => 'tel', 'valor' => $pna_meta( 'pna_telefone' ), 'placeholder' => '(81) 00000-0000', 'autocomplete' => 'tel', 'obrigatorio' => false ) );
				pna_core_campo( 'perfil', 'pna_cep', __( 'CEP', 'pna' ), array( 'valor' => $pna_meta( 'pna_cep' ), 'autocomplete' => 'postal-code', 'obrigatorio' => false ) );
				pna_core_campo( 'perfil', 'pna_nascimento', __( 'Data de nascimento', 'pna' ), array( 'tipo' => 'date', 'valor' => $pna_meta( 'pna_nascimento' ), 'autocomplete' => 'bday', 'obrigatorio' => false ) );
				pna_core_campo( 'perfil', 'pna_ocupacao', __( 'Ocupação', 'pna' ), array( 'valor' => $pna_meta( 'pna_ocupacao' ), 'obrigatorio' => false ) );
				?>
			</div>
			<p class="pna-campo__ajuda"><?php esc_html_e( 'O e-mail da conta não pode ser alterado aqui. Para mudar, fale com a coordenação do PNA.', 'pna' ); ?></p>
			<div class="pna-form__acoes"><button type="submit" class="wp-element-button"><?php esc_html_e( 'Salvar dados', 'pna' ); ?></button></div>
		</form>
	</section>

	<section class="pna-perfil__secao" id="senha">
		<h2 class="pna-form__secao-titulo"><?php esc_html_e( 'Alterar senha', 'pna' ); ?></h2>
		<?php pna_core_resumo_erros( 'senha' ); ?>
		<form class="pna-form" method="post" action="#senha">
			<?php wp_nonce_field( 'pna_senha', 'pna_nonce' ); ?>
			<input type="hidden" name="pna_acao" value="senha">
			<div class="pna-form__grade">
				<?php
				pna_core_campo( 'senha', 'senha_atual', __( 'Senha atual', 'pna' ), array( 'tipo' => 'password', 'autocomplete' => 'current-password' ) );
				echo '<div aria-hidden="true"></div>';
				pna_core_campo( 'senha', 'senha_nova', __( 'Nova senha', 'pna' ), array( 'tipo' => 'password', 'autocomplete' => 'new-password', 'ajuda' => __( 'Pelo menos 8 caracteres.', 'pna' ) ) );
				pna_core_campo( 'senha', 'senha_confirma', __( 'Confirmar nova senha', 'pna' ), array( 'tipo' => 'password', 'autocomplete' => 'new-password' ) );
				?>
			</div>
			<div class="pna-form__acoes"><button type="submit" class="wp-element-button"><?php esc_html_e( 'Alterar senha', 'pna' ); ?></button></div>
		</form>
	</section>

	<section class="pna-perfil__secao">
		<h2 class="pna-form__secao-titulo"><?php esc_html_e( 'Privacidade', 'pna' ); ?></h2>
		<p><?php esc_html_e( 'Você pode pedir uma cópia dos seus dados ou a exclusão deles a qualquer momento, falando com a coordenação do PNA.', 'pna' ); ?></p>
		<p><a class="pna-perfil__sair" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Sair da conta', 'pna' ); ?></a></p>
	</section>
</div>
