<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Kitua Mobile Payment API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.3.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.3.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authentication" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authentication">
                    <a href="#authentication">Authentication</a>
                </li>
                                    <ul id="tocify-subheader-authentication" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="authentication-POSTapi-v1-auth-register">
                                <a href="#authentication-POSTapi-v1-auth-register">Initiate registration/login - Send PIN via SMS</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-POSTapi-v1-auth-verify-pin">
                                <a href="#authentication-POSTapi-v1-auth-verify-pin">Verify PIN to complete registration/login</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-POSTapi-v1-auth-login">
                                <a href="#authentication-POSTapi-v1-auth-login">Alternative login endpoint (uses same flow as register)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-POSTapi-v1-auth-logout">
                                <a href="#authentication-POSTapi-v1-auth-logout">Logout current device</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-POSTapi-v1-auth-logout-all">
                                <a href="#authentication-POSTapi-v1-auth-logout-all">Logout from all devices</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-GETapi-v1-auth-me">
                                <a href="#authentication-GETapi-v1-auth-me">Get current user profile</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-general" class="tocify-header">
                <li class="tocify-item level-1" data-unique="general">
                    <a href="#general">General</a>
                </li>
                                    <ul id="tocify-subheader-general" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="general-GETapi-v1-countries">
                                <a href="#general-GETapi-v1-countries">Get all countries</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="general-GETapi-v1-countries--id-">
                                <a href="#general-GETapi-v1-countries--id-">Get country by ID</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-health-check" class="tocify-header">
                <li class="tocify-item level-1" data-unique="health-check">
                    <a href="#health-check">Health Check</a>
                </li>
                                    <ul id="tocify-subheader-health-check" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="health-check-GETapi-health">
                                <a href="#health-check-GETapi-health">API Health Check</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-payment-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="payment-requests">
                    <a href="#payment-requests">Payment Requests</a>
                </li>
                                    <ul id="tocify-subheader-payment-requests" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="payment-requests-GETapi-v1-payment-requests">
                                <a href="#payment-requests-GETapi-v1-payment-requests">Get all payment requests</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="payment-requests-POSTapi-v1-payment-requests">
                                <a href="#payment-requests-POSTapi-v1-payment-requests">Create a payment request</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="payment-requests-GETapi-v1-payment-requests--uuid_id-">
                                <a href="#payment-requests-GETapi-v1-payment-requests--uuid_id-">Show a specific payment request</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="payment-requests-DELETEapi-v1-payment-requests--uuid_id-">
                                <a href="#payment-requests-DELETEapi-v1-payment-requests--uuid_id-">Delete payment request</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="payment-requests-PUTapi-v1-payment-requests--uuid_id-">
                                <a href="#payment-requests-PUTapi-v1-payment-requests--uuid_id-">Replace Payment Request</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="payment-requests-PATCHapi-v1-payment-requests--uuid_id-">
                                <a href="#payment-requests-PATCHapi-v1-payment-requests--uuid_id-">Update Payment Request</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="payment-requests-POSTapi-v1-payment-requests--uuid_id--settle">
                                <a href="#payment-requests-POSTapi-v1-payment-requests--uuid_id--settle">Settle a payment request</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-payment-webhooks" class="tocify-header">
                <li class="tocify-item level-1" data-unique="payment-webhooks">
                    <a href="#payment-webhooks">Payment Webhooks</a>
                </li>
                                    <ul id="tocify-subheader-payment-webhooks" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="payment-webhooks-POSTapi-v1-webhooks-payment--provider-">
                                <a href="#payment-webhooks-POSTapi-v1-webhooks-payment--provider-">Handle payment webhook from providers.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="payment-webhooks-POSTapi-v1-webhooks-payment--provider---event-">
                                <a href="#payment-webhooks-POSTapi-v1-webhooks-payment--provider---event-">Handle specific webhook events (if providers support it).</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="payment-webhooks-POSTapi-v1-webhooks-payment--provider--test">
                                <a href="#payment-webhooks-POSTapi-v1-webhooks-payment--provider--test">Test webhook endpoint for development.</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-user-profile" class="tocify-header">
                <li class="tocify-item level-1" data-unique="user-profile">
                    <a href="#user-profile">User Profile</a>
                </li>
                                    <ul id="tocify-subheader-user-profile" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="user-profile-GETapi-v1-users--uuid-">
                                <a href="#user-profile-GETapi-v1-users--uuid-">Get user by UUID (JSON:API endpoint)</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: August 16, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>REST API for Kitua mobile payment app supporting group payments and individual payment requests</p>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code>This documentation covers the Kitua Mobile Payment API, which provides endpoints for user authentication, payment accounts, group payments, and individual payment requests.

&lt;aside&gt;The API uses PIN-based authentication for mobile users and follows RESTful conventions. All responses are in JSON format.&lt;/aside&gt;

## Authentication
This API uses Laravel Sanctum tokens for authentication. Most endpoints require authentication except for registration and login endpoints.

## Base URL
All API requests should be made to: `{base_url}/api/v1`</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>To authenticate requests, include an <strong><code>Authorization</code></strong> header with the value <strong><code>"Bearer Bearer {YOUR_TOKEN}"</code></strong>.</p>
<p>All authenticated endpoints are marked with a <code>requires authentication</code> badge in the documentation below.</p>
<p>You can retrieve your token by completing the registration/login flow via the <code>/api/v1/auth/register</code> and <code>/api/v1/auth/verify-pin</code> endpoints.</p>

        <h1 id="authentication">Authentication</h1>

    

                                <h2 id="authentication-POSTapi-v1-auth-register">Initiate registration/login - Send PIN via SMS</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-auth-register">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/register" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"233244123456\",
    \"first_name\": \"John\",
    \"surname\": \"Doe\",
    \"other_names\": \"Michael\",
    \"provider\": \"MTN\",
    \"device_id\": \"ABC123\",
    \"device_name\": \"John\'s iPhone\",
    \"device_type\": \"android\",
    \"app_version\": \"1.0.0\",
    \"os_version\": \"Android 12\",
    \"device_model\": \"Samsung Galaxy S21\",
    \"screen_resolution\": \"1080x2340\",
    \"push_token\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/register"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "233244123456",
    "first_name": "John",
    "surname": "Doe",
    "other_names": "Michael",
    "provider": "MTN",
    "device_id": "ABC123",
    "device_name": "John's iPhone",
    "device_type": "android",
    "app_version": "1.0.0",
    "os_version": "Android 12",
    "device_model": "Samsung Galaxy S21",
    "screen_resolution": "1080x2340",
    "push_token": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-register">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;registration&quot;,
        &quot;id&quot;: &quot;689f7aae2da9f&quot;,
        &quot;attributes&quot;: {
            &quot;userExists&quot;: false,
            &quot;mobileNumber&quot;: &quot;233244123456&quot;,
            &quot;message&quot;: &quot;Registration PIN sent to your mobile number&quot;,
            &quot;pin&quot;: &quot;250964&quot;
        },
        &quot;links&quot;: {
            &quot;verifyPin&quot;: &quot;http://localhost/api/v1/auth/verify-pin&quot;,
            &quot;login&quot;: &quot;http://localhost/api/v1/auth/login&quot;
        }
    },
    &quot;message&quot;: &quot;Registration PIN sent to your mobile number&quot;,
    &quot;status&quot;: 200
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Validation failed&quot;,
    &quot;errors&quot;: {
        &quot;mobile_number&quot;: [
            &quot;This mobile number is already registered.&quot;
        ]
    },
    &quot;status&quot;: 422
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-auth-register" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-auth-register"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-register"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-register" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-register">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-register" data-method="POST"
      data-path="api/v1/auth/register"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-register', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-register"
                    onclick="tryItOut('POSTapi-v1-auth-register');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-register"
                    onclick="cancelTryOut('POSTapi-v1-auth-register');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-register"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-v1-auth-register"
               value="233244123456"
               data-component="body">
    <br>
<p>User's mobile number. Example: <code>233244123456</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>first_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="first_name"                data-endpoint="POSTapi-v1-auth-register"
               value="John"
               data-component="body">
    <br>
<p>User's first name. Example: <code>John</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>surname</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="surname"                data-endpoint="POSTapi-v1-auth-register"
               value="Doe"
               data-component="body">
    <br>
<p>User's surname. Example: <code>Doe</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>other_names</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="other_names"                data-endpoint="POSTapi-v1-auth-register"
               value="Michael"
               data-component="body">
    <br>
<p>optional User's other names. Example: <code>Michael</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>provider</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="provider"                data-endpoint="POSTapi-v1-auth-register"
               value="MTN"
               data-component="body">
    <br>
<p>optional Mobile money provider. Example: <code>MTN</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_id"                data-endpoint="POSTapi-v1-auth-register"
               value="ABC123"
               data-component="body">
    <br>
<p>Unique device identifier. Example: <code>ABC123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="device_name"                data-endpoint="POSTapi-v1-auth-register"
               value="John's iPhone"
               data-component="body">
    <br>
<p>optional User-friendly device name. Example: <code>John's iPhone</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_type"                data-endpoint="POSTapi-v1-auth-register"
               value="android"
               data-component="body">
    <br>
<p>Device type (android/ios). Example: <code>android</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>app_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="app_version"                data-endpoint="POSTapi-v1-auth-register"
               value="1.0.0"
               data-component="body">
    <br>
<p>optional App version. Example: <code>1.0.0</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>os_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="os_version"                data-endpoint="POSTapi-v1-auth-register"
               value="Android 12"
               data-component="body">
    <br>
<p>optional OS version. Example: <code>Android 12</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_model</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="device_model"                data-endpoint="POSTapi-v1-auth-register"
               value="Samsung Galaxy S21"
               data-component="body">
    <br>
<p>optional Device model. Example: <code>Samsung Galaxy S21</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>screen_resolution</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="screen_resolution"                data-endpoint="POSTapi-v1-auth-register"
               value="1080x2340"
               data-component="body">
    <br>
<p>optional Screen resolution. Example: <code>1080x2340</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>push_token</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="push_token"                data-endpoint="POSTapi-v1-auth-register"
               value="architecto"
               data-component="body">
    <br>
