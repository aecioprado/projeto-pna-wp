# Design tokens do PNA

Todos os valores visuais do tema vivem em `themes/pna-theme/theme.json`. Esta tabela liga cada valor do Figma ao token correspondente.

No CSS, use sempre a variável gerada pelo WordPress, nunca o valor direto.

## Cores

| Token | Valor | Origem no Figma | Uso | Variável CSS |
|---|---|---|---|---|
| `primary` | `#35B08A` | Verde principal | Bordas, ícones, decoração (nunca texto) | `--wp--preset--color--primary` |
| `primary-strong` | `#2E9C7A` | Ajustado (ver [decisão 0001](decisoes/0001-contraste-das-cores.md)) | Fundo de botões, cabeçalho e rodapé | `--wp--preset--color--primary-strong` |
| `primary-text` | `#1F7A5E` | Ajustado (ver [decisão 0001](decisoes/0001-contraste-das-cores.md)) | Títulos, links, valores de campos | `--wp--preset--color--primary-text` |
| `primary-light` | `#8ED1B5` | Placeholders, botão desabilitado | Placeholders, estados inativos | `--wp--preset--color--primary-light` |
| `primary-pale` | `#C5E4CF` | Avatar alternativo | Fundos suaves | `--wp--preset--color--primary-pale` |
| `primary-tint` | `#E1F3E2` | Verde a 15% sobre creme | Fundo dos campos e caixas de destaque | `--wp--preset--color--primary-tint` |
| `background` | `#FFFFF1` | Fundo das páginas | Fundo geral | `--wp--preset--color--background` |
| `surface` | `#FCF9EA` | Fundo de login e cadastro | Superfícies, texto sobre verde | `--wp--preset--color--surface` |
| `text` | `#666666` | Ajustado de `#868686` (ver [decisão 0001](decisoes/0001-contraste-das-cores.md)) | Texto corrido, rótulos | `--wp--preset--color--text` |
| `white` | `#FFFFFF` | Texto dos botões | Texto sobre verde forte | `--wp--preset--color--white` |

O editor não permite cores fora desta paleta (`color.custom: false`), para evitar que o visual se desvie do design system.

## Tipografia

Fonte **Poppins**, hospedada no tema (`assets/fonts/poppins/`, licença SIL OFL 1.1 no arquivo `OFL.txt`), nos pesos 600, 700 e 800. Subconjunto latino, com todos os acentos do português.

| Token | Tamanho | Uso no Figma | Variável CSS |
|---|---|---|---|
| `x-small` | 13px | Textos de ajuda, legendas | `--wp--preset--font-size--x-small` |
| `small` | 16px | Descrições, textos de cards | `--wp--preset--font-size--small` |
| `medium` | 20px | Texto padrão, breadcrumb | `--wp--preset--font-size--medium` |
| `large` | 24px | Campos, títulos de seção, botões | `--wp--preset--font-size--large` |
| `x-large` | 32px | Títulos de card e notificação | `--wp--preset--font-size--x-large` |
| `xx-large` | 40px | Títulos de página | `--wp--preset--font-size--xx-large` |
| `huge` | 60px | Números em destaque | `--wp--preset--font-size--huge` |
| `display` | 96px | "LOGIN", "CADASTRO" | `--wp--preset--font-size--display` |

Padrões: texto em peso 600 com entrelinha 1,5; títulos em 700 com entrelinha 1,2 (o `h1` usa 600, como os títulos de página do Figma). Tamanhos fixos, sem tipografia fluida ([decisão 0002](decisoes/0002-escopo-apenas-desktop.md)).

## Espaçamentos

| Token | Valor | Variável CSS |
|---|---|---|
| `20` | 8px | `--wp--preset--spacing--20` |
| `30` | 12px | `--wp--preset--spacing--30` |
| `40` | 20px | `--wp--preset--spacing--40` |
| `50` | 28px | `--wp--preset--spacing--50` |
| `60` | 40px | `--wp--preset--spacing--60` |
| `70` | 60px | `--wp--preset--spacing--70` |
| `80` | 80px | `--wp--preset--spacing--80` |

As margens laterais da página usam o token `70` (60px), o que resulta em conteúdo de 1160px no canvas de 1280px.

## Formas e layout

| Token | Valor | Uso no Figma | Variável CSS |
|---|---|---|---|
| `radius.small` | 10px | Checkbox, botão secundário | `--wp--custom--radius--small` |
| `radius.medium` | 20px | Campos, botões, cards | `--wp--custom--radius--medium` |
| `border.thin` | 3px | Checkbox, botão secundário | `--wp--custom--border--thin` |
| `border.medium` | 4px | Cards e caixas | `--wp--custom--border--medium` |
| `border.thick` | 5px | Campos de formulário | `--wp--custom--border--thick` |
| `layout.canvas` | 1280px | Largura do Figma | `--wp--custom--layout--canvas` |

## Estilos já aplicados aos elementos

- **Links:** `primary-text`, negrito, sublinhados; o sublinhado some no hover; contorno visível no foco do teclado.
- **Títulos:** `primary-text`.
- **Botão padrão:** fundo `primary-strong`, texto branco em caixa alta, 24px, raio de 20px; fica `primary-text` no hover.
- **Botão estilo "Contorno"** (equivale ao "Apadrinhar" do Figma): fundo `surface`, borda de 3px `primary-strong`, texto `primary-text`, raio de 10px.

## Diferenças conscientes em relação ao Figma

- Três cores ajustadas para contraste ([decisão 0001](decisoes/0001-contraste-das-cores.md)).
- O botão "Entrar" do Figma usa a fonte Gothic A1; no tema, todos os botões usam Poppins, para manter uma única família.
