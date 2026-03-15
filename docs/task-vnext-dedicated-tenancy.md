# Task VNext: Dedicated Tenancy Real no Backend

## Título
Implementar tenancy dedicada real com roteamento de conexão por tenant no backend Symfony.

## Contexto
O sistema já possui:
- `Company` como fronteira de tenancy
- `TenantInstance` com `tenancyMode` (`shared` ou `dedicated`)
- resolução de tenant/company por request
- enforcement de contexto organizacional e validação operacional de tenant

Hoje, porém, tenants `dedicated` ainda não usam conexão física dedicada no Doctrine/DBAL. O modo `dedicated` está tratado como contexto operacional, não como isolamento real de banco.

## Objetivo
Evoluir a infraestrutura multi-tenant para que tenants `dedicated` utilizem conexão/banco dedicado em runtime, sem alterar a modelagem de domínio nem espalhar lógica de tenancy pelos módulos.

## Motivação
- atender o modelo enterprise previsto na arquitetura canônica
- garantir isolamento físico real para tenants `dedicated`
- preparar jobs, integrações e operações para ambientes mistos `shared` + `dedicated`
- evitar que tenants dedicados sejam operados sobre a conexão compartilhada

## Escopo
### Incluído
- resolver configuração de conexão por `tenant_instances.database_key`
- introduzir provider/factory de conexão Doctrine/DBAL por tenant
- aplicar a troca de conexão para requests HTTP autenticados e públicos com contexto de tenant
- suportar `shared` e `dedicated` no mesmo código-base
- padronizar configuração de tenants dedicados por ambiente
- expor observabilidade mínima do tenant/conexão ativa
- garantir fallback seguro e falha explícita quando tenant `dedicated` não estiver configurado
- cobrir comandos/entrypoints backend que precisem executar com contexto de tenant

### Não incluído
- refatoração de domínio funcional
- quebra do schema atual
- particionamento de código por cliente
- fila/outbox distribuído
- replicação, sharding ou failover avançado

## Estado Atual
Os pontos já existentes e que devem ser preservados:
- [`TenantInstance.php`](/home/jlazzarotto/Projects/prosperium-v2/backend/src/Company/Domain/Entity/TenantInstance.php)
- [`TenantResolver.php`](/home/jlazzarotto/Projects/prosperium-v2/backend/src/Shared/Infrastructure/MultiTenancy/TenantResolver.php)
- [`TenantRequestListener.php`](/home/jlazzarotto/Projects/prosperium-v2/backend/src/Shared/Infrastructure/MultiTenancy/TenantRequestListener.php)
- [`TenantContext.php`](/home/jlazzarotto/Projects/prosperium-v2/backend/src/Shared/Infrastructure/MultiTenancy/TenantContext.php)
- [`TenantDatabaseConfigRegistry.php`](/home/jlazzarotto/Projects/prosperium-v2/backend/src/Shared/Infrastructure/MultiTenancy/TenantDatabaseConfigRegistry.php)

## Requisitos Funcionais
1. Requests de tenants `shared` continuam usando a conexão padrão.
2. Requests de tenants `dedicated` devem usar conexão específica do tenant.
3. A resolução deve aceitar `database_key` canônico como identificador operacional.
4. Se tenant `dedicated` estiver sem configuração válida, a aplicação deve falhar explicitamente.
5. O modo `shared` não pode sofrer regressão.
6. O comportamento deve funcionar para endpoints autenticados e públicos que dependam de contexto de tenant.

## Requisitos Técnicos
1. Não mover regra de negócio para controllers.
2. Não alterar contratos de domínio por causa da infra de tenancy.
3. Centralizar a estratégia de conexão em infraestrutura.
4. Evitar dependência de `$_ENV` espalhada por services de domínio/aplicação.
5. Tornar a solução compatível com comandos CLI, workers e processamento assíncrono futuro.
6. Manter compatibilidade com Doctrine ORM e migrations atuais.

## Proposta de Implementação
### Camada de infraestrutura
- criar um `TenantConnectionResolver` responsável por devolver os parâmetros DBAL do tenant ativo
- criar um `TenantAwareConnectionFactory` ou estratégia equivalente para abrir conexão correta por request
- encapsular a criação de `EntityManager`/`Connection` tenant-aware sem contaminar os módulos

### Fonte de configuração
- usar `tenant_instances.database_key` como chave canônica
- mapear `database_key -> DATABASE_URL` por configuração segura de ambiente
- manter configuração default para `shared`

### Bootstrap HTTP
- no início do request, resolver `tenantId`, `companyId`, `tenancyMode` e `databaseKey`
- se `tenancyMode = dedicated`, materializar a conexão dedicada antes de acesso persistente
- garantir falha explícita se o tenant dedicado estiver sem configuração

### CLI e jobs
- introduzir padrão para comandos executarem com `--tenant` ou contexto equivalente
- documentar que workers e jobs futuros deverão inicializar `TenantContext` antes de acessar repositórios

### Observabilidade
- logar `company_id`, `database_key`, `tenancy_mode` e `connection_name` ou equivalente
- expor no healthcheck somente sinalização segura, sem credenciais

## Critérios de Aceite
1. Um tenant `shared` opera normalmente na conexão padrão.
2. Um tenant `dedicated` opera em conexão distinta da padrão.
3. Um tenant `dedicated` sem configuração falha com erro explícito e auditável.
4. A troca de conexão não exige alteração em services de domínio/aplicação.
5. `php bin/console doctrine:schema:validate` continua OK.
6. `php bin/console lint:container` continua OK.
7. Testes automatizados cobrindo ao menos:
   - resolução `shared`
   - resolução `dedicated`
   - erro de tenant dedicado sem configuração
   - proteção contra uso de `database_key` inválido

## Riscos
- Doctrine em Symfony não gosta de troca improvisada de conexão no meio do ciclo de request
- commands, consumers e jobs podem escapar do contexto HTTP e usar conexão errada se não houver bootstrap próprio
- migrations por tenant dedicado exigirão estratégia explícita
- testes de integração precisarão simular mais de uma configuração DBAL

## Estratégia Recomendada
Implementar em duas etapas dentro da mesma versão:

### Etapa 1
- resolver conexão tenant-aware para HTTP
- falha explícita para `dedicated` sem config
- logs e healthcheck

### Etapa 2
- suporte formal para CLI/jobs/workers
- convenção de execução por tenant
- documentação operacional

## Fora do Escopo Imediato, mas Relacionado
- execução de migrations por tenant dedicado
- filas assíncronas tenant-aware
- outbox/inbox por tenant
- métricas por tenant
- failover e replicação por tenant dedicado

## Dependências
- configuração segura de `DATABASE_URL` por tenant dedicado
- definição de estratégia operacional para deploy/configuração por ambiente
- decisão de como commands e workers receberão o tenant ativo

## Definição de Pronto
- código implementado
- documentação operacional mínima adicionada
- testes automatizados cobrindo shared/dedicated
- validações Symfony/Doctrine verdes
- sem regressão nos módulos existentes

## Resultado Esperado
O backend passa a suportar tenancy híbrida real:
- `shared` com isolamento lógico por `company_id`
- `dedicated` com isolamento físico por conexão/banco

Sem retrabalho relevante nos módulos de negócio já implementados.
