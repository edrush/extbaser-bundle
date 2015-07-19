# Extbaser Bundle
Export an existing Symfony database scheme to a TYPO3 Extbase extension project. 
## Usage
### 1. Export your database scheme to a TYPO3 extension
```
php app/console extbaser:export your_new_extension_key
```
The generated extension consists of a folder containing the file *ExtensionBuilder.json*, which is the project file for the *TYPO3 Extension Builder*.

For more information, see [Extbaser](https://github.com/edrush/extbaser).