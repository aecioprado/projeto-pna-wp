# Documentação do projeto PNA

- [Design tokens](design-tokens.md): cores, fontes, espaçamentos e como usá-los.
- [Componentes](componentes.md): estilos de bloco, padrões e estrutura HTML de cada componente.
- [Modelo de dados](modelo-de-dados.md): pets, solicitações, fluxos de status e papéis.
- [Pendências](pendencias.md): o que ainda depende de informação externa ou de fases futuras.

## Registro de decisões

Cada decisão importante fica em um arquivo numerado em `decisoes/`, com contexto, implicações e como desfazer.

| Nº | Decisão | Status |
|---|---|---|
| [0001](decisoes/0001-contraste-das-cores.md) | Tons ajustados de verde e cinza para garantir contraste | Aceita |
| [0002](decisoes/0002-escopo-apenas-desktop.md) | Escopo apenas desktop | Aceita |
| [0003](decisoes/0003-formulario-proprio-para-pets.md) | Cadastro de pets com formulário próprio, sem editor de blocos | Aceita |
| [0004](decisoes/0004-solicitacao-unica-com-status.md) | Adoção e apadrinhamento como uma única entidade, com status em metadado | Aceita |
| [0005](decisoes/0005-notificacoes-por-ultimo.md) | Notificações implementadas por último e validadas na hospedagem | Aceita |
| [0006](decisoes/0006-cadastro-sem-cpf-e-sem-login-social.md) | Cadastro sem CPF e sem login social na primeira versão | Aceita (a confirmar) |

Para registrar uma nova decisão, copie um arquivo existente, use o próximo número e atualize esta tabela. Decisões não são apagadas: quando mudam, o arquivo antigo recebe o status "substituída" e aponta para o novo.
