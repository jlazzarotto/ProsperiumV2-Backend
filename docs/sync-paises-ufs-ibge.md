# Sync de Países e UFs via IBGE

## Comandos

```bash
php bin/console app:cadastro:sync-paises-ibge
php bin/console app:cadastro:sync-ufs-ibge
```

## Endpoints de Consulta

```bash
GET /api/v1/paises
GET /api/v1/ufs
```

## Fontes

- Países: `https://servicodados.ibge.gov.br/api/v1/localidades/paises`
- UFs: `https://servicodados.ibge.gov.br/api/v1/localidades/estados`

## Agendamento sugerido

```cron
5 3 * * * cd /home/jlazzarotto/Projects/prosperium-v2/backend && php bin/console app:cadastro:sync-paises-ibge >> var/log/paises-sync.log 2>&1
10 3 * * * cd /home/jlazzarotto/Projects/prosperium-v2/backend && php bin/console app:cadastro:sync-ufs-ibge >> var/log/ufs-sync.log 2>&1
```
