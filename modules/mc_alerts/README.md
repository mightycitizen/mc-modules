# MC Alerts

This module extends the [Sitewide Alert](https://www.drupal.org/project/sitewide_alert) module and adds customizations for the MC Foundational Build.

Includes:
* `RouteSubscriber` that alters the canonical Sitewide Alert route to a full page view rather than the default edit view.
* `AlertsListingController` which displays a listing of all site alerts.
* Preprocessing the hero block (included in `mc_custom`) to format and display alert heroes.
* Preprocessing sitewide alerts for integration with the UI Library.

## Requirements
- [Sitewide Alert](https://www.drupal.org/project/sitewide_alert)
- mc_custom
- Config Page `alerts` for user-managed content on AlertsListingController