<p>optional Firebase push token for notifications. Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="authentication-POSTapi-v1-auth-verify-pin">Verify PIN to complete registration/login</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-auth-verify-pin">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/verify-pin" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"233244123456\",
    \"pin\": \"123456\",
    \"device_id\": \"ABC123\",
    \"device_name\": \"John\'s iPhone\",
    \"device_type\": \"android\",
    \"app_version\": \"1.0.0\",
    \"os_version\": \"Android 12\",
    \"device_model\": \"Samsung Galaxy S21\",
    \"screen_resolution\": \"1080x2340\",
    \"push_token\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/verify-pin"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "233244123456",
    "pin": "123456",
    "device_id": "ABC123",
    "device_name": "John's iPhone",
    "device_type": "android",
    "app_version": "1.0.0",
    "os_version": "Android 12",
    "device_model": "Samsung Galaxy S21",
    "screen_resolution": "1080x2340",
    "push_token": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-verify-pin">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;authentication&quot;,
        &quot;id&quot;: &quot;0198aef7-7dd4-73f5-b45b-ffa0e2f2cd76&quot;,
        &quot;attributes&quot;: {
            &quot;token&quot;: &quot;1|1U35ymFSmoMn02wdjtQNVALwbJhHw2epENTb7a1Bbeb73e31&quot;,
            &quot;isNewUser&quot;: true,
            &quot;isNewDevice&quot;: true,
            &quot;userExists&quot;: null,
            &quot;mobileNumber&quot;: &quot;233244123456&quot;,
            &quot;message&quot;: null,
            &quot;pin&quot;: null
        },
        &quot;relationships&quot;: {
            &quot;user&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;user&quot;,
                    &quot;id&quot;: &quot;0198aef7-7dd4-73f5-b45b-ffa0e2f2cd76&quot;
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;#&quot;
                }
            }
        },
        &quot;includes&quot;: {
            &quot;user&quot;: {
                &quot;type&quot;: &quot;user&quot;,
                &quot;id&quot;: &quot;0198aef7-7dd4-73f5-b45b-ffa0e2f2cd76&quot;,
                &quot;attributes&quot;: {
                    &quot;mobileNumber&quot;: &quot;233244123456&quot;,
                    &quot;firstName&quot;: &quot;John&quot;,
                    &quot;surname&quot;: &quot;Doe&quot;,
                    &quot;otherNames&quot;: null,
                    &quot;fullName&quot;: &quot;John Doe&quot;,
                    &quot;userType&quot;: &quot;customer&quot;,
                    &quot;isActive&quot;: true,
                    &quot;emailVerifiedAt&quot;: null,
                    &quot;createdAt&quot;: &quot;2025-08-15T18:21:51.000000Z&quot;,
                    &quot;updatedAt&quot;: &quot;2025-08-15T18:21:51.000000Z&quot;
                },
                &quot;relationships&quot;: {
                    &quot;country&quot;: {
                        &quot;data&quot;: {
                            &quot;type&quot;: &quot;country&quot;,
                            &quot;id&quot;: &quot;53c153e8-9d2d-4b60-9844-985e6e2c7db2&quot;
                        },
                        &quot;links&quot;: {
                            &quot;self&quot;: &quot;http://localhost/api/v1/countries/53c153e8-9d2d-4b60-9844-985e6e2c7db2&quot;
                        }
                    },
                    &quot;paymentAccounts&quot;: {
                        &quot;data&quot;: [
                            {
                                &quot;type&quot;: &quot;paymentAccount&quot;,
                                &quot;id&quot;: &quot;0198aef7-7dd6-7097-830d-fabd3845c8b7&quot;
                            }
                        ],
                        &quot;links&quot;: {
                            &quot;related&quot;: &quot;#&quot;
                        }
                    }
                },
                &quot;includes&quot;: {
                    &quot;paymentAccounts&quot;: [
                        {
                            &quot;type&quot;: &quot;paymentAccount&quot;,
                            &quot;id&quot;: &quot;0198aef7-7dd6-7097-830d-fabd3845c8b7&quot;,
                            &quot;attributes&quot;: {
                                &quot;accountType&quot;: &quot;momo&quot;,
                                &quot;accountNumber&quot;: &quot;233244123456&quot;,
                                &quot;accountName&quot;: &quot;John Doe&quot;,
                                &quot;provider&quot;: null,
                                &quot;isPrimary&quot;: true,
                                &quot;isVerified&quot;: false,
                                &quot;isActive&quot;: null,
                                &quot;verifiedAt&quot;: null,
                                &quot;createdAt&quot;: &quot;2025-08-15T18:21:51.000000Z&quot;,
                                &quot;updatedAt&quot;: &quot;2025-08-15T18:21:51.000000Z&quot;
                            },
                            &quot;relationships&quot;: {
                                &quot;user&quot;: {
                                    &quot;data&quot;: {
                                        &quot;type&quot;: &quot;user&quot;,
                                        &quot;id&quot;: &quot;0198aef7-7dd4-73f5-b45b-ffa0e2f2cd76&quot;
                                    },
                                    &quot;links&quot;: {
                                        &quot;self&quot;: &quot;#&quot;
                                    }
                                }
                            },
                            &quot;links&quot;: {
                                &quot;self&quot;: &quot;#&quot;
                            }
                        }
                    ]
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;#&quot;
                }
            }
        },
        &quot;links&quot;: {
            &quot;self&quot;: &quot;#&quot;
        }
    },
    &quot;message&quot;: &quot;Authentication successful&quot;,
    &quot;status&quot;: 200
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Invalid PIN or mobile number&quot;,
    &quot;status&quot;: 422
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-auth-verify-pin" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-auth-verify-pin"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-verify-pin"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-verify-pin" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-verify-pin">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-verify-pin" data-method="POST"
      data-path="api/v1/auth/verify-pin"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-verify-pin', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-verify-pin"
                    onclick="tryItOut('POSTapi-v1-auth-verify-pin');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-verify-pin"
                    onclick="cancelTryOut('POSTapi-v1-auth-verify-pin');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-verify-pin"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/verify-pin</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="233244123456"
               data-component="body">
    <br>
<p>Mobile number used in registration. Example: <code>233244123456</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>pin</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="pin"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="123456"
               data-component="body">
    <br>
<p>6-digit PIN received via SMS. Example: <code>123456</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_id"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="ABC123"
               data-component="body">
    <br>
<p>Unique device identifier. Example: <code>ABC123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="device_name"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="John's iPhone"
               data-component="body">
    <br>
<p>optional User-friendly device name. Example: <code>John's iPhone</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_type"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="android"
               data-component="body">
    <br>
<p>Device type (android/ios). Example: <code>android</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>app_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="app_version"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="1.0.0"
               data-component="body">
    <br>
<p>optional App version. Example: <code>1.0.0</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>os_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="os_version"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="Android 12"
               data-component="body">
    <br>
<p>optional OS version. Example: <code>Android 12</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_model</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="device_model"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="Samsung Galaxy S21"
               data-component="body">
    <br>
<p>optional Device model. Example: <code>Samsung Galaxy S21</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>screen_resolution</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="screen_resolution"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="1080x2340"
               data-component="body">
    <br>
<p>optional Screen resolution. Example: <code>1080x2340</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>push_token</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="push_token"                data-endpoint="POSTapi-v1-auth-verify-pin"
               value="architecto"
               data-component="body">
    <br>
<p>optional Firebase push token for notifications. Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="authentication-POSTapi-v1-auth-login">Alternative login endpoint (uses same flow as register)</h2>

<p>
</p>



<span id="example-requests-POSTapi-v1-auth-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"mobile_number\": \"233244123456\",
    \"pin\": \"123456\",
    \"device_id\": \"ABC123\",
    \"device_name\": \"John\'s iPhone\",
    \"device_type\": \"android\",
    \"app_version\": \"1.0.0\",
    \"os_version\": \"Android 12\",
    \"device_model\": \"Samsung Galaxy S21\",
    \"screen_resolution\": \"1080x2340\",
    \"push_token\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "mobile_number": "233244123456",
    "pin": "123456",
    "device_id": "ABC123",
    "device_name": "John's iPhone",
    "device_type": "android",
    "app_version": "1.0.0",
    "os_version": "Android 12",
    "device_model": "Samsung Galaxy S21",
    "screen_resolution": "1080x2340",
    "push_token": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-login">
            <blockquote>
            <p>Example response (200, Login successful):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;authentication&quot;,
        &quot;id&quot;: 1,
        &quot;attributes&quot;: {
            &quot;token&quot;: &quot;1|xyz789token123&quot;,
            &quot;isNewUser&quot;: false,
            &quot;isNewDevice&quot;: false,
            &quot;userExists&quot;: null,
            &quot;mobileNumber&quot;: &quot;233244123456&quot;,
            &quot;message&quot;: null,
            &quot;pin&quot;: null
        },
        &quot;relationships&quot;: {
            &quot;user&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;user&quot;,
                    &quot;id&quot;: 1
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;/api/v1/users/1&quot;
                }
            }
        },
        &quot;includes&quot;: {
            &quot;user&quot;: {
                &quot;type&quot;: &quot;user&quot;,
                &quot;id&quot;: 1,
                &quot;attributes&quot;: {
                    &quot;mobileNumber&quot;: &quot;233244123456&quot;,
                    &quot;firstName&quot;: &quot;John&quot;,
                    &quot;surname&quot;: &quot;Doe&quot;,
                    &quot;fullName&quot;: &quot;John Doe&quot;,
                    &quot;userType&quot;: &quot;customer&quot;,
                    &quot;isActive&quot;: true
                }
            }
        },
        &quot;links&quot;: {
            &quot;self&quot;: &quot;/api/v1/users/1&quot;
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Invalid credentials):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Invalid PIN or mobile number&quot;,
    &quot;status&quot;: 422
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-auth-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-auth-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-login" data-method="POST"
      data-path="api/v1/auth/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-login"
                    onclick="tryItOut('POSTapi-v1-auth-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-login"
                    onclick="cancelTryOut('POSTapi-v1-auth-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>mobile_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="mobile_number"                data-endpoint="POSTapi-v1-auth-login"
               value="233244123456"
               data-component="body">
    <br>
<p>Mobile number used for login. Example: <code>233244123456</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>pin</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="pin"                data-endpoint="POSTapi-v1-auth-login"
               value="123456"
               data-component="body">
    <br>
<p>6-digit PIN for authentication. Example: <code>123456</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_id"                data-endpoint="POSTapi-v1-auth-login"
               value="ABC123"
               data-component="body">
    <br>
