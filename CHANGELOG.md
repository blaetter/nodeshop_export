# Changelog NodeShop Export

All notable changes to this module, grouped by topic. New entries go into the "Unreleased" section at the top; on deployment it gets the date.

Sections per version: Features, Security, Bugs, Removed, Maintenance (omit empty sections).

The CSV is imported into the Verlags-DB, which does not cope with any change of columns, order, charset or content. `tests/fixtures` contains the expected CSV; changes to it must be agreed with the Verlags-DB first.

## Unreleased

### Features
- Values that cause problems in the Verlags-DB (semicolons or line breaks, too long values) are reported after the export; the CSV itself stays unchanged

### Security
- The download is no longer stored by caches (`Cache-Control: private, no-store`)

### Bugs
- The order date was the date of the last change of the order, so a repeated export contained the date of the previous export; it is now the date of the checkout taken from the order history (first completion, otherwise the submit of the checkout), with the creation date as fallback for orders without history (agreed change of the CSV content, only the column "Bestelldatum" is affected)
- An order with a product of a deleted node broke the whole export; the product is now skipped and reported
- An invalid value removed its column, so all following columns of the row were shifted; it is now exported as empty column and reported
- Error messages were written into the downloaded file
- The message after the export was never shown, as the request was ended with `exit`

### Maintenance
- Compatibility with PHP 8.4 (explicitly nullable parameters, no null values for string functions, no undefined array keys) and the upcoming signature of `execute()`
- Unit tests, including a byte by byte comparison of the CSV with the output before the refactoring (only the order date differs)
- License and PHP version in `composer.json`
