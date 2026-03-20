# ElasticSearchService

ElasticSearchService è un package PHP progettato per fornire un'integrazione semplice ed efficiente con Elasticsearch per applicazioni di controllo del budget.

## Caratteristiche

- **Integrazione Facile**: Il package offre un processo di integrazione semplice e diretto, permettendo agli sviluppatori di incorporare rapidamente le funzionalità di ricerca e indicizzazione Elasticsearch nelle loro applicazioni.

- **Configurazione Flessibile**: Con ElasticSearchService, hai la flessibilità di configurare varie impostazioni di Elasticsearch, come host, porta, credenziali di autenticazione e indicizzazione.

- **Gestione Transazioni**: Il package supporta l'indicizzazione e la ricerca di transazioni finanziarie con supporto per aggregazioni e statistiche avanzate.

- **Ricerca in Linguaggio Naturale**: Implementa funzionalità di ricerca avanzata che permettono di interrogare i dati utilizzando un linguaggio naturale.

## Componenti Principali

### Client
- **ElasticSearchClient**: Client principale per la comunicazione con Elasticsearch

### Servizi
- **ElasticSearchService**: Servizio principale per la gestione delle operazioni Elasticsearch
- **Indexer**: Gestisce l'indicizzazione dei documenti
- **Search**: Fornisce funzionalità di ricerca avanzata
- **NaturalLanguageSearch**: Implementa la ricerca in linguaggio naturale
- **FinanceStats**: Gestisce le statistiche finanziarie

### Entità
- **TransactionInterface**: Interfaccia per le transazioni
- **EntryTransaction**: Gestione delle transazioni di entrata
- **WalletTransaction**: Gestione delle transazioni del portafoglio

## Installazione

Installa il package utilizzando Composer:

```bash
composer require budgetcontrol/elasticsearch-service
```

## Esempio d'Uso

```php
use BudgetControl\ElasticSearchService\Services\ElasticSearchService;
use BudgetControl\ElasticSearchService\Services\Clients\ElasticSearchClient;

// Inizializzazione del client
$client = new ElasticSearchClient($host, $port, $username, $password);
$elasticService = new ElasticSearchService($client);

// Indicizzazione di una transazione
$transaction = new EntryTransaction($data);
$elasticService->indexTransaction($transaction);

// Ricerca delle transazioni
$results = $elasticService->searchTransactions($query, $filters);
```
