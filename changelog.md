# v1.0.0

1. Repository has been cleaned up, php-cs-fixer and phpstan has been set in place as well as ide-helper for convenient work.
2. New models and API controllers has been created, API has been switched to follow REST convention for route naming. The authorization has been left intact however it should be rewritten to get rid of sessions so we no longer utilize sessions. (REST APIs are stateless by convention)
3. Repository pattern has been introduced for model fetching to provide testable interface for unit tests.
4. Unit tests have been created for listeners as proof of concept, there was a decision made not to unit tests controllers as they contain little to no logic. Functional tests have been created for them instead.
5. Events and listeners were used for manipulating denormalized data of total car trips and mileage.
6. Form requests were used for validation creational data.
7. No additional database has been setup for this project. Usually I would have gone this way but decided not to modify original docker and spend any more time on optimizing this setup.
