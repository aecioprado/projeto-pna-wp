# Componentes do PNA

Catálogo dos componentes do tema. Todos aparecem na página privada **Guia de estilo** (http://localhost:8888/guia-de-estilo/ no ambiente local), gerada pelo padrão `patterns/guia-de-estilo.php`.

Há três tipos de componente:

1. **Estilos de bloco:** variações de blocos nativos, escolhidas no editor (painel do bloco → Estilos).
2. **Padrões:** conjuntos de blocos prontos, inseridos pelo editor (botão + → Padrões → categoria **PNA**).
3. **Componentes com estrutura HTML fixa:** gerados por código (tema ou plugin `pna-core`), que precisam seguir exatamente a estrutura descrita aqui para receber o visual.

O CSS de cada componente está em `themes/pna-theme/assets/css/componentes/`, um arquivo por componente.

## 1. Estilos de bloco

| Bloco | Estilo | Classe | Uso no Figma | CSS |
|---|---|---|---|---|
| Botão | Padrão | — | "Adotar", "Enviar" | `theme.json` |
| Botão | Contorno | `is-style-outline` | "Apadrinhar" | `theme.json` |
| Botão | Atalho | `is-style-pna-atalho` | Blocos verdes de "Dúvidas" | `botoes.css` |
| Título | Título de seção | `is-style-pna-titulo-secao` | "POSTAGENS", "PETS PARA ADOÇÃO" | `titulos.css` |
| Parágrafo | Link com seta | `is-style-pna-link-seta` | "Ver mais →" | `titulos.css` |
| Parágrafo | Número em destaque | `is-style-pna-numero` | "00" contornado | `titulos.css` |
| Grupo | Card PNA | `is-style-pna-card` | Card genérico com borda verde | `cards.css` |
| Grupo | Card de postagem | `is-style-pna-card-postagem` | Cards de "Postagens" | `cards.css` |
| Grupo | Card de doação | `is-style-pna-card-doacao` | "Doe alimentos" etc. | `cards.css` |
| Grupo | Caixa de destaque | `is-style-pna-destaque` | "Contagem de adoções", "Padrinhos" | `destaques.css` |
| Imagem / Imagem destacada | Foto com moldura | `is-style-pna-foto` | Fotos com borda verde | `cards.css` |

## 2. Padrões (categoria "PNA" no editor)

| Padrão | Arquivo | Conteúdo |
|---|---|---|
| Grade de postagens | `patterns/grade-postagens.php` | Título, 4 postagens mais recentes em 2 colunas, "Ver mais" |
| Cards de doação | `patterns/cards-doacao.php` | Título e 3 cards (alimentos, itens, dinheiro) |
| Atalhos de dúvidas | `patterns/atalhos-duvidas.php` | Título e 4 atalhos para a página Dúvidas |
| Contador de adoções | `patterns/contador-adocoes.php` | Caixa de destaque e botão "Adote um pet". **Números fixos por enquanto**: o plugin vai torná-los automáticos |
| Pets para adoção | `patterns/grade-pets.php` | Título, 4 pets mais recentes (sem adotados) e "Ver mais" |
| Conteúdo da página Dúvidas | `patterns/conteudo-duvidas.php` | Textos do Figma com âncoras (`#quem-somos`, `#como-adotar`, `#buscar-pet`, `#apadrinhamento`, `#maus-tratos`) |
| Conteúdo da página Doações | `patterns/conteudo-doacoes.php` | Textos do Figma com âncoras (`#alimentos`, `#itens`, `#dinheiro`, `#pix`) |
| Política de Privacidade (rascunho) | `patterns/politica-privacidade.php` | Modelo baseado nos dados que o site coleta; usado pela configuração inicial (não aparece no seletor) |
| Legenda de personalidade | `patterns/legenda-personalidade.php` | Ícones Sociável/Brincalhão/Carinhoso (usada na galeria; não aparece no seletor) |
| Guia de estilo | `patterns/guia-de-estilo.php` | Página de referência (não aparece no seletor de padrões) |

O **Contador de adoções** agora usa o bloco `pna/contador-adocoes`, com números reais.

## 2.1 Blocos do tema

Cada pasta em `themes/pna-theme/blocks/` com um `block.json` é registrada automaticamente. Todos são gerados no servidor (`render.php`).

| Bloco | Onde é usado | O que faz |
|---|---|---|
| `pna/header-acoes` | Cabeçalho | Cadastro/Entrar ou sino/perfil |
| `pna/breadcrumb` | Todas as páginas internas | Trilha de navegação |
| `pna/card-pet` | Dentro de um bloco Consulta de pets | Card de pet, com selo "Em processo de adoção" quando for o caso |
| `pna/filtro-pets` | Galeria (`archive-pet.html`) | Filtro por idade, porte, espécie e sexo, pela URL, sem JavaScript |
| `pna/pet-detalhes` | Página do pet (`single-pet.html`) | Foto, dados, saúde, personalidade, padrinhos e botões |
| `pna/contador-adocoes` | Padrão "Contador de adoções" | Adoções concluídas hoje e no total |

### Botões da página do pet, conforme a situação

| Situação | Adotar | Apadrinhar |
|---|---|---|
| Disponível | ✅ | ✅ |
| Em processo de adoção | Aviso "já está em processo de adoção" | ✅ |
| Adotado | Aviso "já encontrou um lar!" | — |

Visitantes que clicam em Adotar ou Apadrinhar vão para o login e, depois, voltam ao formulário.

## 2.1.1 Blocos do plugin (área logada)

Ficam em `plugins/pna-core/blocks/` e usam as classes de formulário deste documento.

| Bloco | Página |
|---|---|
| `pna-core/entrar` | Entrar |
| `pna-core/cadastro` | Cadastro |
| `pna-core/perfil` | Meu perfil (cabeçalho, minhas solicitações, meus dados, senha, privacidade) |
| `pna-core/formulario` | Adotar e Apadrinhar (atributo `tipo`) |

Classes novas, estilizadas em `assets/css/componentes/conta.css`: `pna-aviso` (`--sucesso`, `--erro`), `pna-conta`, `pna-perfil`, `pna-solicitacoes` / `pna-solicitacao--{status}`, `pna-solicitar`, `pna-link-botao`.

## 2.2 Templates

| Template | Página |
|---|---|
| `front-page.html` | Página inicial (a imagem ao lado do contador é a **imagem destacada** da página "Página inicial") |
| `archive-pet.html` | Galeria `/pets/` (12 por página) |
| `single-pet.html` | Página de cada pet |
| `home.html` | Lista de postagens `/postagens/` (6 por página) |
| `single.html` | Cada postagem |
| `page.html` | Páginas comuns (Dúvidas, Doações…) |
| `pagina-conta.html` | Template escolhível "Página de conta e formulários": breadcrumb e conteúdo, sem título (usado pelas páginas da área logada) |
| `404.html` | Página não encontrada |
| `index.html` | Reserva (qualquer outra listagem) |

A quantidade de itens por página fica em `themes/pna-theme/inc/consultas.php`.

## 3. Componentes com estrutura HTML fixa

Usados pelo plugin `pna-core` (e pelos blocos do tema). Os ícones são impressos com `pna_icone( 'nome' )`, função do tema.

### Card de pet (`cards.css`)

```html
<article class="pna-card-pet">
	<a class="pna-card-pet__link" href="URL DO PET">
		<img class="pna-card-pet__foto" src="..." alt="Foto de Nina Oreo">
		<span class="pna-card-pet__rodape">
			<span class="pna-card-pet__nome">Nina Oreo</span>
			<span class="pna-card-pet__sexo">
				<!-- pna_icone( 'sexo-femea' ) ou pna_icone( 'sexo-macho' ) -->
				<span class="pna-so-leitor">Fêmea</span>
			</span>
		</span>
	</a>
</article>
```

O sexo precisa do texto escondido (`pna-so-leitor`): o ícone sozinho não é lido por leitores de tela.

### Notificação (`notificacoes.css`)

```html
<ul class="pna-notificacoes">
	<li class="pna-notificacao-item">
		<article class="pna-notificacao pna-notificacao--nao-lida">
			<span class="pna-notificacao__icone"><!-- pna_icone( ... ) --></span>
			<div class="pna-notificacao__corpo">
				<h3 class="pna-notificacao__titulo">Adoção aprovada!</h3>
				<p class="pna-notificacao__texto">...</p>
			</div>
			<time class="pna-notificacao__tempo" datetime="2026-09-26T09:00">3 horas atrás</time>
		</article>
		<button type="button" class="pna-notificacao__excluir" aria-label="Excluir notificação: Adoção aprovada!">
			<!-- pna_icone( 'lixeira' ) -->
		</button>
	</li>
</ul>
```

`pna-notificacao--nao-lida` deixa o fundo verde claro. O `aria-label` do botão deve citar o título da notificação.

### Formulário (`formularios.css`)

```html
<form class="pna-form">
	<h3 class="pna-form__secao-titulo">Seus dados</h3>
	<div class="pna-form__grade"> <!-- 2 colunas de 480px -->

		<!-- Campo simples -->
		<div class="pna-campo">
			<label class="pna-campo__rotulo" for="nome">Nome completo</label>
			<input class="pna-campo__entrada" id="nome" type="text">
		</div>

		<!-- Campo com ajuda -->
		<div class="pna-campo">
			<label class="pna-campo__rotulo" for="pet">Pet escolhido</label>
			<input class="pna-campo__entrada" id="pet" type="text" readonly aria-describedby="pet-ajuda">
			<p class="pna-campo__ajuda" id="pet-ajuda">Vem da página do pet.</p>
		</div>

		<!-- Campo com erro: classe no wrapper + aria-invalid + mensagem ligada ao campo -->
		<div class="pna-campo pna-campo--erro">
			<label class="pna-campo__rotulo" for="nasc">Data de nascimento</label>
			<input class="pna-campo__entrada" id="nasc" type="text" aria-invalid="true" aria-describedby="nasc-erro">
			<p class="pna-campo__erro" id="nasc-erro">A adoção é permitida apenas para maiores de 18 anos.</p>
		</div>

		<!-- Campo que ocupa as duas colunas -->
		<div class="pna-campo pna-campo--largo">
			<label class="pna-campo__rotulo" for="motivo">Por que você decidiu adotar?</label>
			<textarea class="pna-campo__entrada" id="motivo" maxlength="1000"></textarea>
			<p class="pna-campo__contador">0 / 1000 caracteres</p>
		</div>
	</div>

	<!-- Checkbox ou radio -->
	<label class="pna-opcao">
		<input class="pna-opcao__marcador" type="checkbox"> <span>Estou ciente...</span>
	</label>

	<!-- Escolha de valor (radios com aparência de botão) -->
	<fieldset class="pna-escolhas">
		<legend class="pna-so-leitor">Valor mensal</legend>
		<label class="pna-escolha"><input type="radio" name="valor" value="25"><span>R$ 25</span></label>
	</fieldset>

	<div class="pna-form__acoes">
		<button type="submit" class="wp-element-button">Enviar</button>
	</div>
</form>
```

Regras:

- Todo campo tem `<label>` visível ligado pelo `for`/`id`. O placeholder é só um exemplo.
- Erros: classe `pna-campo--erro`, `aria-invalid="true"` e mensagem ligada por `aria-describedby`.
- O botão de envio usa `wp-element-button` e herda o estilo de botão do `theme.json`.

### Paginação (`paginacao.css`)

Estiliza o bloco nativo **Paginação da consulta**. Não precisa de classe extra.