<p>Unique device identifier. Example: <code>ABC123</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="device_name"                data-endpoint="POSTapi-v1-auth-login"
               value="John's iPhone"
               data-component="body">
    <br>
<p>optional User-friendly device name. Example: <code>John's iPhone</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="device_type"                data-endpoint="POSTapi-v1-auth-login"
               value="android"
               data-component="body">
    <br>
<p>Device type (android/ios). Example: <code>android</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>app_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="app_version"                data-endpoint="POSTapi-v1-auth-login"
               value="1.0.0"
               data-component="body">
    <br>
<p>optional App version. Example: <code>1.0.0</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>os_version</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="os_version"                data-endpoint="POSTapi-v1-auth-login"
               value="Android 12"
               data-component="body">
    <br>
<p>optional OS version. Example: <code>Android 12</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>device_model</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="device_model"                data-endpoint="POSTapi-v1-auth-login"
               value="Samsung Galaxy S21"
               data-component="body">
    <br>
<p>optional Device model. Example: <code>Samsung Galaxy S21</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>screen_resolution</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="screen_resolution"                data-endpoint="POSTapi-v1-auth-login"
               value="1080x2340"
               data-component="body">
    <br>
<p>optional Screen resolution. Example: <code>1080x2340</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>push_token</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="push_token"                data-endpoint="POSTapi-v1-auth-login"
               value="architecto"
               data-component="body">
    <br>
<p>optional Firebase push token for notifications. Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="authentication-POSTapi-v1-auth-logout">Logout current device</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-v1-auth-logout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/logout" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/logout"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-logout">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;logout&quot;,
        &quot;id&quot;: &quot;66c8f91a8f4d3&quot;,
        &quot;attributes&quot;: {
            &quot;reason&quot;: &quot;user_initiated&quot;,
            &quot;loggedOutAt&quot;: &quot;2025-08-15T08:45:00Z&quot;,
            &quot;deviceSessionsCount&quot;: null,
            &quot;message&quot;: &quot;Logged out successfully&quot;
        },
        &quot;relationships&quot;: {
            &quot;user&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;user&quot;,
                    &quot;id&quot;: 1
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;/api/v1/users/1&quot;
                }
            }
        },
        &quot;includes&quot;: {
            &quot;user&quot;: {
                &quot;type&quot;: &quot;user&quot;,
                &quot;id&quot;: 1,
                &quot;attributes&quot;: {
                    &quot;mobileNumber&quot;: &quot;233244123456&quot;,
                    &quot;firstName&quot;: &quot;John&quot;
                }
            }
        }
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-auth-logout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-auth-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-logout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-logout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-logout" data-method="POST"
      data-path="api/v1/auth/logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-logout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-logout"
                    onclick="tryItOut('POSTapi-v1-auth-logout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-logout"
                    onclick="cancelTryOut('POSTapi-v1-auth-logout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-logout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-auth-logout"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="authentication-POSTapi-v1-auth-logout-all">Logout from all devices</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-POSTapi-v1-auth-logout-all">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/auth/logout-all" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/logout-all"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-auth-logout-all">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;logout&quot;,
        &quot;id&quot;: &quot;66c8f91a8f4d3&quot;,
        &quot;attributes&quot;: {
            &quot;reason&quot;: &quot;user_initiated&quot;,
            &quot;loggedOutAt&quot;: &quot;2025-08-15T08:45:00Z&quot;,
            &quot;deviceSessionsCount&quot;: 3,
            &quot;message&quot;: &quot;Logged out from all devices&quot;
        },
        &quot;relationships&quot;: {
            &quot;user&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;user&quot;,
                    &quot;id&quot;: 1
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;/api/v1/users/1&quot;
                }
            }
        },
        &quot;includes&quot;: {
            &quot;user&quot;: {
                &quot;type&quot;: &quot;user&quot;,
                &quot;id&quot;: 1,
                &quot;attributes&quot;: {
                    &quot;mobileNumber&quot;: &quot;233244123456&quot;
                }
            }
        }
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-auth-logout-all" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-auth-logout-all"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-auth-logout-all"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-auth-logout-all" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-auth-logout-all">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-auth-logout-all" data-method="POST"
      data-path="api/v1/auth/logout-all"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-auth-logout-all', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-auth-logout-all"
                    onclick="tryItOut('POSTapi-v1-auth-logout-all');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-auth-logout-all"
                    onclick="cancelTryOut('POSTapi-v1-auth-logout-all');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-auth-logout-all"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/auth/logout-all</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-auth-logout-all"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-auth-logout-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-auth-logout-all"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="authentication-GETapi-v1-auth-me">Get current user profile</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-v1-auth-me">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/auth/me" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/auth/me"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-auth-me">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;user&quot;,
        &quot;id&quot;: &quot;0198aef7-c446-7064-aa5b-f97eec114ba2&quot;,
        &quot;attributes&quot;: {
            &quot;mobileNumber&quot;: &quot;233337138993&quot;,
            &quot;firstName&quot;: &quot;Camren&quot;,
            &quot;surname&quot;: &quot;Simonis&quot;,
            &quot;otherNames&quot;: &quot;Jarrell&quot;,
            &quot;fullName&quot;: &quot;Camren Simonis Jarrell&quot;,
            &quot;userType&quot;: &quot;customer&quot;,
            &quot;isActive&quot;: true,
            &quot;emailVerifiedAt&quot;: null,
            &quot;createdAt&quot;: &quot;2025-08-15T18:22:09.000000Z&quot;,
            &quot;updatedAt&quot;: &quot;2025-08-15T18:22:09.000000Z&quot;
        },
        &quot;relationships&quot;: {
            &quot;country&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;country&quot;,
                    &quot;id&quot;: &quot;cdcd6ddb-7bd2-4775-a1c4-0d565510c79f&quot;
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;http://localhost/api/v1/countries/cdcd6ddb-7bd2-4775-a1c4-0d565510c79f&quot;
                }
            },
            &quot;paymentAccounts&quot;: {
                &quot;data&quot;: [],
                &quot;links&quot;: {
                    &quot;related&quot;: &quot;#&quot;
                }
            }
        },
        &quot;includes&quot;: {
            &quot;country&quot;: {
                &quot;type&quot;: &quot;country&quot;,
                &quot;id&quot;: &quot;cdcd6ddb-7bd2-4775-a1c4-0d565510c79f&quot;,
                &quot;attributes&quot;: {
                    &quot;name&quot;: &quot;Ghana&quot;,
                    &quot;code&quot;: &quot;GH&quot;,
                    &quot;dialingCode&quot;: null,
                    &quot;currencyCode&quot;: &quot;GHS&quot;,
                    &quot;currencySymbol&quot;: &quot;GH₵&quot;,
                    &quot;currencyName&quot;: &quot;Ghana Cedi&quot;,
                    &quot;flag&quot;: null,
                    &quot;isActive&quot;: true,
                    &quot;createdAt&quot;: &quot;2025-08-15T18:22:09.000000Z&quot;,
                    &quot;updatedAt&quot;: &quot;2025-08-15T18:22:09.000000Z&quot;
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;http://localhost/api/v1/countries/cdcd6ddb-7bd2-4775-a1c4-0d565510c79f&quot;
                }
            },
            &quot;paymentAccounts&quot;: []
        },
        &quot;links&quot;: {
            &quot;self&quot;: &quot;#&quot;
        }
    },
    &quot;message&quot;: &quot;User profile retrieved successfully&quot;,
    &quot;status&quot;: 200
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-auth-me" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-auth-me"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-auth-me"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-auth-me" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-auth-me">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-auth-me" data-method="GET"
      data-path="api/v1/auth/me"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-auth-me', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-auth-me"
                    onclick="tryItOut('GETapi-v1-auth-me');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-auth-me"
                    onclick="cancelTryOut('GETapi-v1-auth-me');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-auth-me"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/auth/me</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-auth-me"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-auth-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-auth-me"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="general">General</h1>

    

                                <h2 id="general-GETapi-v1-countries">Get all countries</h2>

<p>
</p>

<p>Retrieve a list of all available countries with their details including currency information.
Only active countries are returned by default.</p>

<span id="example-requests-GETapi-v1-countries">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/countries?active=1&amp;search=Ghana" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/countries"
);

const params = {
    "active": "1",
    "search": "Ghana",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-countries">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;type&quot;: &quot;country&quot;,
            &quot;id&quot;: &quot;9d2f8b10-1234-4567-8901-123456789abc&quot;,
            &quot;attributes&quot;: {
                &quot;name&quot;: &quot;Ghana&quot;,
                &quot;code&quot;: &quot;GH&quot;,
                &quot;currencyCode&quot;: &quot;GHS&quot;,
                &quot;currencySymbol&quot;: &quot;GH₵&quot;,
                &quot;currencyName&quot;: &quot;Ghana Cedi&quot;,
                &quot;isActive&quot;: true,
                &quot;createdAt&quot;: &quot;2025-08-15T10:15:30.000Z&quot;,
                &quot;updatedAt&quot;: &quot;2025-08-15T10:15:30.000Z&quot;
            },
            &quot;links&quot;: {
                &quot;self&quot;: &quot;http://localhost/api/v1/countries/9d2f8b10-1234-4567-8901-123456789abc&quot;
            }
        },
        {
            &quot;type&quot;: &quot;country&quot;,
            &quot;id&quot;: &quot;9d2f8b10-1234-4567-8901-123456789def&quot;,
            &quot;attributes&quot;: {
                &quot;name&quot;: &quot;Nigeria&quot;,
                &quot;code&quot;: &quot;NG&quot;,
                &quot;currencyCode&quot;: &quot;NGN&quot;,
                &quot;currencySymbol&quot;: &quot;₦&quot;,
                &quot;currencyName&quot;: &quot;Nigerian Naira&quot;,
                &quot;isActive&quot;: true,
                &quot;createdAt&quot;: &quot;2025-08-15T10:15:30.000Z&quot;,
                &quot;updatedAt&quot;: &quot;2025-08-15T10:15:30.000Z&quot;
            },
            &quot;links&quot;: {
                &quot;self&quot;: &quot;http://localhost/api/v1/countries/9d2f8b10-1234-4567-8901-123456789def&quot;
            }
        }
    ],
    &quot;meta&quot;: {
        &quot;total&quot;: 2,
        &quot;count&quot;: 2
    },
    &quot;links&quot;: {
        &quot;self&quot;: &quot;http://localhost/api/v1/countries&quot;
    },
    &quot;message&quot;: &quot;Countries retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200, Empty result):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [],
    &quot;meta&quot;: {
        &quot;total&quot;: 0,
        &quot;count&quot;: 0
    },
    &quot;links&quot;: {
        &quot;self&quot;: &quot;http://localhost/api/v1/countries&quot;
    },
    &quot;message&quot;: &quot;Countries retrieved successfully&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-countries" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-countries"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-countries"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-countries" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-countries">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-countries" data-method="GET"
      data-path="api/v1/countries"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-countries', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-countries"
                    onclick="tryItOut('GETapi-v1-countries');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-countries"
                    onclick="cancelTryOut('GETapi-v1-countries');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-countries"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/countries</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-countries"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-countries"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>active</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="GETapi-v1-countries" style="display: none">
            <input type="radio" name="active"
                   value="1"
                   data-endpoint="GETapi-v1-countries"
                   data-component="query"             >
            <code>true</code>
        </label>
        <label data-endpoint="GETapi-v1-countries" style="display: none">
            <input type="radio" name="active"
                   value="0"
                   data-endpoint="GETapi-v1-countries"
                   data-component="query"             >
            <code>false</code>
        </label>
    <br>
