# 0007 – Sem ambiente de testes (staging): site fechado até o lançamento e backup antes de cada atualização

- **Status:** aceita
- **Data:** 27/09/2026
- **Decidido por:** responsável pelo projeto

## Contexto

O plano original previa um ambiente de testes na hospedagem (um segundo WordPress, num subdomínio). O projeto é mantido por poucas pessoas, e um ambiente extra é mais uma instalação para atualizar, proteger e manter sincronizada.

Hospedagem: HostGator, Plano P (cPanel, backups automáticos diários, SSL grátis, acesso SSH e e-mail).

## Decisão

Não haverá ambiente de testes. Os dois papéis que ele cumpriria são cobertos assim:

1. **Testar antes de o público ver:** o plugin tem um **modo pré-lançamento**. Ligado, os visitantes veem só uma página "Em breve", enquanto a equipe, logada, usa o site completo. Ele é ligado automaticamente na primeira ativação do plugin e desligado no lançamento, em Ferramentas → PNA: configuração inicial.
2. **Voltar atrás se algo der errado:** **backup completo pelo cPanel antes de cada atualização**, além dos backups automáticos da HostGator. E o `.zip` da versão anterior guardado, para reinstalar se preciso.

## Implicações

- As atualizações acontecem direto no site no ar. Elas devem ser pequenas, feitas em horário de pouco movimento e sempre depois de validadas no ambiente local.
- O ambiente local passa a ser o único lugar de testes antes da publicação: nenhuma mudança vai para a hospedagem sem passar pelo roteiro de validação local.
- A validação das notificações (decisão 0005) acontece no site real, de preferência antes do lançamento, com o modo pré-lançamento ligado.

## Como mudar no futuro

O Plano P permite subdomínios ilimitados. Se o projeto crescer, basta criar um subdomínio (ex.: `testes.`), instalar um WordPress nele e seguir o `docs/publicacao.md` com o modo pré-lançamento sempre ligado.
