# 0002 – Escopo apenas desktop

- **Status:** aceita
- **Data:** 26/09/2026
- **Decidido por:** responsável pelo projeto

## Contexto

O Figma do projeto só tem telas desktop, com canvas de 1280px. Não há versões para celular ou tablet.

## Decisão

O escopo do projeto é **apenas desktop**. Não serão criados breakpoints, menus mobile, versões adaptadas de componentes nem testes em telas pequenas.

## Implicações

- A largura-alvo é **1280px**, com conteúdo em 1160px (tokens `layout.wideSize` e `layout.contentSize` do `theme.json`).
- Tamanhos de fonte são fixos, exatamente como no Figma (tipografia fluida desligada).
- Abaixo de 1280px a página mantém a largura mínima (`body { min-width }` em `assets/css/base.css`) e o navegador mostra rolagem horizontal. O layout nunca se reorganiza nem "quebra".
- Em celulares o site abre, mas sem adaptação. Isso é consequência desta decisão, não um defeito.

## Como mudar no futuro

Se o projeto passar a incluir mobile, será preciso: remover o `min-width` do `assets/css/base.css`, ligar `typography.fluid` no `theme.json`, desenhar as versões mobile no Figma e revisar cada template e pattern. Registre a mudança em um novo arquivo de decisão e marque este como "substituída".