<p>Filter by active status. Default: true. Example: <code>true</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>search</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="search"                data-endpoint="GETapi-v1-countries"
               value="Ghana"
               data-component="query">
    <br>
<p>Search countries by name or code. Example: <code>Ghana</code></p>
            </div>
                </form>

                    <h2 id="general-GETapi-v1-countries--id-">Get country by ID</h2>

<p>
</p>

<p>Retrieve detailed information about a specific country by its UUID.</p>

<span id="example-requests-GETapi-v1-countries--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/countries/3359e0e1-e154-41f8-b0c1-b976eb7cb467" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/countries/3359e0e1-e154-41f8-b0c1-b976eb7cb467"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-countries--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;country&quot;,
        &quot;id&quot;: &quot;9d2f8b10-1234-4567-8901-123456789abc&quot;,
        &quot;attributes&quot;: {
            &quot;name&quot;: &quot;Ghana&quot;,
            &quot;code&quot;: &quot;GH&quot;,
            &quot;currencyCode&quot;: &quot;GHS&quot;,
            &quot;currencySymbol&quot;: &quot;GH₵&quot;,
            &quot;currencyName&quot;: &quot;Ghana Cedi&quot;,
            &quot;isActive&quot;: true,
            &quot;createdAt&quot;: &quot;2025-08-15T10:15:30.000Z&quot;,
            &quot;updatedAt&quot;: &quot;2025-08-15T10:15:30.000Z&quot;
        },
        &quot;links&quot;: {
            &quot;self&quot;: &quot;http://localhost/api/v1/countries/9d2f8b10-1234-4567-8901-123456789abc&quot;
        }
    },
    &quot;message&quot;: &quot;Country retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Country not found&quot;,
    &quot;errors&quot;: {
        &quot;country&quot;: [
            &quot;The specified country could not be found.&quot;
        ]
    },
    &quot;status&quot;: &quot;error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-countries--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-countries--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-countries--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-countries--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-countries--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-countries--id-" data-method="GET"
      data-path="api/v1/countries/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-countries--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-countries--id-"
                    onclick="tryItOut('GETapi-v1-countries--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-countries--id-"
                    onclick="cancelTryOut('GETapi-v1-countries--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-countries--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/countries/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-countries--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-countries--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-v1-countries--id-"
               value="3359e0e1-e154-41f8-b0c1-b976eb7cb467"
               data-component="url">
    <br>
<p>The ID of the country. Example: <code>3359e0e1-e154-41f8-b0c1-b976eb7cb467</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>country</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="country"                data-endpoint="GETapi-v1-countries--id-"
               value="9d2f8b10-1234-4567-8901-123456789abc"
               data-component="url">
    <br>
<p>The UUID of the country. Example: <code>9d2f8b10-1234-4567-8901-123456789abc</code></p>
            </div>
                    </form>

                <h1 id="health-check">Health Check</h1>

    

                                <h2 id="health-check-GETapi-health">API Health Check</h2>

<p>
</p>

<p>Check the health status of the API service.</p>

<span id="example-requests-GETapi-health">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/health" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/health"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-health">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;ok&quot;,
    &quot;service&quot;: &quot;Kitua API&quot;,
    &quot;version&quot;: &quot;1.0.0&quot;,
    &quot;timestamp&quot;: &quot;2025-08-15T10:15:30Z&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-health" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-health"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-health"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-health" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-health">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-health" data-method="GET"
      data-path="api/health"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-health', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-health"
                    onclick="tryItOut('GETapi-health');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-health"
                    onclick="cancelTryOut('GETapi-health');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-health"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/health</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-health"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-health"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="payment-requests">Payment Requests</h1>

    

                                <h2 id="payment-requests-GETapi-v1-payment-requests">Get all payment requests</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-v1-payment-requests">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/payment-requests?sort=sort%3Damount%2C-created_at&amp;filter%5Bstatus%5D=pending&amp;filter%5Bamount%5D%5Bgte%5D=100&amp;filter%5Bamount%5D%5Blte%5D=500&amp;filter%5Bpurpose%5D=%2Alunch%2A&amp;filter%5Bcreated_at%5D%5Bgte%5D=2025-01-01&amp;include=author&amp;fields%5BpaymentRequest%5D=amount%2Cpurpose%2Cstatus&amp;per_page=10&amp;page=1" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/payment-requests"
);

const params = {
    "sort": "sort=amount,-created_at",
    "filter[status]": "pending",
    "filter[amount][gte]": "100",
    "filter[amount][lte]": "500",
    "filter[purpose]": "*lunch*",
    "filter[created_at][gte]": "2025-01-01",
    "include": "author",
    "fields[paymentRequest]": "amount,purpose,status",
    "per_page": "10",
    "page": "1",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-payment-requests">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;type&quot;: &quot;paymentRequest&quot;,
            &quot;id&quot;: &quot;f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;,
            &quot;attributes&quot;: {
                &quot;amount&quot;: &quot;150.00&quot;,
                &quot;formattedAmount&quot;: &quot;GHS 150.00&quot;,
                &quot;currencyCode&quot;: &quot;GHS&quot;,
                &quot;purpose&quot;: &quot;Lunch payment&quot;,
                &quot;isNegotiable&quot;: false,
                &quot;status&quot;: &quot;pending&quot;,
                &quot;expiresAt&quot;: &quot;2025-09-15T12:00:00Z&quot;,
                &quot;paidAt&quot;: null,
                &quot;isExpired&quot;: false,
                &quot;createdAt&quot;: &quot;2025-08-01T10:15:30Z&quot;,
                &quot;updatedAt&quot;: &quot;2025-08-01T10:15:30Z&quot;
            },
            &quot;relationships&quot;: {
                &quot;author&quot;: {
                    &quot;data&quot;: {
                        &quot;type&quot;: &quot;user&quot;,
                        &quot;id&quot;: 1
                    },
                    &quot;links&quot;: {
                        &quot;self&quot;: &quot;/api/v1/users/1&quot;
                    }
                }
            },
            &quot;links&quot;: {
                &quot;self&quot;: &quot;/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;
            }
        },
        {
            &quot;type&quot;: &quot;paymentRequest&quot;,
            &quot;id&quot;: &quot;a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11&quot;,
            &quot;attributes&quot;: {
                &quot;amount&quot;: &quot;200.00&quot;,
                &quot;formattedAmount&quot;: &quot;GHS 200.00&quot;,
                &quot;currencyCode&quot;: &quot;GHS&quot;,
                &quot;purpose&quot;: &quot;Office supplies&quot;,
                &quot;isNegotiable&quot;: false,
                &quot;status&quot;: &quot;paid&quot;,
                &quot;expiresAt&quot;: null,
                &quot;paidAt&quot;: &quot;2025-08-01T09:20:15Z&quot;,
                &quot;isExpired&quot;: false,
                &quot;createdAt&quot;: &quot;2025-07-28T14:30:45Z&quot;,
                &quot;updatedAt&quot;: &quot;2025-08-01T09:20:15Z&quot;
            },
            &quot;relationships&quot;: {
                &quot;author&quot;: {
                    &quot;data&quot;: {
                        &quot;type&quot;: &quot;user&quot;,
                        &quot;id&quot;: 2
                    },
                    &quot;links&quot;: {
                        &quot;self&quot;: &quot;/api/v1/users/2&quot;
                    }
                }
            },
            &quot;links&quot;: {
                &quot;self&quot;: &quot;/api/v1/payment-requests/a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11&quot;
            }
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;https://kitua.com/api/v1/payment-requests?page=1&quot;,
        &quot;last&quot;: &quot;https://kitua.com/api/v1/payment-requests?page=3&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: &quot;https://kitua.com/api/v1/payment-requests?page=2&quot;
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 3,
        &quot;path&quot;: &quot;https://kitua.com/api/v1/payment-requests&quot;,
        &quot;per_page&quot;: 15,
        &quot;to&quot;: 15,
        &quot;total&quot;: 35
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401, Unauthenticated):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;,
    &quot;status&quot;: 401
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-payment-requests" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-payment-requests"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-payment-requests"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-payment-requests" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-payment-requests">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-payment-requests" data-method="GET"
      data-path="api/v1/payment-requests"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-payment-requests', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-payment-requests"
                    onclick="tryItOut('GETapi-v1-payment-requests');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-payment-requests"
                    onclick="cancelTryOut('GETapi-v1-payment-requests');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-payment-requests"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/payment-requests</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-payment-requests"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-payment-requests"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-payment-requests"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="sort"                data-endpoint="GETapi-v1-payment-requests"
               value="sort=amount,-created_at"
               data-component="query">
    <br>
<p>Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: <code>sort=amount,-created_at</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter[status]</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="filter[status]"                data-endpoint="GETapi-v1-payment-requests"
               value="pending"
               data-component="query">
    <br>
<p>Filter by status: pending, paid, cancelled, expired. Example: <code>pending</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter[amount][gte]</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="filter[amount][gte]"                data-endpoint="GETapi-v1-payment-requests"
               value="100"
               data-component="query">
    <br>
<p>numeric Filter by minimum amount. Example: <code>100</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter[amount][lte]</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="filter[amount][lte]"                data-endpoint="GETapi-v1-payment-requests"
               value="500"
               data-component="query">
    <br>
