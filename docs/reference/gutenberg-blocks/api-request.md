---
title: API Request / Response
sidebar_position: 80
---

Use `docspress/api-request` to teach one HTTP exchange. It can remain a static request and example response, or become a guarded browser runner with editable inputs, live status, timing, headers, and response body.

## Choose static or runnable

Keep the block static when the endpoint needs credentials, modifies data, has restrictive cross-origin policy, or should return a deterministic teaching example. Enable `runnable` for safe public or same-origin endpoints where live feedback materially helps the reader.

Use `editable: false` to offer a fixed runnable request. Relative endpoints resolve against the current site unless `baseUrl` is supplied.

## Attributes

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/fields",
  "attrs": {
    "title": "API Request / Response attributes",
    "description": "Request, example response, and runner policy accepted by \u003ccode\u003edocspress/api-request\u003c/code\u003e.",
    "fields": [
      {
        "name": "method",
        "type": "enum",
        "required": false,
        "defaultValue": "GET",
        "description": "\u003cp\u003eHTTP request method.\u003c/p\u003e",
        "values": "GET, POST, PUT, PATCH, DELETE",
        "deprecated": false
      },
      {
        "name": "endpoint",
        "type": "string",
        "required": true,
        "defaultValue": "/wp-json/wp/v2/pages",
        "description": "\u003cp\u003eRelative or absolute request URL.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "headers",
        "type": "string",
        "required": false,
        "defaultValue": "Accept plus token placeholder",
        "description": "\u003cp\u003eOne \u003ccode\u003eName: value\u003c/code\u003e header per line.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "requestBody",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eExample or runnable request body.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "requestBodyFormat",
        "type": "enum",
        "required": false,
        "defaultValue": "json",
        "description": "\u003cp\u003eRequest editor formatting.\u003c/p\u003e",
        "values": "json, raw",
        "deprecated": false
      },
      {
        "name": "responseStatus",
        "type": "string",
        "required": false,
        "defaultValue": "200 OK",
        "description": "\u003cp\u003eStatic example status.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "responseBody",
        "type": "string",
        "required": false,
        "defaultValue": "Example JSON object",
        "description": "\u003cp\u003eStatic example response body.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "responseBodyFormat",
        "type": "enum",
        "required": false,
        "defaultValue": "json",
        "description": "\u003cp\u003eStatic response formatting.\u003c/p\u003e",
        "values": "json, raw",
        "deprecated": false
      },
      {
        "name": "runnable",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eAdds the guarded browser runner.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "editable",
        "type": "boolean",
        "required": false,
        "defaultValue": "true",
        "description": "\u003cp\u003eLets readers change the runnable URL, headers, and body.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "allowUnsafe",
        "type": "boolean",
        "required": false,
        "defaultValue": "false",
        "description": "\u003cp\u003eAuthor opt-in for mutating methods; readers must still confirm each run.\u003c/p\u003e",
        "values": "true, false",
        "deprecated": false
      },
      {
        "name": "baseUrl",
        "type": "url",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eBase used to resolve relative endpoints.\u003c/p\u003e",
        "values": "",
        "deprecated": false
      },
      {
        "name": "allowedOrigins",
        "type": "string",
        "required": false,
        "defaultValue": "",
        "description": "\u003cp\u003eComma-separated exact origins permitted for cross-origin requests.\u003c/p\u003e",
        "values": "https://api.example.com",
        "deprecated": false
      },
      {
        "name": "timeout",
        "type": "number",
        "required": false,
        "defaultValue": "10000",
        "description": "\u003cp\u003eRunner timeout in milliseconds, clamped to 1000–30000.\u003c/p\u003e",
        "values": "1000–30000",
        "deprecated": false
      }
    ],
    "searchable": true,
    "compact": true
  }
}
-->
#### API Request / Response attributes

Request, example response, and runner policy accepted by <code>docspress/api-request</code>.

| Field | Type | Required | Default | Description |
| --- | --- | --- | --- | --- |
| `method` | enum | No | GET | <p>HTTP request method.</p> |
| `endpoint` | string | Yes | /wp-json/wp/v2/pages | <p>Relative or absolute request URL.</p> |
| `headers` | string | No | Accept plus token placeholder | <p>One <code>Name: value</code> header per line.</p> |
| `requestBody` | string | No |  | <p>Example or runnable request body.</p> |
| `requestBodyFormat` | enum | No | json | <p>Request editor formatting.</p> |
| `responseStatus` | string | No | 200 OK | <p>Static example status.</p> |
| `responseBody` | string | No | Example JSON object | <p>Static example response body.</p> |
| `responseBodyFormat` | enum | No | json | <p>Static response formatting.</p> |
| `runnable` | boolean | No | false | <p>Adds the guarded browser runner.</p> |
| `editable` | boolean | No | true | <p>Lets readers change the runnable URL, headers, and body.</p> |
| `allowUnsafe` | boolean | No | false | <p>Author opt-in for mutating methods; readers must still confirm each run.</p> |
| `baseUrl` | url | No |  | <p>Base used to resolve relative endpoints.</p> |
| `allowedOrigins` | string | No |  | <p>Comma-separated exact origins permitted for cross-origin requests.</p> |
| `timeout` | number | No | 10000 | <p>Runner timeout in milliseconds, clamped to 1000–30000.</p> |
<!-- /docspress:block -->

