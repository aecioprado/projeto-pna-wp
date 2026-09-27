# 0004 – Adoção e apadrinhamento como uma única entidade, com status em metadado

- **Status:** aceita
- **Data:** 27/09/2026

## Contexto

Adoções e apadrinhamentos têm quase o mesmo ciclo (enviada, em análise, decisão) e são avaliados pela mesma equipe.

## Decisão

- Uma única entidade, **Solicitação** (`pna_solicitacao`), com o campo `_pna_tipo`.
- O status fica em um **metadado** (`_pna_status`), não nos status nativos de post do WordPress, que têm suporte limitado no painel. O post fica sempre **privado**.
- O status só muda pela função `pna_core_alterar_status_solicitacao()`, que concentra validação, histórico e efeitos no pet.

## Implicações

- Uma só tela de análise e uma só lista, com filtro por tipo.
- Relatórios e contadores usam consultas por metadado (adequado ao volume do projeto).
- Nenhum código deve alterar `_pna_status` diretamente; isso pularia histórico, efeitos no pet e notificações.