<p>numeric Filter by maximum amount. Example: <code>500</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter[purpose]</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="filter[purpose]"                data-endpoint="GETapi-v1-payment-requests"
               value="*lunch*"
               data-component="query">
    <br>
<p>Filter by purpose. Wildcards are supported. Example: <code>*lunch*</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filter[created_at][gte]</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="filter[created_at][gte]"                data-endpoint="GETapi-v1-payment-requests"
               value="2025-01-01"
               data-component="query">
    <br>
<p>date Filter by minimum creation date. Example: <code>2025-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>include</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="include"                data-endpoint="GETapi-v1-payment-requests"
               value="author"
               data-component="query">
    <br>
<p>Include related resources. Available: author,recipient. Example: <code>author</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>fields[paymentRequest]</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="fields[paymentRequest]"                data-endpoint="GETapi-v1-payment-requests"
               value="amount,purpose,status"
               data-component="query">
    <br>
<p>Comma-separated list of fields to include. Example: <code>amount,purpose,status</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-v1-payment-requests"
               value="10"
               data-component="query">
    <br>
<p>Number of results per page. Default is 15. Example: <code>10</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-v1-payment-requests"
               value="1"
               data-component="query">
    <br>
<p>Page number. Default is 1. Example: <code>1</code></p>
            </div>
                </form>

                    <h2 id="payment-requests-POSTapi-v1-payment-requests">Create a payment request</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Creates a new payment request record. Users can only create payment requests for themselves.</p>

<span id="example-requests-POSTapi-v1-payment-requests">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/payment-requests" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"amount\": \"150\",
    \"currency_code\": \"bng\",
    \"purpose\": \"Lunch payment\",
    \"description\": \"Payment for team lunch at the cafeteria\",
    \"is_negotiable\": false,
    \"status\": \"cancelled\",
    \"expires_at\": \"2025-09-15T12:00:00Z\",
    \"metadata\": {
        \"restaurant\": \"Cafe Royal\",
        \"receipt_number\": \"RCT-12345\"
    },
    \"negotiable\": true
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/payment-requests"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "amount": "150",
    "currency_code": "bng",
    "purpose": "Lunch payment",
    "description": "Payment for team lunch at the cafeteria",
    "is_negotiable": false,
    "status": "cancelled",
    "expires_at": "2025-09-15T12:00:00Z",
    "metadata": {
        "restaurant": "Cafe Royal",
        "receipt_number": "RCT-12345"
    },
    "negotiable": true
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-payment-requests">
            <blockquote>
            <p>Example response (201, Created successfully):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;paymentRequest&quot;,
        &quot;id&quot;: &quot;f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;,
        &quot;attributes&quot;: {
            &quot;amount&quot;: &quot;150.00&quot;,
            &quot;formattedAmount&quot;: &quot;GHS 150.00&quot;,
            &quot;currencyCode&quot;: &quot;GHS&quot;,
            &quot;purpose&quot;: &quot;Lunch payment&quot;,
            &quot;description&quot;: &quot;Payment for team lunch at the cafeteria&quot;,
            &quot;isNegotiable&quot;: false,
            &quot;status&quot;: &quot;pending&quot;,
            &quot;expiresAt&quot;: &quot;2025-09-15T12:00:00Z&quot;,
            &quot;paidAt&quot;: null,
            &quot;isExpired&quot;: false,
            &quot;metadata&quot;: {
                &quot;restaurant&quot;: &quot;Cafe Royal&quot;,
                &quot;receipt_number&quot;: &quot;RCT-12345&quot;
            },
            &quot;createdAt&quot;: &quot;2025-08-15T10:15:30Z&quot;,
            &quot;updatedAt&quot;: &quot;2025-08-15T10:15:30Z&quot;
        },
        &quot;relationships&quot;: {
            &quot;author&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;user&quot;,
                    &quot;id&quot;: 1
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;/api/v1/users/1&quot;
                }
            }
        },
        &quot;links&quot;: {
            &quot;self&quot;: &quot;/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401, Unauthenticated):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;,
    &quot;status&quot;: 401
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validation error):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Validation failed&quot;,
    &quot;errors&quot;: {
        &quot;amount&quot;: [
            &quot;The amount field is required.&quot;
        ],
        &quot;purpose&quot;: [
            &quot;The purpose field is required.&quot;
        ]
    },
    &quot;status&quot;: 422
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-payment-requests" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-payment-requests"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-payment-requests"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-payment-requests" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-payment-requests">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-payment-requests" data-method="POST"
      data-path="api/v1/payment-requests"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-payment-requests', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-payment-requests"
                    onclick="tryItOut('POSTapi-v1-payment-requests');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-payment-requests"
                    onclick="cancelTryOut('POSTapi-v1-payment-requests');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-payment-requests"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/payment-requests</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-payment-requests"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-payment-requests"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-payment-requests"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>numeric</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="amount"                data-endpoint="POSTapi-v1-payment-requests"
               value="150"
               data-component="body">
    <br>
<p>The amount of the payment request. Example: <code>150</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>currency_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="currency_code"                data-endpoint="POSTapi-v1-payment-requests"
               value="bng"
               data-component="body">
    <br>
<p>Must be 3 characters. Example: <code>bng</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>purpose</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="purpose"                data-endpoint="POSTapi-v1-payment-requests"
               value="Lunch payment"
               data-component="body">
    <br>
<p>The purpose of the payment request (max 100 chars). Example: <code>Lunch payment</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="POSTapi-v1-payment-requests"
               value="Payment for team lunch at the cafeteria"
               data-component="body">
    <br>
<p>optional A longer description of the payment request. Example: <code>Payment for team lunch at the cafeteria</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>is_negotiable</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="POSTapi-v1-payment-requests" style="display: none">
            <input type="radio" name="is_negotiable"
                   value="true"
                   data-endpoint="POSTapi-v1-payment-requests"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-v1-payment-requests" style="display: none">
            <input type="radio" name="is_negotiable"
                   value="false"
                   data-endpoint="POSTapi-v1-payment-requests"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-v1-payment-requests"
               value="cancelled"
               data-component="body">
    <br>
<p>Example: <code>cancelled</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>pending</code></li> <li><code>paid</code></li> <li><code>cancelled</code></li> <li><code>expired</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>expires_at</code></b>&nbsp;&nbsp;
<small>datetime</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="expires_at"                data-endpoint="POSTapi-v1-payment-requests"
               value="2025-09-15T12:00:00Z"
               data-component="body">
    <br>
<p>optional The date and time when the payment request expires. If not provided, defaults to 30 days from creation. Example: <code>2025-09-15T12:00:00Z</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>metadata</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="metadata"                data-endpoint="POSTapi-v1-payment-requests"
               value=""
               data-component="body">
    <br>
<p>optional Additional metadata for the payment request.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>image</code></b>&nbsp;&nbsp;
<small>file</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="file" style="display: none"
                              name="image"                data-endpoint="POSTapi-v1-payment-requests"
               value=""
               data-component="body">
    <br>
<p>optional An image to attach to the payment request (jpg, png, pdf). Maximum size: 5MB.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>negotiable</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="POSTapi-v1-payment-requests" style="display: none">
            <input type="radio" name="negotiable"
                   value="true"
                   data-endpoint="POSTapi-v1-payment-requests"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-v1-payment-requests" style="display: none">
            <input type="radio" name="negotiable"
                   value="false"
                   data-endpoint="POSTapi-v1-payment-requests"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>optional Whether the amount is negotiable. Default is false. Example: <code>true</code></p>
        </div>
        </form>

                    <h2 id="payment-requests-GETapi-v1-payment-requests--uuid_id-">Show a specific payment request</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Display an individual payment request by its UUID. Users can only view their own payment requests.</p>

<span id="example-requests-GETapi-v1-payment-requests--uuid_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329?include=author" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329"
);

const params = {
    "include": "author",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-payment-requests--uuid_id-">
            <blockquote>
            <p>Example response (200, Success):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;paymentRequest&quot;,
        &quot;id&quot;: &quot;f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;,
        &quot;attributes&quot;: {
            &quot;amount&quot;: &quot;150.00&quot;,
            &quot;formattedAmount&quot;: &quot;GHS 150.00&quot;,
            &quot;currencyCode&quot;: &quot;GHS&quot;,
            &quot;purpose&quot;: &quot;Lunch payment&quot;,
            &quot;description&quot;: &quot;Payment for team lunch&quot;,
            &quot;isNegotiable&quot;: false,
            &quot;status&quot;: &quot;pending&quot;,
            &quot;expiresAt&quot;: &quot;2025-09-15T12:00:00Z&quot;,
            &quot;paidAt&quot;: null,
            &quot;isExpired&quot;: false,
            &quot;metadata&quot;: {
                &quot;restaurant&quot;: &quot;Cafe Royal&quot;,
                &quot;receipt_number&quot;: &quot;RCT-12345&quot;
            },
            &quot;createdAt&quot;: &quot;2025-08-01T10:15:30Z&quot;,
            &quot;updatedAt&quot;: &quot;2025-08-01T10:15:30Z&quot;
        },
        &quot;relationships&quot;: {
            &quot;author&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;user&quot;,
                    &quot;id&quot;: 1
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;/api/v1/users/1&quot;
                }
            }
        },
        &quot;links&quot;: {
            &quot;self&quot;: &quot;/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401, Unauthenticated):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;,
    &quot;status&quot;: 401
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Payment request not found&quot;,
    &quot;status&quot;: 404
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-payment-requests--uuid_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-payment-requests--uuid_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-payment-requests--uuid_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-payment-requests--uuid_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-payment-requests--uuid_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-payment-requests--uuid_id-" data-method="GET"
      data-path="api/v1/payment-requests/{uuid_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-payment-requests--uuid_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-payment-requests--uuid_id-"
                    onclick="tryItOut('GETapi-v1-payment-requests--uuid_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-payment-requests--uuid_id-"
                    onclick="cancelTryOut('GETapi-v1-payment-requests--uuid_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-payment-requests--uuid_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/payment-requests/{uuid_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-payment-requests--uuid_id-"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-payment-requests--uuid_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-payment-requests--uuid_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid_id"                data-endpoint="GETapi-v1-payment-requests--uuid_id-"
               value="0198b21f-6d55-73d3-8c88-a05996bba329"
               data-component="url">
    <br>
