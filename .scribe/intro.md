# Introduction

REST API for Kitua mobile payment app supporting group payments and individual payment requests

<aside>
    <strong>Base URL</strong>: <code>http://kitua.test</code>
</aside>

    This documentation covers the Kitua Mobile Payment API, which provides endpoints for user authentication, payment accounts, group payments, and individual payment requests.

    <aside>The API uses PIN-based authentication for mobile users and follows RESTful conventions. All responses are in JSON format.</aside>

    ## Authentication
    This API uses Laravel Sanctum tokens for authentication. Most endpoints require authentication except for registration and login endpoints.

    ## Base URL
    All API requests should be made to: `{base_url}/api/v1`

