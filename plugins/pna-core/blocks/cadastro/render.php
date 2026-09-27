<?php
/**
 * Página "Cadastro" (Figma: CADASTRO). Sem CPF: ver docs/decisoes/0006.
 *
 * @package PNA_Core
 */

defined( 'ABSPATH' ) || exit;

$pna_privacidade = get_privacy_policy_url();
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'pna-conta pna-conta--cadastro' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<h1 class="pna-conta__titulo"><?php esc_html_e( 'Cadastro', 'pna' ); ?></h1>

	<?php if ( ! get_option( 'users_can_register' ) ) : ?>
		<div class="pna-aviso pna-aviso--erro"><p><?php esc_html_e( 'O cadastro está fechado no momento.', 'pna' ); ?></p></div>
	<?php else : ?>

	<?php pna_core_resumo_erros( 'cadastro' ); ?>

	<form class="pna-form pna-conta__form" method="post" action="">
		<?php wp_nonce_field( 'pna_cadastro', 'pna_nonce' ); ?>
		<input type="hidden" name="pna_acao" value="cadastro">
		<?php pna_core_campo_armadilha(); ?>

		<div class="pna-form__grade">
			<?php
			pna_core_campo( 'cadastro', 'email', __( 'E-mail', 'pna' ), array( 'tipo' => 'email', 'autocomplete' => 'email', 'ajuda' => __( 'Você vai usar este e-mail para entrar.', 'pna' ) ) );
			pna_core_campo( 'cadastro', 'nome', __( 'Nome completo', 'pna' ), array( 'autocomplete' => 'name' ) );
			pna_core_campo( 'cadastro', 'cep', __( 'CEP', 'pna' ), array( 'placeholder' => '55000-000', 'autocomplete' => 'postal-code', 'ajuda' => __( 'Atendemos Caruaru e região.', 'pna' ) ) );
			echo '<div aria-hidden="true"></div>';
			pna_core_campo( 'cadastro', 'senha', __( 'Senha', 'pna' ), array( 'tipo' => 'password', 'autocomplete' => 'new-password', 'ajuda' => __( 'Pelo menos 8 caracteres.', 'pna' ) ) );
			pna_core_campo( 'cadastro', 'confirmar_senha', __( 'Confirmar senha', 'pna' ), array( 'tipo' => 'password', 'autocomplete' => 'new-password' ) );
			?>
		</div>

		<?php
		pna_core_aceite( 'cadastro', 'termo', __( 'Concordo com o <strong>Termo de Compromisso</strong>: cuidar da saúde, do bem-estar e da segurança dos animais, sem qualquer tipo de maus-tratos.', 'pna' ) );
		pna_core_aceite(
			'cadastro',
			'privacidade',
			$pna_privacidade
				/* translators: %s: link da política de privacidade. */
				? sprintf( __( 'Li e concordo com a <a href="%s" target="_blank">Política de Privacidade</a>.', 'pna' ), esc_url( $pna_privacidade ) )
				: __( 'Li e concordo com a Política de Privacidade.', 'pna' )
		);
		?>

		<div class="pna-form__acoes pna-conta__acoes">
			<button type="submit" class="wp-element-button"><?php esc_html_e( 'Cadastrar', 'pna' ); ?></button>
		</div>

		<p class="pna-conta__links"><a href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Já tenho cadastro', 'pna' ); ?></a></p>
	</form>
	<?php endif; ?>
</div>