<p>The ID of the uuid. Example: <code>0198b21f-6d55-73d3-8c88-a05996bba329</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid"                data-endpoint="GETapi-v1-payment-requests--uuid_id-"
               value="f47ac10b-58cc-4372-a567-0e02b2c3d479"
               data-component="url">
    <br>
<p>The UUID of the payment request. Example: <code>f47ac10b-58cc-4372-a567-0e02b2c3d479</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>include</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="include"                data-endpoint="GETapi-v1-payment-requests--uuid_id-"
               value="author"
               data-component="query">
    <br>
<p>Include related resources. Available: author. Example: <code>author</code></p>
            </div>
                </form>

                    <h2 id="payment-requests-DELETEapi-v1-payment-requests--uuid_id-">Delete payment request</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Remove the specified payment request from storage. Users can only delete their own payment requests,
and paid payment requests cannot be deleted.</p>

<span id="example-requests-DELETEapi-v1-payment-requests--uuid_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-v1-payment-requests--uuid_id-">
            <blockquote>
            <p>Example response (200, Deleted successfully):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;deleted_at&quot;: &quot;2025-08-15T13:45:30Z&quot;
    },
    &quot;message&quot;: &quot;Payment request deleted successfully&quot;,
    &quot;status&quot;: 200
}</code>
 </pre>
            <blockquote>
            <p>Example response (401, Unauthenticated):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;,
    &quot;status&quot;: 401
}</code>
 </pre>
            <blockquote>
            <p>Example response (403, Paid payment request):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Failed to delete payment request: Cannot delete a paid payment request&quot;,
    &quot;status&quot;: 403
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Payment request not found&quot;,
    &quot;status&quot;: 404
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-v1-payment-requests--uuid_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-v1-payment-requests--uuid_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-v1-payment-requests--uuid_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-v1-payment-requests--uuid_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-v1-payment-requests--uuid_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-v1-payment-requests--uuid_id-" data-method="DELETE"
      data-path="api/v1/payment-requests/{uuid_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-v1-payment-requests--uuid_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-v1-payment-requests--uuid_id-"
                    onclick="tryItOut('DELETEapi-v1-payment-requests--uuid_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-v1-payment-requests--uuid_id-"
                    onclick="cancelTryOut('DELETEapi-v1-payment-requests--uuid_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-v1-payment-requests--uuid_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/v1/payment-requests/{uuid_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="DELETEapi-v1-payment-requests--uuid_id-"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-v1-payment-requests--uuid_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-v1-payment-requests--uuid_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid_id"                data-endpoint="DELETEapi-v1-payment-requests--uuid_id-"
               value="0198b21f-6d55-73d3-8c88-a05996bba329"
               data-component="url">
    <br>
<p>The ID of the uuid. Example: <code>0198b21f-6d55-73d3-8c88-a05996bba329</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid"                data-endpoint="DELETEapi-v1-payment-requests--uuid_id-"
               value="f47ac10b-58cc-4372-a567-0e02b2c3d479"
               data-component="url">
    <br>
<p>The UUID of the payment request. Example: <code>f47ac10b-58cc-4372-a567-0e02b2c3d479</code></p>
            </div>
                    </form>

                    <h2 id="payment-requests-PUTapi-v1-payment-requests--uuid_id-">Replace Payment Request</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Completely replace the specified payment request (PUT method). All fields must be provided.
Users can only replace their own payment requests, and paid payment requests cannot be replaced.</p>

<span id="example-requests-PUTapi-v1-payment-requests--uuid_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: multipart/form-data" \
    --header "Accept: application/json" \
    --form "amount=250"\
    --form "currency_code=bng"\
    --form "purpose=Replaced lunch payment"\
    --form "description=Completely replaced payment for team lunch"\
    --form "is_negotiable=1"\
    --form "status=paid"\
    --form "expires_at=2025-11-15T12:00:00Z"\
    --form "negotiable="\
    --form "image=@/private/var/folders/bd/x67xzw0s72321v82bxt7z2_40000gp/T/phpec8lwR" </code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "multipart/form-data",
    "Accept": "application/json",
};

const body = new FormData();
body.append('amount', '250');
body.append('currency_code', 'bng');
body.append('purpose', 'Replaced lunch payment');
body.append('description', 'Completely replaced payment for team lunch');
body.append('is_negotiable', '1');
body.append('status', 'paid');
body.append('expires_at', '2025-11-15T12:00:00Z');
body.append('negotiable', '');
body.append('image', document.querySelector('input[name="image"]').files[0]);

fetch(url, {
    method: "PUT",
    headers,
    body,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-v1-payment-requests--uuid_id-">
            <blockquote>
            <p>Example response (200, Replaced successfully):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;paymentRequest&quot;,
        &quot;id&quot;: &quot;f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;,
        &quot;attributes&quot;: {
            &quot;amount&quot;: &quot;250.00&quot;,
            &quot;formattedAmount&quot;: &quot;GHS 250.00&quot;,
            &quot;currencyCode&quot;: &quot;GHS&quot;,
            &quot;purpose&quot;: &quot;Replaced lunch payment&quot;,
            &quot;description&quot;: &quot;Completely replaced payment for team lunch&quot;,
            &quot;isNegotiable&quot;: false,
            &quot;status&quot;: &quot;pending&quot;,
            &quot;expiresAt&quot;: &quot;2025-11-15T12:00:00Z&quot;,
            &quot;paidAt&quot;: null,
            &quot;isExpired&quot;: false,
            &quot;metadata&quot;: null,
            &quot;createdAt&quot;: &quot;2025-08-01T10:15:30Z&quot;,
            &quot;updatedAt&quot;: &quot;2025-08-15T12:30:15Z&quot;
        },
        &quot;relationships&quot;: {
            &quot;author&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;user&quot;,
                    &quot;id&quot;: 1
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;/api/v1/users/1&quot;
                }
            }
        },
        &quot;links&quot;: {
            &quot;self&quot;: &quot;/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Payment request not found&quot;,
    &quot;status&quot;: 404
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validation error):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Validation failed&quot;,
    &quot;errors&quot;: {
        &quot;amount&quot;: [
            &quot;The amount field is required.&quot;
        ],
        &quot;purpose&quot;: [
            &quot;The purpose field is required.&quot;
        ]
    },
    &quot;status&quot;: 422
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-v1-payment-requests--uuid_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-v1-payment-requests--uuid_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-v1-payment-requests--uuid_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-v1-payment-requests--uuid_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-v1-payment-requests--uuid_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-v1-payment-requests--uuid_id-" data-method="PUT"
      data-path="api/v1/payment-requests/{uuid_id}"
      data-authed="1"
      data-hasfiles="1"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-v1-payment-requests--uuid_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-v1-payment-requests--uuid_id-"
                    onclick="tryItOut('PUTapi-v1-payment-requests--uuid_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-v1-payment-requests--uuid_id-"
                    onclick="cancelTryOut('PUTapi-v1-payment-requests--uuid_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-v1-payment-requests--uuid_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/v1/payment-requests/{uuid_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="multipart/form-data"
               data-component="header">
    <br>
<p>Example: <code>multipart/form-data</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid_id"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="0198b21f-6d55-73d3-8c88-a05996bba329"
               data-component="url">
    <br>
<p>The ID of the uuid. Example: <code>0198b21f-6d55-73d3-8c88-a05996bba329</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="f47ac10b-58cc-4372-a567-0e02b2c3d479"
               data-component="url">
    <br>
<p>The UUID of the payment request. Example: <code>f47ac10b-58cc-4372-a567-0e02b2c3d479</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>numeric</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="amount"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="250"
               data-component="body">
    <br>
<p>The amount of the payment request. Example: <code>250</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>currency_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="currency_code"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="bng"
               data-component="body">
    <br>
<p>Must be 3 characters. Example: <code>bng</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>purpose</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="purpose"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="Replaced lunch payment"
               data-component="body">
    <br>
<p>The purpose of the payment request. Example: <code>Replaced lunch payment</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="Completely replaced payment for team lunch"
               data-component="body">
    <br>
<p>A longer description of the payment request. Example: <code>Completely replaced payment for team lunch</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>is_negotiable</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="PUTapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="is_negotiable"
                   value="true"
                   data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PUTapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="is_negotiable"
                   value="false"
                   data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>true</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="paid"
               data-component="body">
    <br>
<p>Example: <code>paid</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>pending</code></li> <li><code>paid</code></li> <li><code>cancelled</code></li> <li><code>expired</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>expires_at</code></b>&nbsp;&nbsp;
<small>datetime</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="expires_at"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value="2025-11-15T12:00:00Z"
               data-component="body">
    <br>
<p>The date and time when the payment request expires. Example: <code>2025-11-15T12:00:00Z</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>metadata</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="metadata"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value=""
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>image</code></b>&nbsp;&nbsp;
<small>file</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="file" style="display: none"
                              name="image"                data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
               value=""
               data-component="body">
    <br>
<p>Must be an image. Must not be greater than 2048 kilobytes. Example: <code>/private/var/folders/bd/x67xzw0s72321v82bxt7z2_40000gp/T/phpec8lwR</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>negotiable</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
 &nbsp;
                <label data-endpoint="PUTapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="negotiable"
                   value="true"
                   data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PUTapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="negotiable"
                   value="false"
                   data-endpoint="PUTapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether the amount is negotiable. Example: <code>false</code></p>
        </div>
        </form>

                    <h2 id="payment-requests-PATCHapi-v1-payment-requests--uuid_id-">Update Payment Request</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Partially update the specified payment request (PATCH method). Only provided fields will be updated.
Users can only update their own payment requests, and paid payment requests cannot be updated.</p>

<span id="example-requests-PATCHapi-v1-payment-requests--uuid_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PATCH \
    "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"amount\": \"200\",
    \"currency_code\": \"bng\",
    \"purpose\": \"Updated lunch payment\",
    \"description\": \"Updated payment for team lunch\",
    \"is_negotiable\": false,
    \"status\": \"pending\",
    \"expires_at\": \"2025-10-15T12:00:00Z\",
    \"metadata\": {
        \"restaurant\": \"Updated Cafe\",
        \"receipt_number\": \"RCT-67890\"
    },
    \"remove_image\": true,
    \"negotiable\": true
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "amount": "200",
    "currency_code": "bng",
    "purpose": "Updated lunch payment",
    "description": "Updated payment for team lunch",
    "is_negotiable": false,
    "status": "pending",
    "expires_at": "2025-10-15T12:00:00Z",
    "metadata": {
        "restaurant": "Updated Cafe",
        "receipt_number": "RCT-67890"
    },
    "remove_image": true,
    "negotiable": true
};

