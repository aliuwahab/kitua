# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer Bearer {YOUR_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

You can retrieve your token by completing the registration/login flow via the `/api/v1/auth/register` and `/api/v1/auth/verify-pin` endpoints.