## Static example

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/api-request",
  "attrs": {
    "method": "GET",
    "endpoint": "/wp-json/wp/v2/pages?context=edit",
    "headers": "Accept: application/json\nAuthorization: Bearer $WP_ACCESS_TOKEN",
    "requestBody": "",
    "requestBodyFormat": "json",
    "responseStatus": "200 OK",
    "responseBody": "[{\n  \"id\": 42,\n  \"slug\": \"docs\"\n}]",
    "responseBodyFormat": "json"
  }
}
-->
#### GET /wp-json/wp/v2/pages?context=edit

**Request headers**

```http
Accept: application/json
Authorization: Bearer $WP_ACCESS_TOKEN
```

**Response: 200 OK**

```json
[{
  "id": 42,
  "slug": "docs"
}]
```
<!-- /docspress:block -->

## Runnable example

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/api-request",
  "attrs": {
    "method": "GET",
    "endpoint": "/wp-json/",
    "headers": "Accept: application/json",
    "requestBody": "",
    "requestBodyFormat": "json",
    "responseStatus": "200 OK",
    "responseBody": "{\n  \"name\": \"WordPress\"\n}",
    "responseBodyFormat": "json",
    "runnable": true,
    "editable": true,
    "allowUnsafe": false,
    "baseUrl": "",
    "allowedOrigins": "",
    "timeout": 10000
  }
}
-->
#### GET /wp-json/

**Request headers**

```http
Accept: application/json
```

**Response: 200 OK**

```json
{
  "name": "WordPress"
}
```
<!-- /docspress:block -->

## Static validation-error example

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/api-request",
  "attrs": {
    "method": "POST",
    "endpoint": "/wp-json/wp/v2/pages",
    "headers": "Accept: application/json\nContent-Type: application/json\nAuthorization: Bearer $WP_ACCESS_TOKEN",
    "requestBody": "{\n  \"title\": \"\",\n  \"status\": \"publish\"\n}",
    "requestBodyFormat": "json",
    "responseStatus": "400 Bad Request",
    "responseBody": "{\n  \"code\": \"rest_invalid_param\",\n  \"message\": \"Invalid parameter: title\",\n  \"data\": {\n    \"status\": 400\n  }\n}",
    "responseBodyFormat": "json",
    "runnable": false,
    "editable": false,
    "allowUnsafe": false,
    "baseUrl": "",
    "allowedOrigins": "",
    "timeout": 10000
  }
}
-->
#### POST /wp-json/wp/v2/pages

**Request headers**

```http
Accept: application/json
Content-Type: application/json
Authorization: Bearer $WP_ACCESS_TOKEN
```

**Request body**

```json
{
  "title": "",
  "status": "publish"
}
```

**Response: 400 Bad Request**

```json
{
  "code": "rest_invalid_param",
  "message": "Invalid parameter: title",
  "data": {
    "status": 400
  }
}
```
<!-- /docspress:block -->

## Runner safety

The runner sends requests with browser credentials omitted. It blocks Cookie, Host, Origin, Content-Length, Proxy-Authorization, Proxy-Connection, and every `Sec-*` header. Same-origin requests are allowed by default; cross-origin requests require an exact `http` or `https` origin in `allowedOrigins`.

`POST`, `PUT`, `PATCH`, and `DELETE` remain disabled unless the author enables `allowUnsafe`. Even then, the reader must complete a two-step confirmation immediately before execution. Run can be cancelled, Reset restores the authored request, and Copy as cURL excludes blocked headers.

<!-- docspress:block
{
  "version": 1,
  "name": "docspress/callout",
  "attrs": {
    "tone": "danger",
    "title": "Never publish a real credential",
    "content": "\u003cp\u003eA runnable block executes in every reader’s browser. Use placeholders such as \u003ccode\u003e$WP_ACCESS_TOKEN\u003c/code\u003e, and keep privileged or destructive requests in a controlled development environment.\u003c/p\u003e",
    "collapsible": false,
    "open": true
  }
}
-->
> [!CAUTION]
>
> **Never publish a real credential**
>
> A runnable block executes in every reader’s browser. Use placeholders such as `$WP_ACCESS_TOKEN`, and keep privileged or destructive requests in a controlled development environment.
<!-- /docspress:block -->

## Live output and accessibility

Live results report status, elapsed time, byte count, response headers, and body. Long response bodies use a responsive 16–26rem scroll area with a visible keyboard focus target and the label “Scrollable API response body.” Run, Cancel, Reset, and Copy status changes are announced to assistive technology.

Test the exact published origin and its CORS behavior. A successful static example does not guarantee that a browser can run the same request.
