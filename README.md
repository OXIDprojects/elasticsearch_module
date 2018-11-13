# Elasticsearch module for OXID eShop

Full-text search using elasticsearch - everyone wants it - everyone needs it - We deliver!

# Special Thanks to
Syseleven (https://www.syseleven.de/)
fleur ami (https://www.fleur-ami.com/ and https://www.tingo-living.com/)
Marco Steinhäuser (Oxid Hackathon 2018 - https://forum.oxid-esales.com/u/marco.steinhaeuser/summary)

# IMPORTANT

This module is not for production use at the moment!

# How to activate

- activate module
- renew views
- modify module params
- go to Elasticsearch->Status if green: everything is ok... not green: your cluster is not healthy - does not open: module params wrong
- go to Elasticsearch->Cronjob -> read the text in german!!!

# ToDo
- File: ElasticsearchStatus.php
    -> if no elasticsearch client is found, an error is thrown... better: test with curl, best: https://github.com/elastic/elasticsearch-php/issues/571
  
- File: ElasticsearchCron.php
    -> define mappings
    -> improve renew index - slow
    -> Language Analyzers
    -> bulk api (maybe issues with php timeouts and memory?!?)
    -> more hosts

- File: oxcom_elastic_status_admin_list.tpl
    -> All Cronjobs are possible for one language >> Add select field and a little javascript

- Lang
    -> en: a lot is missing

- Cronjobs
    -> sh exec for linux systems

- Security
  -> Cronjobs need a authentification
 
# Improvements 
    - Example for search (maybe new module)
    - Filtersystem based on elasticsearch
    - howto: import stoppwords
    - Tests, Tests, Tests
