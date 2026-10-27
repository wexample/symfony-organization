# symfony-organization

Version: 3.0.6

A directory of organizations and their legal identity for Symfony

## Table of Contents

- [Introduction](#introduction)

`wexample/symfony-organization` is the directory of the organizations an application deals with — companies, sole traders, associations, private persons —, each once, with its legal identity: name, legal identifier, VAT number, legal form, contacts, the country it is registered in and the address it points at. Other packages point to an organization instead of copying its identity: in `symfony-accounting`, the ledger keeping its books and the parties it is to other ledgers. It also knows how each country identifies its organizations (SIREN/SIRET, BCE enterprise number, VAT numbers) and checks VAT numbers with VIES.
