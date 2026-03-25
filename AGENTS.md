# AGENTS.md - AI Agent Guide for Varshidhi

This Magento 2 e-commerce platform contains both third-party modules and custom merchant extensions. This guide helps AI agents understand the architecture and make productive changes.

## Architecture Overview

**Structure**: `/app/code/{Vendor}/{ModuleName}` - Standard Magento 2 module structure
- **Third-party modules**: Razorpay, Codazon, Magefan, Webkul, etc. - integrated payment gateways, themes, and utilities
- **Custom modules**: `Varsidhi/*` (Blousemeasurement, Lehengameasurement, etc.) - brand-specific product measurement features
- **Shared patterns**: Custom/PincodeChecker and Wbcom/PincodeChecker appear twice (region-specific implementations)

## Module Anatomy & Key Files

Every module follows this structure:
- `registration.php` - Module registration (namespace: `{Vendor}_{ModuleName}`)
- `etc/module.xml` - Module metadata and dependencies
- `etc/config.xml` - Default configuration in `<payment>` or `<system>` sections
- `etc/di.xml` - Dependency injection, virtual types, plugin definitions
- `etc/events.xml` - Event observers for Magento events
- `Model/` - Business logic and payment processing
- `Controller/` - Request handlers (frontend/adminhtml)
- `Block/`, `Ui/` - UI components for admin/frontend
- `Setup/` - Database schema and data patches
- `view/{area}/templates/` - PHTML templates and static files

## Critical Patterns

### Payment Gateway Integration (Razorpay Example)
- **Config handling**: `Model/Config.php` centralizes all configuration keys (API credentials, webhook settings, cron timeouts)
- **Payment method**: `Model/PaymentMethod.php` extends `AbstractMethod` and implements `authorize`, `capture`, `refund` capabilities
- **Webhook security**: CSRF validation is skipped for webhook endpoints via `Plugin/CsrfValidatorSkip.php`
- **Cron jobs**: Separate jobs for `CancelPendingOrders` and `UpdateOrdersToProcessing` (configured in `etc/crontab.xml`)
- **Logging**: Custom monolog handler via DI (`RazorpayLogger` virtual type in `di.xml`)
- **Events**: Observes `sales_order_payment_place_end` to capture payment details post-order creation

### Custom Module Patterns (Varsidhi)
- **Model structure**: Simple model with `ResourceModel/` for database operations
- **UI components**: Measurement forms stored in `Ui/` directory for product attribute management
- **Controllers**: Separated by area (`Adminhtml/` for backend, `Index/` for frontend)
- **Data registration**: Models defined in `registration.php` with author/copyright headers

### GraphQL Support
- Razorpay module includes `etc/graphql/schema.graphqls` and GraphQL resolvers in `Model/Resolver/`
- Used for headless checkout flows with mutations like `setPaymentMethodOnCart` and `placeRazorpayOrder`

## Developer Workflows

### Adding a New Module
1. Create `/app/code/{Vendor}/{ModuleName}/` directory
2. Add `registration.php` with ComponentRegistrar call
3. Create `etc/module.xml` with version and dependencies
4. Define DI in `etc/di.xml` (controllers, model factories, plugins)
5. Run: `bin/magento module:enable {Vendor}_{ModuleName}`
6. Clear cache: `bin/magento cache:clean`

### Payment Module Configuration
- Add `<payment>` section in `etc/config.xml` with default values
- Define payment model extending `AbstractMethod`
- Configure payment action in config (authorize, capture, or authorize_capture)
- Register webhook endpoint controller extending `BaseController`
- Add CSRF skip for webhook if needed via Plugin on `CsrfValidator`

### Custom Product Forms (Measurement)
- Create model extending `AbstractDb` with `ResourceModel`
- Add UI form components in `Ui/` for admin grid/form display
- Create frontend controller for customer-facing forms
- Link to products via `SetupUpgradeData` patches for attribute assignment

### Cron Jobs
1. Create class in `Cron/` implementing execute method
2. Declare in `etc/di.xml` with logger injection
3. Configure schedule in `etc/crontab.xml` with group and schedule expression
4. Install with: `bin/magento cron:install`

## External Dependencies & Integration Points

- **Razorpay PHP SDK**: `razorpay/razorpay` (v2.*) - payment processing API
- **HTTP Requests Library**: `WpOrg\Requests` (v1.6.0+) - GitHub API calls for upgrade notifications
- **Database**: MySQL via Magento resource models in `Setup/` and `Model/ResourceModel/`
- **Webhooks**: Razorpay callbacks to `/razorpay/payment/webhook` validate signature from config `webhook_secret`
- **Admin System Messages**: System notification interface for upgrade alerts

## Configuration Management

**Config hierarchy** (highest priority first):
1. Admin UI (`Stores > Configuration > Payment Methods > Razorpay`)
2. `.env` / environment variables (if used)
3. `etc/config.xml` defaults
4. Database (`core_config_data` table)

**Key config keys pattern**: `payment/{method_code}/{config_key}` e.g., `payment/razorpay/key_id`

## Common Tasks & Commands

- Enable module: `bin/magento module:enable {Vendor}_{ModuleName}`
- Clear caches: `bin/magento cache:clean` (especially after config/DI changes)
- Install schema: `bin/magento setup:upgrade` (runs Setup patch classes)
- Static files: `bin/magento static:deploy` (frontend JS/CSS)
- Check module status: `bin/magento module:status`

## Important Conventions

- **Namespacing**: `{Vendor}\\{ModuleName}\\{SubNamespace}` mirrors directory structure
- **Config constants**: Define in Config class as `const KEY_*` for typo prevention
- **Error handling**: Use `LocalizedException` for user-facing errors, preserve stack traces in logs
- **Security**: Skip CSRF validation only for webhook/API endpoints; validate payment signatures against stored secrets
- **Logging**: Leverage DI-injected logger (monolog) instead of `echo` or `var_dump`
- **Database**: Use ResourceModel and Collections, never raw SQL queries

