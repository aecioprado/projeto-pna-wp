# 0001 – Tons ajustados de verde e cinza para garantir contraste

- **Status:** aceita
- **Data:** 26/09/2026
- **Decidido por:** responsável pelo desenvolvimento do projeto
- **Arquivos afetados:** `themes/pna-theme/theme.json`, `themes/pna-theme/styles/fiel-ao-figma.json`

## Contexto

Contraste é a diferença de claridade entre a cor de um texto e a cor do fundo atrás dele. Ele é medido como uma razão que vai de 1:1 (texto invisível) a 21:1 (preto no branco).

A norma internacional de acessibilidade web, a [WCAG 2.2](https://www.w3.org/TR/WCAG22/#contrast-minimum), nível AA, exige:

- **4,5:1** para texto normal;
- **3:1** para texto grande (a partir de 24px, ou 18,7px em negrito).

O verde da marca PNA no Figma (`#35B08A`) é um verde claro. Usado como cor de texto sobre o fundo creme, ou como fundo de botões e do cabeçalho com texto claro por cima, ele não atinge esses mínimos. O cinza dos textos corridos (`#868686`) também fica abaixo.

Isso prejudica a leitura para pessoas com baixa visão ou daltonismo e para qualquer pessoa usando monitor antigo, tela com reflexo ou projetor (situação comum num projeto universitário).

Há também um aspecto formal: a Lei Brasileira de Inclusão (Lei 13.146/2015) prevê acessibilidade em sites, e, caso o site seja tratado como institucional da UFPE, pode se aplicar o [eMAG](https://www.gov.br/governodigital/pt-br/acessibilidade-e-usuario/acessibilidade-digital/modelo-de-acessibilidade), que adota os mesmos critérios de contraste. **Pendente:** confirmar com a coordenação do PNA se o site será considerado institucional.

## Decisão

Manter o verde original da marca nos elementos decorativos (bordas, ícones, fundos suaves) e **criar dois tons de apoio mais escuros**, usados apenas onde há texto. O cinza de texto também foi escurecido.

| Uso | Token | Figma | Contraste | Adotado | Contraste |
|---|---|---|---|---|---|
| Títulos e links verdes sobre o fundo creme | `primary-text` | `#35B08A` | 2,69:1 ❌ | `#1F7A5E` | 5,20:1 ✅ |
| Valor digitado dentro dos campos | `primary-text` | `#35B08A` | 2,34:1 ❌ | `#1F7A5E` | 4,53:1 ✅ |
| Texto branco sobre botões | `primary-strong` | `#35B08A` | 2,72:1 ❌ | `#2E9C7A` | 3,41:1 ✅ (texto grande) |
| Menu creme sobre o cabeçalho verde | `primary-strong` | `#35B08A` | 2,57:1 ❌ | `#2E9C7A` | 3,23:1 ✅ (texto grande) |
| Texto corrido | `text` | `#868686` | 3,61:1 ❌ | `#666666` | 5,69:1 ✅ |
| Bordas, ícones, fundos suaves | `primary` | `#35B08A` | não se aplica | `#35B08A` (sem mudança) | — |

Valores medidos contra o fundo creme `#FFFFF1` (token `background`) ou contra o verde correspondente.

### Regra de uso dos tokens

- `primary` → bordas, ícones e elementos decorativos. **Nunca** para texto.
- `primary-strong` → fundo de botões, cabeçalho e rodapé (texto branco ou creme por cima, sempre em tamanho grande: 24px ou mais).
- `primary-text` → títulos, links e qualquer texto verde sobre fundo claro.
- `text` → textos corridos, rótulos e breadcrumb.

## Implicações

- **Visual:** a diferença é sutil (um verde um pouco mais fechado). A identidade do PNA se mantém.
- **Desenvolvimento:** nenhum custo extra. Todos os componentes usam os tokens semânticos acima; trocar os valores muda o site inteiro de uma vez.
- **Figma:** as telas do Figma ficam com um tom levemente diferente do site. **Pendente:** avisar a pessoa responsável pelo design para atualizar o Figma com os novos tons.
- **Botões com texto pequeno:** o verde `primary-strong` só passa para texto grande. Botões com texto menor que 24px (ou 18,7px em negrito) devem usar `primary-text` como fundo.

### Exceção conhecida: placeholder dos campos

O texto de exemplo dentro dos campos vazios (placeholder) usa `primary-light` (`#8ED1B5`) sobre `primary-tint`, com contraste de apenas 1,52:1. A exceção foi aceita porque todos os campos têm **rótulo visível acima**, então o placeholder é só um exemplo complementar e nunca a única identificação do campo. Se no futuro algum campo ficar sem rótulo, esta exceção deixa de valer.

## Como voltar para as cores fiéis ao Figma

Os únicos tokens alterados em relação ao Figma são `primary-strong`, `primary-text` e `text`. Há duas formas de reverter.

### Opção A – Pelo painel, sem código (reversível a qualquer momento)

O tema inclui a variação de estilo **"Fiel ao Figma"** (`themes/pna-theme/styles/fiel-ao-figma.json`), que aplica exatamente as cores do Figma.

1. No painel do WordPress, acesse **Aparência → Editor**.
2. Clique em **Estilos** e depois em **Navegar pelos estilos**.
3. Escolha **"Fiel ao Figma"** e clique em **Salvar**.

Para desfazer, repita os passos e escolha **"Padrão"**.

A escolha fica gravada no banco de dados de cada ambiente, então precisa ser feita também na produção.

### Opção B – No código (mudança permanente, vale para todos os ambientes)

1. Crie um branch: `git switch -c chore/cores-fieis-ao-figma`
2. Em `themes/pna-theme/theme.json`, dentro de `settings.color.palette`, altere somente estes três valores:

   | Token | De | Para |
   |---|---|---|
   | `primary-strong` | `#2E9C7A` | `#35B08A` |
   | `primary-text` | `#1F7A5E` | `#35B08A` |
   | `text` | `#666666` | `#868686` |

3. Rode o site local (`npm start`) e confira as páginas.
4. Abra um Pull Request para a `main` e **atualize este documento**: mude o status para "substituída" e registre a nova decisão em um arquivo `docs/decisoes/000N-...md`.

Não é preciso alterar nenhum outro arquivo: CSS, templates e patterns usam apenas os nomes dos tokens, nunca os valores.

### Cuidados

- Se alguém tiver personalizado as cores em **Aparência → Editor → Estilos**, essa personalização tem prioridade sobre o `theme.json` e sobre a variação. Para limpar, use **Estilos → ⋮ → Redefinir**.
- Nunca "corrija" uma cor escrevendo o valor direto no CSS ou num bloco (`#35B08A`). Isso quebra a centralização e faz esta reversão deixar de funcionar.
- Para conferir o contraste de uma combinação nova, use o [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/).
