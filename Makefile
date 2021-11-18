test:api-all unit-all

unit-all:
	vendor/bin/codecept run -- -c common

unit-coach:
	vendor/bin/codecept run -- common/tests/unit/models/ClientCoachTest.php

unit-training:
	vendor/bin/codecept run -- common/tests/unit/models/TrainingTest.php

api-all:
	vendor/bin/codecept run -- -c api

api-auth:
	vendor/bin/codecept run -- api/tests/rest/AuthTestCest.php

api-client:
	vendor/bin/codecept run -- api/tests/rest/ClientTestCest.php

api-request:
	vendor/bin/codecept run -- api/tests/rest/RequestTestCest.php

api-signup:
	vendor/bin/codecept run -- api/tests/rest/SignUpTestCest.php

api-user:
	vendor/bin/codecept run -- api/tests/rest/UserTestCest.php

api-profile:
	vendor/bin/codecept run -- api/tests/rest/ProfileTestCest.php

api-employee:
	vendor/bin/codecept run -- api/tests/rest/EmployeeTestCest.php

api-mentor:
	vendor/bin/codecept run -- api/tests/rest/MentorTestCest.php

api-coach:
	vendor/bin/codecept run -- api/tests/rest/CoachTestCest.php

api-planning:
	vendor/bin/codecept run -- api/tests/rest/PlanningTestCest.php

api-meeting:
	vendor/bin/codecept run -- api/tests/rest/MeetingTestCest.php

