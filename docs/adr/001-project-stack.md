# ADR-001: Project Stack

## Context

The project is a CRM/B2B backend system for processing client tickets
It should demonstrate production-like backend-practices: API-Design
PostgreSQL schema design, Redis usage, queues, logging, testing, static analysis

## Decision

Use the following stack:

- PHP 8.4
- Laravel 13
- PostgreSQL 17
- Redis 7.4
- Nginx 1.30 stable
- PHP-FPM
- Docker Compose
- PHPUnit
- PHPStan/Larastan
- Laravel Pint

## Rationale

PHP 8.4 gives modern language features while staying stable enough for a backend project.

Laravel 13 is suitable for a new project because it has longer support than previous major versions and provides stable tools for HTTP, validation, queues, policies, migrations and testing.

PostgreSQL 17 is modern and stable enough for transaction-heavy CRM scenarios, indexing, constraints and query analysis.

Redis 7.4 is enough for cache, queues, rate limiting and short-lived locks. Redis 8 is not required because this project does not use its newer data structures or query engine.

Nginx stable is used as a production-like reverse proxy in front of PHP-FPM.

## Consequences

The project environment is close to a real backend setup, but still simple enough for local development.
