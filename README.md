# Extbaser Bundle
Create a TYPO3 Extbase Extension from a Symfony application.
* Extbaser project homepage: https://github.com/edrush/extbaser

## Installation
Define the following requirement in your composer.json file:
```
"require": {
    "edrush/extbaser-bundle": "*",
}
```

## Usage

Convert mapping information to a TYPO3 Extbase Extension:
```
php app/console extbaser:export target_extension_key
```
Now continue with step 2 [here](https://github.com/edrush/extbaser#2-upload-the-extension-to-your-typo3-installation).
