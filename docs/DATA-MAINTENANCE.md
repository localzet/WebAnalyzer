# Maintenance and data handling

The `data/` directory includes device/browser signature datasets and binary GeoIP databases. Their presence in Git does **not** mean they are regularly refreshed, licensed for arbitrary redistribution, or fit for fraud/security decisions.

Before using location or ASN fields in production, audit the GeoIP database provider's license terms, source, update cadence, and attribution requirements. Consider switching to externally provisioned databases with version/checksum verification; do not delete or replace existing binary files without a compatibility migration.

`ci/php.yml` verifies Composer metadata, PHP syntax, autoload and basic object initialization. It is a smoke suite, **not** device fingerprinting accuracy or behavioral regression coverage.

No network data refresh or publishing is performed by CI.