fetch(url, {
    method: "PATCH",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PATCHapi-v1-payment-requests--uuid_id-">
            <blockquote>
            <p>Example response (200, Updated successfully):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;paymentRequest&quot;,
        &quot;id&quot;: &quot;f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;,
        &quot;attributes&quot;: {
            &quot;amount&quot;: &quot;200.00&quot;,
            &quot;formattedAmount&quot;: &quot;GHS 200.00&quot;,
            &quot;currencyCode&quot;: &quot;GHS&quot;,
            &quot;purpose&quot;: &quot;Updated lunch payment&quot;,
            &quot;description&quot;: &quot;Updated payment for team lunch&quot;,
            &quot;isNegotiable&quot;: true,
            &quot;status&quot;: &quot;pending&quot;,
            &quot;expiresAt&quot;: &quot;2025-10-15T12:00:00Z&quot;,
            &quot;paidAt&quot;: null,
            &quot;isExpired&quot;: false,
            &quot;metadata&quot;: {
                &quot;restaurant&quot;: &quot;Updated Cafe&quot;,
                &quot;receipt_number&quot;: &quot;RCT-67890&quot;
            },
            &quot;createdAt&quot;: &quot;2025-08-01T10:15:30Z&quot;,
            &quot;updatedAt&quot;: &quot;2025-08-15T11:20:45Z&quot;
        },
        &quot;relationships&quot;: {
            &quot;author&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;user&quot;,
                    &quot;id&quot;: 1
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;/api/v1/users/1&quot;
                }
            }
        },
        &quot;links&quot;: {
            &quot;self&quot;: &quot;/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401, Unauthenticated):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;,
    &quot;status&quot;: 401
}</code>
 </pre>
            <blockquote>
            <p>Example response (403, Paid payment request):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Cannot update a paid payment request&quot;,
    &quot;status&quot;: 403
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Payment request not found&quot;,
    &quot;status&quot;: 404
}</code>
 </pre>
    </span>
<span id="execution-results-PATCHapi-v1-payment-requests--uuid_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PATCHapi-v1-payment-requests--uuid_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PATCHapi-v1-payment-requests--uuid_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PATCHapi-v1-payment-requests--uuid_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PATCHapi-v1-payment-requests--uuid_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PATCHapi-v1-payment-requests--uuid_id-" data-method="PATCH"
      data-path="api/v1/payment-requests/{uuid_id}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PATCHapi-v1-payment-requests--uuid_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PATCHapi-v1-payment-requests--uuid_id-"
                    onclick="tryItOut('PATCHapi-v1-payment-requests--uuid_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PATCHapi-v1-payment-requests--uuid_id-"
                    onclick="cancelTryOut('PATCHapi-v1-payment-requests--uuid_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PATCHapi-v1-payment-requests--uuid_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/v1/payment-requests/{uuid_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid_id"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="0198b21f-6d55-73d3-8c88-a05996bba329"
               data-component="url">
    <br>
<p>The ID of the uuid. Example: <code>0198b21f-6d55-73d3-8c88-a05996bba329</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="f47ac10b-58cc-4372-a567-0e02b2c3d479"
               data-component="url">
    <br>
<p>The UUID of the payment request. Example: <code>f47ac10b-58cc-4372-a567-0e02b2c3d479</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>numeric</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="amount"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="200"
               data-component="body">
    <br>
<p>optional The amount of the payment request. Example: <code>200</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>currency_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="currency_code"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="bng"
               data-component="body">
    <br>
<p>Must be 3 characters. Example: <code>bng</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>purpose</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="purpose"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="Updated lunch payment"
               data-component="body">
    <br>
<p>optional The purpose of the payment request. Example: <code>Updated lunch payment</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>description</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="description"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="Updated payment for team lunch"
               data-component="body">
    <br>
<p>optional A longer description of the payment request. Example: <code>Updated payment for team lunch</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>is_negotiable</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="PATCHapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="is_negotiable"
                   value="true"
                   data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PATCHapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="is_negotiable"
                   value="false"
                   data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Example: <code>false</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="pending"
               data-component="body">
    <br>
<p>Example: <code>pending</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>pending</code></li> <li><code>paid</code></li> <li><code>cancelled</code></li> <li><code>expired</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>expires_at</code></b>&nbsp;&nbsp;
<small>datetime</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="expires_at"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value="2025-10-15T12:00:00Z"
               data-component="body">
    <br>
<p>optional The date and time when the payment request expires. Example: <code>2025-10-15T12:00:00Z</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>metadata</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="metadata"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value=""
               data-component="body">
    <br>
<p>optional Additional metadata for the payment request.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>image</code></b>&nbsp;&nbsp;
<small>file</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="file" style="display: none"
                              name="image"                data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
               value=""
               data-component="body">
    <br>
<p>optional A new image to attach to the payment request.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>remove_image</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="PATCHapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="remove_image"
                   value="true"
                   data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PATCHapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="remove_image"
                   value="false"
                   data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>optional Whether to remove the existing image. Example: <code>true</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>negotiable</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="PATCHapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="negotiable"
                   value="true"
                   data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="PATCHapi-v1-payment-requests--uuid_id-" style="display: none">
            <input type="radio" name="negotiable"
                   value="false"
                   data-endpoint="PATCHapi-v1-payment-requests--uuid_id-"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>optional Whether the amount is negotiable. Example: <code>true</code></p>
        </div>
        </form>

                    <h2 id="payment-requests-POSTapi-v1-payment-requests--uuid_id--settle">Settle a payment request</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Initiate payment for a specific payment request. This creates a payment record
and initiates the payment process with the selected payment provider.</p>

<span id="example-requests-POSTapi-v1-payment-requests--uuid_id--settle">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329/settle" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"amount\": \"120.50\",
    \"payment_method\": \"mobile_money\",
    \"phone_number\": \"+233201234567\",
    \"account_number\": \"1234567890\",
    \"callback_url\": \"https:\\/\\/myapp.com\\/payment\\/callback\",
    \"metadata\": {
        \"note\": \"Payment for lunch\"
    }
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/payment-requests/0198b21f-6d55-73d3-8c88-a05996bba329/settle"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "amount": "120.50",
    "payment_method": "mobile_money",
    "phone_number": "+233201234567",
    "account_number": "1234567890",
    "callback_url": "https:\/\/myapp.com\/payment\/callback",
    "metadata": {
        "note": "Payment for lunch"
    }
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-payment-requests--uuid_id--settle">
            <blockquote>
            <p>Example response (200, Settlement initiated successfully):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;type&quot;: &quot;payment&quot;,
        &quot;id&quot;: &quot;9d2f8e1a-5c3b-4a7d-8f9e-1a2b3c4d5e6f&quot;,
        &quot;attributes&quot;: {
            &quot;amount&quot;: &quot;150.00&quot;,
            &quot;formattedAmount&quot;: &quot;GHS 150.00&quot;,
            &quot;currencyCode&quot;: &quot;GHS&quot;,
            &quot;status&quot;: &quot;processing&quot;,
            &quot;paymentMethod&quot;: &quot;mobile_money&quot;,
            &quot;phoneNumber&quot;: &quot;+233201234567&quot;,
            &quot;provider&quot;: &quot;dummy&quot;,
            &quot;providerReference&quot;: &quot;DUMMY_abc123_1692179400&quot;,
            &quot;authorizationUrl&quot;: &quot;https://dummy-payment.test/pay?reference=DUMMY_abc123_1692179400&amp;payment_id=9d2f8e1a-5c3b-4a7d-8f9e-1a2b3c4d5e6f&quot;,
            &quot;accessCode&quot;: &quot;dummy_access_92179400&quot;,
            &quot;isCompleted&quot;: false,
            &quot;isFailed&quot;: false,
            &quot;isPending&quot;: true,
            &quot;initiatedAt&quot;: &quot;2025-08-16T08:30:00Z&quot;,
            &quot;createdAt&quot;: &quot;2025-08-16T08:30:00Z&quot;,
            &quot;updatedAt&quot;: &quot;2025-08-16T08:30:00Z&quot;
        },
        &quot;relationships&quot;: {
            &quot;user&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;user&quot;,
                    &quot;id&quot;: 2
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;/api/v1/users/2&quot;
                }
            },
            &quot;paymentRequest&quot;: {
                &quot;data&quot;: {
                    &quot;type&quot;: &quot;paymentRequest&quot;,
                    &quot;id&quot;: &quot;f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;
                },
                &quot;links&quot;: {
                    &quot;self&quot;: &quot;/api/v1/payment-requests/f47ac10b-58cc-4372-a567-0e02b2c3d479&quot;
                }
            }
        },
        &quot;links&quot;: {
            &quot;self&quot;: &quot;/api/v1/payments/9d2f8e1a-5c3b-4a7d-8f9e-1a2b3c4d5e6f&quot;
        }
    },
    &quot;message&quot;: &quot;Payment settlement initiated successfully&quot;,
    &quot;status&quot;: 200
}</code>
 </pre>
            <blockquote>
            <p>Example response (403, Cannot settle own payment request):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;You cannot pay your own payment request&quot;,
    &quot;status&quot;: 403
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Payment request not found):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Payment request not found&quot;,
    &quot;status&quot;: 404
}</code>
 </pre>
            <blockquote>
            <p>Example response (410, Payment request expired):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Payment request has expired&quot;,
    &quot;status&quot;: 410
}</code>
 </pre>
            <blockquote>
            <p>Example response (422, Validation error):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Validation failed&quot;,
    &quot;errors&quot;: {
        &quot;payment_method&quot;: [
            &quot;Payment method is required.&quot;
        ],
        &quot;phone_number&quot;: [
            &quot;Phone number is required for mobile money payments.&quot;
        ]
    },
    &quot;status&quot;: 422
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-payment-requests--uuid_id--settle" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-payment-requests--uuid_id--settle"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-payment-requests--uuid_id--settle"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-payment-requests--uuid_id--settle" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-payment-requests--uuid_id--settle">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-payment-requests--uuid_id--settle" data-method="POST"
      data-path="api/v1/payment-requests/{uuid_id}/settle"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-payment-requests--uuid_id--settle', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-payment-requests--uuid_id--settle"
                    onclick="tryItOut('POSTapi-v1-payment-requests--uuid_id--settle');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-payment-requests--uuid_id--settle"
                    onclick="cancelTryOut('POSTapi-v1-payment-requests--uuid_id--settle');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-payment-requests--uuid_id--settle"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/payment-requests/{uuid_id}/settle</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid_id"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="0198b21f-6d55-73d3-8c88-a05996bba329"
               data-component="url">
    <br>
