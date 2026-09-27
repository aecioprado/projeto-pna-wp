<?php
/**
 * Title: Guia de estilo
 * Slug: pna/guia-de-estilo
 * Categories: pna
 * Inserter: no
 * Description: Página de referência com todos os tokens e componentes do PNA. Usada na página privada "Guia de estilo".
 *
 * @package PNA
 */

$pna_cores = array(
	'primary'        => array( '#35B08A', 'Bordas, ícones, decoração' ),
	'primary-strong' => array( '#2E9C7A', 'Fundos com texto claro' ),
	'primary-text'   => array( '#1F7A5E', 'Títulos, links' ),
	'primary-light'  => array( '#8ED1B5', 'Placeholders' ),
	'primary-pale'   => array( '#C5E4CF', 'Fundos suaves' ),
	'primary-tint'   => array( '#E1F3E2', 'Fundo de campos' ),
	'background'     => array( '#FFFFF1', 'Fundo da página' ),
	'surface'        => array( '#FCF9EA', 'Superfícies' ),
	'body-text'      => array( '#666666', 'Texto corrido' ),
	'danger'         => array( '#B3261E', 'Erros' ),
	'white'          => array( '#FFFFFF', 'Texto sobre verde' ),
);

$pna_tamanhos = array(
	'display'  => '96px',
	'huge'     => '60px',
	'xx-large' => '40px',
	'x-large'  => '32px',
	'large'    => '24px',
	'medium'   => '20px',
	'small'    => '16px',
	'x-small'  => '13px',
);
?>
<!-- wp:paragraph -->
<p>Esta página mostra todos os tokens e componentes do design system do PNA. Ela é privada: só administradores a veem. Use-a para conferir o visual depois de qualquer mudança no tema. Referência completa em <code>docs/design-tokens.md</code> e <code>docs/componentes.md</code>.</p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao">Cores</h2>
	<!-- /wp:heading -->

	<!-- wp:group {"className":"pna-guia-cores","layout":{"type":"default"}} -->
	<div class="wp-block-group pna-guia-cores">
		<?php foreach ( $pna_cores as $pna_slug => $pna_cor ) : ?>
		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:group {"className":"pna-guia-cor","backgroundColor":"<?php echo esc_attr( $pna_slug ); ?>","layout":{"type":"default"}} -->
			<div class="wp-block-group pna-guia-cor has-<?php echo esc_attr( $pna_slug ); ?>-background-color has-background"></div>
			<!-- /wp:group -->
			<!-- wp:paragraph {"className":"pna-guia-cor-rotulo"} -->
			<p class="pna-guia-cor-rotulo"><?php echo esc_html( $pna_slug ); ?><br><?php echo esc_html( $pna_cor[0] ); ?><br><?php echo esc_html( $pna_cor[1] ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao">Tipografia</h2>
	<!-- /wp:heading -->

	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading">Título de página (h1)</h1>
	<!-- /wp:heading -->
	<!-- wp:heading -->
	<h2 class="wp-block-heading">Título de card (h2)</h2>
	<!-- /wp:heading -->
	<!-- wp:heading {"level":3} -->
	<h3 class="wp-block-heading">Título de seção de formulário (h3)</h3>
	<!-- /wp:heading -->
	<!-- wp:heading {"level":4} -->
	<h4 class="wp-block-heading">Subtítulo (h4)</h4>
	<!-- /wp:heading -->

	<?php foreach ( $pna_tamanhos as $pna_slug => $pna_px ) : ?>
	<!-- wp:paragraph {"fontSize":"<?php echo esc_attr( $pna_slug ); ?>"} -->
	<p class="has-<?php echo esc_attr( $pna_slug ); ?>-font-size"><?php echo esc_html( $pna_slug . ' · ' . $pna_px ); ?> · Pets no Agreste</p>
	<!-- /wp:paragraph -->
	<?php endforeach; ?>

	<!-- wp:paragraph -->
	<p>Texto corrido com <a href="#">um link no meio da frase</a>, para conferir cor, sublinhado e foco pelo teclado (tecla Tab).</p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph {"className":"is-style-pna-link-seta"} -->
	<p class="is-style-pna-link-seta"><a href="#">Link com seta (estilo "Link com seta")</a></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao">Botões</h2>
	<!-- /wp:heading -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#">Adotar</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#">Apadrinhar</a></div>
		<!-- /wp:button -->
		<!-- wp:button {"className":"is-style-pna-atalho"} -->
		<div class="wp-block-button is-style-pna-atalho"><a class="wp-block-button__link wp-element-button" href="#">Atalho (dúvidas)</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao">Caixa de destaque</h2>
	<!-- /wp:heading -->

	<!-- wp:group {"style":{"dimensions":{"minHeight":""}},"layout":{"type":"constrained","contentSize":"392px","justifyContent":"left"}} -->
	<div class="wp-block-group">
		<!-- wp:pattern {"slug":"pna/contador-adocoes"} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:pattern {"slug":"pna/grade-postagens"} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao">Card de pet (exemplo)</h2>
	<!-- /wp:heading -->

	<!-- wp:html -->
	<div class="pna-guia-pets">
		<article class="pna-card-pet">
			<a class="pna-card-pet__link" href="#">
				<span class="pna-card-pet__foto" role="img" aria-label="Sem foto"></span>
				<span class="pna-card-pet__rodape">
					<span class="pna-card-pet__nome">Nina Oreo</span>
					<span class="pna-card-pet__sexo"><?php echo pna_icone( 'sexo-femea' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="pna-so-leitor">Fêmea</span></span>
				</span>
			</a>
		</article>
		<article class="pna-card-pet">
			<a class="pna-card-pet__link" href="#">
				<span class="pna-card-pet__foto" role="img" aria-label="Sem foto"></span>
				<span class="pna-card-pet__rodape">
					<span class="pna-card-pet__nome">Neymar</span>
					<span class="pna-card-pet__sexo"><?php echo pna_icone( 'sexo-macho' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="pna-so-leitor">Macho</span></span>
				</span>
			</a>
		</article>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:pattern {"slug":"pna/cards-doacao"} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:pattern {"slug":"pna/atalhos-duvidas"} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao">Notificações (exemplo)</h2>
	<!-- /wp:heading -->

	<!-- wp:html -->
	<ul class="pna-notificacoes">
		<li class="pna-notificacao-item">
			<article class="pna-notificacao pna-notificacao--nao-lida">
				<span class="pna-notificacao__icone"><?php echo pna_icone( 'marcado' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div class="pna-notificacao__corpo">
					<h3 class="pna-notificacao__titulo">Adoção aprovada!</h3>
					<p class="pna-notificacao__texto">Seu formulário foi aprovado! Enviaremos uma mensagem para seu e-mail com as instruções para te juntar com seu novo amigo.</p>
				</div>
				<time class="pna-notificacao__tempo" datetime="2026-09-26T09:00">3 horas atrás</time>
			</article>
			<button type="button" class="pna-notificacao__excluir" aria-label="Excluir notificação: Adoção aprovada!"><?php echo pna_icone( 'lixeira' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</li>
		<li class="pna-notificacao-item">
			<article class="pna-notificacao">
				<span class="pna-notificacao__icone"><?php echo pna_icone( 'sino' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<div class="pna-notificacao__corpo">
					<h3 class="pna-notificacao__titulo">Formulário em análise</h3>
					<p class="pna-notificacao__texto">Aguarde um pouco. Nossa equipe está analisando seu pedido de adoção e vai responder o mais rápido possível.</p>
				</div>
				<time class="pna-notificacao__tempo" datetime="2026-09-25T12:00">1 dia atrás</time>
			</article>
			<button type="button" class="pna-notificacao__excluir" aria-label="Excluir notificação: Formulário em análise"><?php echo pna_icone( 'lixeira' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</li>
	</ul>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao">Formulário (exemplo)</h2>
	<!-- /wp:heading -->

	<!-- wp:html -->
	<form class="pna-form" action="#" onsubmit="return false;">
		<h3 class="pna-form__secao-titulo">Seus dados</h3>
		<div class="pna-form__grade">
			<div class="pna-campo">
				<label class="pna-campo__rotulo" for="guia-nome">Nome completo</label>
				<input class="pna-campo__entrada" id="guia-nome" type="text" value="Fulano de Tal">
			</div>
			<div class="pna-campo">
				<label class="pna-campo__rotulo" for="guia-telefone">Telefone / WhatsApp</label>
				<input class="pna-campo__entrada" id="guia-telefone" type="tel" placeholder="(81) 00000-0000">
			</div>
			<div class="pna-campo pna-campo--erro">
				<label class="pna-campo__rotulo" for="guia-nascimento">Data de nascimento</label>
				<input class="pna-campo__entrada" id="guia-nascimento" type="text" value="12/05/2012" aria-invalid="true" aria-describedby="guia-nascimento-erro">
				<p class="pna-campo__erro" id="guia-nascimento-erro">A adoção é permitida apenas para maiores de 18 anos.</p>
			</div>
			<div class="pna-campo">
				<label class="pna-campo__rotulo" for="guia-pet">Pet escolhido</label>
				<input class="pna-campo__entrada" id="guia-pet" type="text" value="Rhaenyra" readonly aria-describedby="guia-pet-ajuda">
				<p class="pna-campo__ajuda" id="guia-pet-ajuda">Vem da página do pet e não pode ser alterado aqui.</p>
			</div>
			<div class="pna-campo pna-campo--largo">
				<label class="pna-campo__rotulo" for="guia-motivo">Por que você decidiu adotar?</label>
				<textarea class="pna-campo__entrada" id="guia-motivo" maxlength="1000" placeholder="Conte um pouco sobre você e sua rotina."></textarea>
				<p class="pna-campo__contador">0 / 1000 caracteres</p>
			</div>
		</div>

		<h3 class="pna-form__secao-titulo">Valor mensal</h3>
		<fieldset class="pna-escolhas">
			<legend class="pna-so-leitor">Valor mensal</legend>
			<label class="pna-escolha"><input type="radio" name="guia-valor" value="25"><span>R$ 25</span></label>
			<label class="pna-escolha"><input type="radio" name="guia-valor" value="50" checked><span>R$ 50</span></label>
			<label class="pna-escolha"><input type="radio" name="guia-valor" value="100"><span>R$ 100</span></label>
			<label class="pna-escolha"><input type="radio" name="guia-valor" value="outro"><span>Outro valor</span></label>
		</fieldset>

		<h3 class="pna-form__secao-titulo">Forma de pagamento</h3>
		<label class="pna-opcao"><input class="pna-opcao__marcador" type="radio" name="guia-pagamento" checked> Pix</label>
		<label class="pna-opcao"><input class="pna-opcao__marcador" type="radio" name="guia-pagamento"> Cartão de crédito ou débito</label>

		<label class="pna-opcao"><input class="pna-opcao__marcador" type="checkbox"> <span>Estou ciente e me comprometo com a responsabilidade de cuidar da saúde, do bem-estar e da segurança do pet.</span></label>
		<label class="pna-opcao"><input class="pna-opcao__marcador" type="checkbox" checked> <span>Li e concordo com a <a href="#">Política de Privacidade</a>.</span></label>

		<div class="pna-form__acoes">
			<button type="submit" class="wp-element-button">Enviar</button>
			<button type="button" class="wp-element-button" disabled>Enviar (desabilitado)</button>
		</div>
	</form>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->

<!-- wp:group {"className":"pna-guia-secao","layout":{"type":"default"}} -->
<div class="wp-block-group pna-guia-secao">
	<!-- wp:heading {"className":"is-style-pna-titulo-secao"} -->
	<h2 class="wp-block-heading is-style-pna-titulo-secao">Paginação (exemplo)</h2>
	<!-- /wp:heading -->

	<!-- wp:html -->
	<nav class="wp-block-query-pagination is-layout-flex" aria-label="Paginação de exemplo">
		<a class="wp-block-query-pagination-previous" href="#">Anterior</a>
		<div class="wp-block-query-pagination-numbers"><a class="page-numbers" href="#">1</a><span aria-current="page" class="page-numbers current">2</span><a class="page-numbers" href="#">3</a></div>
		<a class="wp-block-query-pagination-next" href="#">Próxima</a>
	</nav>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
