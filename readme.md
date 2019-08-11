# TRM WordPress Affiliation

Un plugin d'affiliation pour TRM

## Getting Started

Ce module à été developper pour la société TRM par Ayrton LECOUTRE de la SARL WEEBEDIA. Pour toutes questions vous pouvez envoyez un mail à contact@weebedia.com ou ayrtonpgt@gmail.com

### Prerequisites

Vous devez disposez des licenses des extensions suivantes :

```
TRM WP Affiliation
WooCommerce
User Role Editor
Advanced Custom Fields
Admin Columns Pro
Admin Columns Pro - Advanced Custom Fields (ACF)
Custom Post Type UI
```

### Installing

Installer tout les plugins requis

```
Vous pouvez ajouter des extensions sur WordPress en Administrateur dans l'outil Extensions/Ajouter
```

```
Le plugin de TRM est dans l'archive trm-wp-affiliation.zip de ce dossier
```

Importer les parametres des plugins suivant :
```
Advanced Custom Fields
Via le fichier acf-export.json
```

```
Admin Columns Pro
Via le fichier admin-column-export.json
```

```
Custom Post Type UI
Via le fichier cpt-ui-param.json
```

```
User Role Editor
Créer un role avec les param suivants:

id : partenaires
Nom : Partenaire
```

## Running the tests

Pour tester, rendez vous dans l'administration WordPress

```
Créer un produit WooCommerce
```

```
Créer un nouveau Cashback pour le produit
```

```
Créer une nouvelle Campagne d'affiliation avec le Cashback précedent
```

```
Commander le produit en question par le lien d'affiliation sur la fiche produit
```

```
Retrouvez les resultat de l'affiliation dans l'onglet Resultat Affiliation sur WordPress
```

## Authors

* **Ayrton LECOUTRE** - *Initial work* - [Simerca](https://github.com/Simerca)

## Acknowledgments

* Hat tip to anyone whose code was used
* Inspiration
* etc
