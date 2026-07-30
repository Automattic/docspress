---
title: Authenticate with an API key
sidebar_position: 2
---

Version 1 sends a long-lived API key with every request:

```http
X-API-Key: <api-key>
```

Upgrade to a newer API before rotating production credentials.
