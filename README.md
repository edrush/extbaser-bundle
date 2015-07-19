# Extbaser Bundle
Create a TYPO3 Extbase Extension from an existing Symfony database.
* Extbaser project homepage: https://github.com/edrush/extbaser

## Installation
Define the following requirement in your composer.json file:
```
"require": {
    "edrush/extbaser-bundle": "*",
}
```

## Usage

Export your database schema to a TYPO3 extension with following command:
```
php app/console extbaser:export target_extension_key
```
Now continue with step 2 [here](https://github.com/edrush/extbaser#2-upload-the-extension-to-your-typo3-installation).
