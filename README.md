PDN Alerts
================

A simple alerting framework for internal Magento metrics

Current Alerts:

1. Low stock alerts


Notes:

- Checks always run off a CRON job rather than hooking into observers in order to avoid any excessive processing during a clients request.

