# Sync de Municípios via IBGE

## Comando

```bash
php bin/console app:cadastro:sync-municipios-ibge
```

## Fonte

- Endpoint: `https://servicodados.ibge.gov.br/api/v1/localidades/municipios`

## Estratégia

- `codigo_ibge` é a chave natural do município.
- O processo faz `upsert` por `codigo_ibge`.
- Registros ausentes na fonte são marcados como `inactive`.
- O campo `hash_payload` evita update desnecessário quando o payload não mudou.

## Agendamento sugerido

```cron
0 3 * * * cd /home/jlazzarotto/Projects/prosperium-v2/backend && php bin/console app:cadastro:sync-municipios-ibge >> var/log/municipios-sync.log 2>&1
```
