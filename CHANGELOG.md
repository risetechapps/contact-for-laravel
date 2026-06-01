# Changelog

Todas as alterações notáveis neste projeto serão documentadas neste arquivo.
O formato é baseado em [Keep a Changelog](https://keepachangelog.com/en/1.0.0/), e este projeto segue o [Versionamento Semântico](https://semver.org/lang/pt-BR/) (SemVer).

## [1.2.0] - 2026-06-01
### Added
- Novo método público `syncContacts(Request|array $data)` no trait `HasContacts` para sincronização explícita de contatos (útil em jobs e processamento em segundo plano, sem depender do evento `saved`)
- Novo `ContactPayloadResolver` para resolver o payload a partir de `Request` ou `array`, aceitando lista de contatos, `['contacts' => [...]]`, `person.contacts` ou um contato único

### Changed
- A lógica de persistência de contatos (update por `id`, criação, remoção dos ausentes e garantia de um único primário) foi movida do `ContactListener` para o método `syncContacts()` do trait `HasContacts`
- `ContactListener` agora apenas resolve a origem dos dados (request / `person.contacts` / fallback estático) e delega para `$model->syncContacts()`, eliminando duplicação de lógica

## [1.1.0] - 2026-04-28
### Added
- Dependências atualizadas
- **Sistema de Audit Log (Histórico de Mudanças)**:
  - Tabela `contact_histories` para rastrear todas as alterações
  - Model `ContactHistory` com métodos úteis (`getOldValues()`, `getNewValues()`, `fieldChanged()`)
  - Trait `HasAuditing` com eventos automáticos (created, updated, deleted, restored)
  - Registra: quem alterou, quando, o quê mudou, IP, user-agent e URL
  - Métodos no Contact: `histories()`, `latestHistory()`, `historiesForAction()`, `lastUpdatedBy()`, `createdBy()`
  - Resource `ContactHistoryResource` para API
- Configuração de campos auditáveis via propriedade `$auditable`
- Configuração de guard via propriedade `$auditGuard`
- **Gerenciamento Automático do Contato Principal**:
- Ao criar um contato, se não existir nenhum primário, ele automaticamente assume essa função
- Ao marcar um contato como primário, o anterior é automaticamente desmarcado (garante apenas 1 primário)
- Ao deletar o contato primário, outro contato do mesmo model é promovido automaticamente (respeitando `sort_order` e `created_at`)

## [1.0.0] - 2026-01-24
### Added
- Lançamento inicial (Primeira versão estável).
