---
title: API Request / Response
sidebar_position: 80
---

Use `docspress/api-request` to teach one HTTP exchange. It can remain a static request and example response, or become a guarded browser runner with editable inputs, live status, timing, headers, and response body.

## Choose static or runnable

Keep the block static when the endpoint needs credentials, modifies data, has restrictive cross-origin policy, or should return a deterministic teaching example. Enable `runnable` for safe public or same-origin endpoints where live feedback materially helps the reader.

Use `editable: false` to offer a fixed runnable request. Relative endpoints resolve against the current site unless `baseUrl` is supplied.

## Attributes

<!-- wp:docspress/fields {"title":"API Request / Response attributes","description":"Request, example response, and runner policy accepted by <code>docspress/api-request</code>.","fields":[{"name":"method","type":"enum","required":false,"defaultValue":"GET","description":"<p>HTTP request method.</p>","values":"GET, POST, PUT, PATCH, DELETE","deprecated":false},{"name":"endpoint","type":"string","required":true,"defaultValue":"/wp-json/wp/v2/pages","description":"<p>Relative or absolute request URL.</p>","values":"","deprecated":false},{"name":"headers","type":"string","required":false,"defaultValue":"Accept plus token placeholder","description":"<p>One <code>Name: value</code> header per line.</p>","values":"","deprecated":false},{"name":"requestBody","type":"string","required":false,"defaultValue":"","description":"<p>Example or runnable request body.</p>","values":"","deprecated":false},{"name":"requestBodyFormat","type":"enum","required":false,"defaultValue":"json","description":"<p>Request editor formatting.</p>","values":"json, raw","deprecated":false},{"name":"responseStatus","type":"string","required":false,"defaultValue":"200 OK","description":"<p>Static example status.</p>","values":"","deprecated":false},{"name":"responseBody","type":"string","required":false,"defaultValue":"Example JSON object","description":"<p>Static example response body.</p>","values":"","deprecated":false},{"name":"responseBodyFormat","type":"enum","required":false,"defaultValue":"json","description":"<p>Static response formatting.</p>","values":"json, raw","deprecated":false},{"name":"runnable","type":"boolean","required":false,"defaultValue":"false","description":"<p>Adds the guarded browser runner.</p>","values":"true, false","deprecated":false},{"name":"editable","type":"boolean","required":false,"defaultValue":"true","description":"<p>Lets readers change the runnable URL, headers, and body.</p>","values":"true, false","deprecated":false},{"name":"allowUnsafe","type":"boolean","required":false,"defaultValue":"false","description":"<p>Author opt-in for mutating methods; readers must still confirm each run.</p>","values":"true, false","deprecated":false},{"name":"baseUrl","type":"url","required":false,"defaultValue":"","description":"<p>Base used to resolve relative endpoints.</p>","values":"","deprecated":false},{"name":"allowedOrigins","type":"string","required":false,"defaultValue":"","description":"<p>Comma-separated exact origins permitted for cross-origin requests.</p>","values":"https://api.example.com","deprecated":false},{"name":"timeout","type":"number","required":false,"defaultValue":"10000","description":"<p>Runner timeout in milliseconds, clamped to 1000–30000.</p>","values":"1000–30000","deprecated":false}],"searchable":true,"compact":true} /-->

## Static example

<!-- wp:docspress/api-request {"method":"GET","endpoint":"/wp-json/wp/v2/pages?context=edit","headers":"Accept: application/json\nAuthorization: Bearer $WP_ACCESS_TOKEN","requestBody":"","requestBodyFormat":"json","responseStatus":"200 OK","responseBody":"[{\n  \"id\": 42,\n  \"slug\": \"docs\"\n}]","responseBodyFormat":"json"} /-->

## Runnable example

<!-- wp:docspress/api-request {"method":"GET","endpoint":"/wp-json/","headers":"Accept: application/json","requestBody":"","requestBodyFormat":"json","responseStatus":"200 OK","responseBody":"{\n  \"name\": \"WordPress\"\n}","responseBodyFormat":"json","runnable":true,"editable":true,"allowUnsafe":false,"baseUrl":"","allowedOrigins":"","timeout":10000} /-->

## Static validation-error example

<!-- wp:docspress/api-request {"method":"POST","endpoint":"/wp-json/wp/v2/pages","headers":"Accept: application/json\nContent-Type: application/json\nAuthorization: Bearer $WP_ACCESS_TOKEN","requestBody":"{\n  \"title\": \"\",\n  \"status\": \"publish\"\n}","requestBodyFormat":"json","responseStatus":"400 Bad Request","responseBody":"{\n  \"code\": \"rest_invalid_param\",\n  \"message\": \"Invalid parameter: title\",\n  \"data\": {\n    \"status\": 400\n  }\n}","responseBodyFormat":"json","runnable":false,"editable":false,"allowUnsafe":false,"baseUrl":"","allowedOrigins":"","timeout":10000} /-->

## Runner safety

The runner sends requests with browser credentials omitted. It blocks Cookie, Host, Origin, Content-Length, Proxy-Authorization, Proxy-Connection, and every `Sec-*` header. Same-origin requests are allowed by default; cross-origin requests require an exact `http` or `https` origin in `allowedOrigins`.

`POST`, `PUT`, `PATCH`, and `DELETE` remain disabled unless the author enables `allowUnsafe`. Even then, the reader must complete a two-step confirmation immediately before execution. Run can be cancelled, Reset restores the authored request, and Copy as cURL excludes blocked headers.

<!-- wp:docspress/callout {"tone":"danger","title":"Never publish a real credential","content":"<p>A runnable block executes in every reader’s browser. Use placeholders such as <code>$WP_ACCESS_TOKEN</code>, and keep privileged or destructive requests in a controlled development environment.</p>","collapsible":false,"open":true} /-->

## Live output and accessibility

Live results report status, elapsed time, byte count, response headers, and body. Long response bodies use a responsive 16–26rem scroll area with a visible keyboard focus target and the label “Scrollable API response body.” Run, Cancel, Reset, and Copy status changes are announced to assistive technology.

Test the exact published origin and its CORS behavior. A successful static example does not guarantee that a browser can run the same request.
