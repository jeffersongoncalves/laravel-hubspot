# Changelog

All notable changes to this project will be documented in this file.

## 1.0.0 - 2026-09-08

Initial release.

- CRUD for any CRM object type (contacts, companies, deals, tickets, products, line items, custom objects)
- Search: raw payloads or a single-property filter helper
- Associations: create and read them between object types
- Form submissions and marketing emails
- Automatic retry on 429 (HubSpot caps requests at 100 per 10 seconds)
- `HubspotException` carrying the original API error body on any non-2xx response

## [Unreleased]
