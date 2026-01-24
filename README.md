# UID REGISTER API FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## Features

This module allows you to connect to the Swiss UID Register and French SIRENE API when editing third parties from frontend.

**Note:** This module is NOT provided by the Swiss administration or French government.

Using `UID Register API`, you can:
- Quickly fill all available data using UID register (Switzerland) or SIRENE API (France) on third party creation
- Easily update third party information if registry data are different from yours
- Update your existing third parties with registry data when editing them
- Switch between Swiss (CH) and French (FR) company search

### Supported APIs

- **Swiss UID Register**: Free access, no API key required
- **French SIRENE API**: Requires an API key from [api.insee.fr](https://api.insee.fr/)

## Installation

### From the ZIP file and GUI interface

If you get the module in a zip file (like when downloading it from the market place [Dolistore](https://www.dolistore.com)), go into menu `Home - Setup - Modules - Deploy external module` and upload the zip file.

Note: If this screen tells you there is no custom directory, check your setup is correct:

In your Dolibarr installation directory, edit the `htdocs/conf/conf.php` file and check that following lines are not commented:

```php
//$dolibarr_main_url_root_alt ...
//$dolibarr_main_document_root_alt ...
```

Uncomment them if necessary (delete the leading `//`) and assign a sensible value according to your Dolibarr installation:

- UNIX:
    ```php
    $dolibarr_main_url_root_alt = '/custom';
    $dolibarr_main_document_root_alt = '/var/www/Dolibarr/htdocs/custom';
    ```

- Windows:
    ```php
    $dolibarr_main_url_root_alt = '/custom';
    $dolibarr_main_document_root_alt = 'C:/My Web Sites/Dolibarr/htdocs/custom';
    ```

### From a GIT repository

Clone the repository in `$dolibarr_main_document_root_alt/uidregisterapi`:

```sh
cd ....../custom
git clone git@github.com:resilio-tech/dolibarr-uidregisterapi.git uidregisterapi
```

### Final steps

From your browser:

1. Log into Dolibarr as a super-administrator
2. Go to "Setup" -> "Modules"
3. Find and enable the "UID Register API" module
4. Go to the module configuration page to set up your SIRENE API key (if you want to use French company search)

## Configuration

### SIRENE API Key (for French companies)

To use the French company search feature, you need to obtain an API key from INSEE:

1. Create an account on [api.insee.fr](https://api.insee.fr/)
2. Subscribe to the SIRENE API
3. Copy your API key
4. Go to the module configuration page in Dolibarr and paste your API key

## Usage

1. Go to Third Parties -> New Third Party
2. Start typing a company name in the "Name" field
3. Use the country button (CH/FR) to switch between Swiss and French search
4. Select a company from the autocomplete dropdown
5. The form will be automatically filled with the company information

## Licenses

### Main code

GPLv3 or (at your option) any later version. See file COPYING for more information.

### Documentation

All texts and readmes are licensed under GFDL.
