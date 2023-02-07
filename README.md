# Installation
* use `app` script in terminal
* `./app start`
* `./app composer install`
* `./app doctrine:schema:create`
* `./app doctrine:schema:create --env=test`

# Testing
* `./app tests`

# Usage
* `./app console company:add`
* `./app console company:salary-bonus-policy:add`
* `./app console company:department:add`
* `./app console employee:add`
* `./app console contract:sign`
* `./app console payroll:generate` then `./app console messenger:consume async`
* `./app console payroll:view`
* `./app stop`
