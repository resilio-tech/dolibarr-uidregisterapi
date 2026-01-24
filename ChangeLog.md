# CHANGELOG UID REGISTER API FOR [DOLIBARR ERP CRM](https://www.dolibarr.org)

## 1.2

### Improvements
- Cleaned up legacy MYOBJECT/MYMODULE template code from module descriptor
- Simplified permissions structure using translation keys
- Renamed UIDREGISTERAPI_MYPARAM1 to UIDREGISTERAPI_SIRENE_API_KEY for clarity
- Updated lang file with proper translations
- Removed unused code from setup page and index page
- Added version bump workflow for automated version management
- Added release trigger to build workflow

### Bug Fixes
- Fixed permission checks to use modern `hasRight()` method
- Updated menu permissions to use proper permission rules

## 1.1

### Features
- Added French SIRENE API integration for company lookup
- Added SIRENE API key configuration in module settings
- Added country switcher (CH/FR) for company search
- Fill form with SIRET information from SIRENE API

### Bug Fixes
- Fixed query quoting issue

## 1.0

### Features
- Initial version
- Swiss UID Register API integration
- Auto-complete company name on third party creation
- Auto-fill company information (UID, RC number, address, VAT status)
- Support for updating existing third parties with UID data