<p>The ID of the uuid. Example: <code>0198b21f-6d55-73d3-8c88-a05996bba329</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="f47ac10b-58cc-4372-a567-0e02b2c3d479"
               data-component="url">
    <br>
<p>The UUID of the payment request to settle. Example: <code>f47ac10b-58cc-4372-a567-0e02b2c3d479</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>numeric</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="amount"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="120.50"
               data-component="body">
    <br>
<p>optional Custom amount for negotiable payment requests. Example: <code>120.50</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_method</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="payment_method"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="mobile_money"
               data-component="body">
    <br>
<p>The payment method to use. Example: <code>mobile_money</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone_number"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="+233201234567"
               data-component="body">
    <br>
<p>The phone number for mobile money payments. Example: <code>+233201234567</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>account_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="account_number"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="1234567890"
               data-component="body">
    <br>
<p>optional Account number for bank transfers. Example: <code>1234567890</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>callback_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="callback_url"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value="https://myapp.com/payment/callback"
               data-component="body">
    <br>
<p>optional URL to redirect to after payment. Example: <code>https://myapp.com/payment/callback</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>metadata</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="metadata"                data-endpoint="POSTapi-v1-payment-requests--uuid_id--settle"
               value=""
               data-component="body">
    <br>
<p>optional Additional payment metadata.</p>
        </div>
        </form>

                <h1 id="payment-webhooks">Payment Webhooks</h1>

    

                                <h2 id="payment-webhooks-POSTapi-v1-webhooks-payment--provider-">Handle payment webhook from providers.</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>This endpoint receives webhook notifications from payment providers
when payment status changes occur.</p>

<span id="example-requests-POSTapi-v1-webhooks-payment--provider-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/webhooks/payment/dummy" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/webhooks/payment/dummy"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-webhooks-payment--provider-">
            <blockquote>
            <p>Example response (200, Webhook processed successfully):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;message&quot;: &quot;Webhook processed successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Invalid webhook data):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Invalid webhook data&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Unknown provider):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Unknown payment provider&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-webhooks-payment--provider-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-webhooks-payment--provider-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-webhooks-payment--provider-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-webhooks-payment--provider-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-webhooks-payment--provider-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-webhooks-payment--provider-" data-method="POST"
      data-path="api/v1/webhooks/payment/{provider}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-webhooks-payment--provider-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-webhooks-payment--provider-"
                    onclick="tryItOut('POSTapi-v1-webhooks-payment--provider-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-webhooks-payment--provider-"
                    onclick="cancelTryOut('POSTapi-v1-webhooks-payment--provider-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-webhooks-payment--provider-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/webhooks/payment/{provider}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-webhooks-payment--provider-"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-webhooks-payment--provider-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-webhooks-payment--provider-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>provider</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="provider"                data-endpoint="POSTapi-v1-webhooks-payment--provider-"
               value="dummy"
               data-component="url">
    <br>
<p>The payment provider name (dummy, paystack, flutterwave, etc.). Example: <code>dummy</code></p>
            </div>
                    </form>

                    <h2 id="payment-webhooks-POSTapi-v1-webhooks-payment--provider---event-">Handle specific webhook events (if providers support it).</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Some providers might send different webhook URLs for different events.
This method handles event-specific webhooks.</p>

<span id="example-requests-POSTapi-v1-webhooks-payment--provider---event-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/webhooks/payment/paystack/payment.success" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/webhooks/payment/paystack/payment.success"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-webhooks-payment--provider---event-">
            <blockquote>
            <p>Example response (200, Event webhook processed successfully):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;success&quot;,
    &quot;message&quot;: &quot;Webhook processed successfully&quot;,
    &quot;payment_id&quot;: &quot;9d2f8e1a-5c3b-4a7d-8f9e-1a2b3c4d5e6f&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400, Invalid webhook data):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Invalid webhook data&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404, Unknown provider):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Unknown payment provider&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-v1-webhooks-payment--provider---event-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-webhooks-payment--provider---event-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-webhooks-payment--provider---event-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-webhooks-payment--provider---event-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-webhooks-payment--provider---event-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-webhooks-payment--provider---event-" data-method="POST"
      data-path="api/v1/webhooks/payment/{provider}/{event}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-webhooks-payment--provider---event-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-webhooks-payment--provider---event-"
                    onclick="tryItOut('POSTapi-v1-webhooks-payment--provider---event-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-webhooks-payment--provider---event-"
                    onclick="cancelTryOut('POSTapi-v1-webhooks-payment--provider---event-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-webhooks-payment--provider---event-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/webhooks/payment/{provider}/{event}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-webhooks-payment--provider---event-"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-webhooks-payment--provider---event-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-webhooks-payment--provider---event-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>provider</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="provider"                data-endpoint="POSTapi-v1-webhooks-payment--provider---event-"
               value="paystack"
               data-component="url">
    <br>
<p>The payment provider name. Example: <code>paystack</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>event</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="event"                data-endpoint="POSTapi-v1-webhooks-payment--provider---event-"
               value="payment.success"
               data-component="url">
    <br>
<p>The event type (payment.success, payment.failed, etc.). Example: <code>payment.success</code></p>
            </div>
                    </form>

                    <h2 id="payment-webhooks-POSTapi-v1-webhooks-payment--provider--test">Test webhook endpoint for development.</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>This endpoint can be used to test webhook processing during development.</p>

<span id="example-requests-POSTapi-v1-webhooks-payment--provider--test">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/v1/webhooks/payment/dummy/test" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"reference\": \"DUMMY_123_456\",
    \"status\": \"success\",
    \"amount\": \"100.50\",
    \"currency\": \"GHS\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/webhooks/payment/dummy/test"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "reference": "DUMMY_123_456",
    "status": "success",
    "amount": "100.50",
    "currency": "GHS"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-v1-webhooks-payment--provider--test">
</span>
<span id="execution-results-POSTapi-v1-webhooks-payment--provider--test" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-v1-webhooks-payment--provider--test"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-v1-webhooks-payment--provider--test"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-v1-webhooks-payment--provider--test" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-v1-webhooks-payment--provider--test">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-v1-webhooks-payment--provider--test" data-method="POST"
      data-path="api/v1/webhooks/payment/{provider}/test"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-v1-webhooks-payment--provider--test', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-v1-webhooks-payment--provider--test"
                    onclick="tryItOut('POSTapi-v1-webhooks-payment--provider--test');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-v1-webhooks-payment--provider--test"
                    onclick="cancelTryOut('POSTapi-v1-webhooks-payment--provider--test');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-v1-webhooks-payment--provider--test"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/v1/webhooks/payment/{provider}/test</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="POSTapi-v1-webhooks-payment--provider--test"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-v1-webhooks-payment--provider--test"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-v1-webhooks-payment--provider--test"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>provider</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="provider"                data-endpoint="POSTapi-v1-webhooks-payment--provider--test"
               value="dummy"
               data-component="url">
    <br>
<p>The payment provider name. Example: <code>dummy</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>reference</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="reference"                data-endpoint="POSTapi-v1-webhooks-payment--provider--test"
               value="DUMMY_123_456"
               data-component="body">
    <br>
<p>The payment reference to test. Example: <code>DUMMY_123_456</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="POSTapi-v1-webhooks-payment--provider--test"
               value="success"
               data-component="body">
    <br>
<p>The payment status to simulate. Example: <code>success</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>amount</code></b>&nbsp;&nbsp;
<small>numeric</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="amount"                data-endpoint="POSTapi-v1-webhooks-payment--provider--test"
               value="100.50"
               data-component="body">
    <br>
<p>The payment amount. Example: <code>100.50</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>currency</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="currency"                data-endpoint="POSTapi-v1-webhooks-payment--provider--test"
               value="GHS"
               data-component="body">
    <br>
<p>The currency code. Example: <code>GHS</code></p>
        </div>
        </form>

                <h1 id="user-profile">User Profile</h1>

    

                                <h2 id="user-profile-GETapi-v1-users--uuid-">Get user by UUID (JSON:API endpoint)</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>



<span id="example-requests-GETapi-v1-users--uuid-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/v1/users/f47ac10b-58cc-4372-a567-0e02b2c3d479" \
    --header "Authorization: Bearer Bearer {YOUR_TOKEN}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/v1/users/f47ac10b-58cc-4372-a567-0e02b2c3d479"
);

const headers = {
    "Authorization": "Bearer Bearer {YOUR_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-v1-users--uuid-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;id&quot;: 1,
    &quot;mobile_number&quot;: &quot;233244123456&quot;,
    &quot;first_name&quot;: &quot;John&quot;,
    &quot;surname&quot;: &quot;Doe&quot;,
    &quot;full_name&quot;: &quot;John Doe&quot;,
    &quot;payment_accounts&quot;: [],
    &quot;active_device_sessions&quot;: []
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-v1-users--uuid-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-v1-users--uuid-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-v1-users--uuid-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-v1-users--uuid-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-v1-users--uuid-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-v1-users--uuid-" data-method="GET"
      data-path="api/v1/users/{uuid}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-v1-users--uuid-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-v1-users--uuid-"
                    onclick="tryItOut('GETapi-v1-users--uuid-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-v1-users--uuid-"
                    onclick="cancelTryOut('GETapi-v1-users--uuid-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-v1-users--uuid-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/v1/users/{uuid}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Authorization</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Authorization" class="auth-value"               data-endpoint="GETapi-v1-users--uuid-"
               value="Bearer Bearer {YOUR_TOKEN}"
               data-component="header">
    <br>
<p>Example: <code>Bearer Bearer {YOUR_TOKEN}</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-v1-users--uuid-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-v1-users--uuid-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>uuid</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="uuid"                data-endpoint="GETapi-v1-users--uuid-"
               value="f47ac10b-58cc-4372-a567-0e02b2c3d479"
               data-component="url">
    <br>
<p>The UUID of the user. Currently returns authenticated user data. Example: <code>f47ac10b-58cc-4372-a567-0e02b2c3d479</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
