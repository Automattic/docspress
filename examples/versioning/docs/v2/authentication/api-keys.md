---
title: Authenticate with an API key
sidebar_position: 2
---

Version 2 accepts an API key in the request header:

```http
X-API-Key: <api-key>
```

The key flow is not available in version 3, so switching to v3 demonstrates the version-root fallback.
